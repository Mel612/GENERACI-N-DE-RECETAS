<?php

namespace App\Http\Controllers;

use App\Exceptions\IngredienteNoComestible;
use App\Models\Bitacora;
use App\Models\CategoriaIngrediente;
use App\Models\Ingrediente;
use App\Models\Inventario;
use App\Models\SalidaInventario;
use App\Models\UnidadMedida;
use App\Services\AsistenteIA;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

/**
 * Inventario del usuario: su despensa (Historia 3, Requerimientos 4, 11 y 12).
 *
 * Cada persona ve y edita únicamente la suya. Todas las consultas filtran por
 * el usuario de la sesión, y además cada acción comprueba la propiedad de la
 * fila antes de tocarla: sin eso, cambiar el número en la URL dejaría editar
 * la despensa de otro.
 */
class InventarioController extends Controller
{
    public function __construct(
        private AsistenteIA $asistente,
    ) {}

    public function index(Request $request): View
    {
        $consulta = Inventario::with(['ingrediente.categoria', 'unidad'])
            ->where('cod_usuario_inv', Auth::id());

        if ($request->filled('buscar')) {
            $texto = '%' . mb_strtolower($request->string('buscar')->toString()) . '%';
            $consulta->whereHas('ingrediente', fn ($q) => $q
                ->whereRaw('LOWER(nom_ing) LIKE ?', [$texto])
                ->orWhereRaw('LOWER(COALESCE(sin_ing, \'\')) LIKE ?', [$texto]));
        }

        if ($request->filled('categoria')) {
            $consulta->whereHas('ingrediente', fn ($q) =>
                $q->where('cod_categoria_ingrediente_ing', $request->integer('categoria')));
        }

        match ($request->input('estado')) {
            'por_caducar' => $consulta->porCaducar(),
            'vencidos'    => $consulta->vencidos(),
            'sin_fecha'   => $consulta->whereNull('fec_cad_inv'),
            default       => null,
        };

        $mios = Inventario::where('cod_usuario_inv', Auth::id());

        // Lo vencido que todavía tiene cantidad espera que la persona diga
        // qué pasó con ello. Los días se cuentan en la base, en hora de Bolivia.
        $porResolver = (clone $mios)->with('ingrediente', 'unidad')
            ->vencidos()->where('can_inv', '>', 0)
            ->select('inventario.*')
            ->selectRaw('CURRENT_DATE - fec_cad_inv AS dias_vencido')
            ->orderBy('fec_cad_inv')
            ->get();

        return view('inventario.index', [
            'inventario' => $consulta->get()->sortBy(fn ($i) => $i->ingrediente->nom_ing),
            'porResolver' => $porResolver,
            'hoy'        => Carbon::parse(DB::selectOne('SELECT CURRENT_DATE AS hoy')->hoy),
            'categorias' => CategoriaIngrediente::where('act_cat', true)->orderBy('nom_cat')->get(),
            'resumen'    => [
                'total'       => (clone $mios)->disponibles()->count(),
                'por_caducar' => (clone $mios)->disponibles()->porCaducar()->count(),
                'vencidos'    => (clone $mios)->vencidos()->count(),
                // Todas las filas, usables o no: si solo hay vencidos, la
                // despensa no está vacía
                'filas'       => (clone $mios)->count(),
            ],
            // Lo que todavía no tiene en la despensa, para el formulario de alta
            'disponibles' => Ingrediente::with('categoria')->activos()
                ->whereNotIn('cod_ingrediente', (clone $mios)->pluck('cod_ingrediente_inv'))
                ->orderBy('nom_ing')->get(),
            'unidades'    => UnidadMedida::where('act_uni', true)
                ->orderBy('tip_uni')->orderBy('nom_uni')->get(),
            'iaDisponible' => $this->asistente->disponible(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cod_ingrediente_inv' => [
                'required', 'integer', 'exists:ingredientes,cod_ingrediente',
                // La base tiene UNIQUE(usuario, ingrediente): se avisa antes de chocar con ella
                Rule::unique('inventario', 'cod_ingrediente_inv')
                    ->where('cod_usuario_inv', Auth::id()),
            ],
            'cod_unidad_inv' => ['required', 'integer', 'exists:unidades_medida,cod_unidad'],
            'can_inv'        => ['required', 'numeric', 'gt:0', 'max:99999'],
            'fec_cad_inv'    => ['nullable', 'date', 'after_or_equal:today'],
            'not_inv'        => ['nullable', 'string', 'max:300'],
        ], [
            'cod_ingrediente_inv.required' => 'Elige un ingrediente.',
            'cod_ingrediente_inv.unique'   => 'Ese ingrediente ya está en tu despensa. Edita la cantidad en lugar de agregarlo otra vez.',
            'cod_unidad_inv.required'      => 'Elige una unidad de medida.',
            'can_inv.required'             => 'Indica cuánto tienes.',
            'can_inv.gt'                   => 'La cantidad debe ser mayor que cero.',
            'fec_cad_inv.after_or_equal'   => 'La fecha de caducidad no puede estar en el pasado.',
        ]);

        $fila = Inventario::create($datos + ['cod_usuario_inv' => Auth::id(), 'act_inv' => true]);

        Bitacora::registrar('agregar_inventario', 'inventario', $fila->cod_inventario, [
            'ingrediente' => $fila->ingrediente->nom_ing,
        ]);

        return back()->with('exito', "«{$fila->ingrediente->nom_ing}» se agregó a tu despensa.");
    }

    /**
     * Reconoce con la IA un ingrediente que no está en el catálogo.
     *
     * Solo lo da de alta si es comestible; lo que no se puede comer se
     * rechaza con un mensaje. Devuelve el ingrediente para que el buscador
     * lo deje elegido y la persona complete cantidad y unidad.
     */
    public function identificarConIa(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'texto' => ['required', 'string', 'max:60', 'regex:' . AsistenteIA::PATRON_NOMBRE],
        ], [
            'texto.required' => 'Escribe el nombre del ingrediente.',
            'texto.regex'    => 'Usa solo letras y espacios en el nombre del ingrediente.',
        ]);

        try {
            ['ingrediente' => $ingrediente, 'nuevo' => $nuevo] =
                $this->asistente->identificarIngrediente(Auth::user(), $datos['texto']);
        } catch (IngredienteNoComestible $e) {
            return response()->json(['rechazado' => true, 'mensaje' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            return response()->json(['mensaje' => $e->getMessage()], 422);
        }

        $yaLoTiene = Inventario::where('cod_usuario_inv', Auth::id())
            ->where('cod_ingrediente_inv', $ingrediente->cod_ingrediente)->exists();

        if ($yaLoTiene) {
            return response()->json([
                'mensaje' => "«{$ingrediente->nom_ing}» ya está en tu despensa. Edita la cantidad en lugar de agregarlo otra vez.",
            ], 422);
        }

        return response()->json([
            'id'        => $ingrediente->cod_ingrediente,
            'nombre'    => $ingrediente->nom_ing,
            'categoria' => $ingrediente->categoria->nom_cat,
            'nuevo'     => $nuevo,
        ]);
    }

    public function update(Request $request, Inventario $inventario): RedirectResponse
    {
        $this->comprobarPropiedad($inventario);

        $datos = $request->validate([
            'cod_unidad_inv' => ['required', 'integer', 'exists:unidades_medida,cod_unidad'],
            'can_inv'        => ['required', 'numeric', 'min:0', 'max:99999'],
            'fec_cad_inv'    => ['nullable', 'date'],
            'not_inv'        => ['nullable', 'string', 'max:300'],
        ], [
            'can_inv.min' => 'La cantidad no puede ser negativa. Usa cero si se te acabó.',
        ]);

        $inventario->update($datos + [
            // Cantidad cero equivale a no tenerlo: deja de contar para la generación
            'act_inv'     => (float) $datos['can_inv'] > 0,
            'fec_act_inv' => now(),
        ]);

        return back()->with('exito', 'Despensa actualizada.');
    }

    /**
     * Saca un ingrediente de la despensa diciendo por qué.
     *
     * Antes era un borrado silencioso y se perdía lo que la tesis mide: qué
     * se aprovechó y qué se botó. Ahora la salida queda registrada con la
     * cantidad, la unidad y la caducidad que tenía la fila.
     */
    public function destroy(Request $request, Inventario $inventario): RedirectResponse
    {
        $this->comprobarPropiedad($inventario);

        ['mot_sal' => $motivo, 'cau_sal' => $causa] = $this->motivoDeSalida($request);

        $nombre = $inventario->ingrediente->nom_ing;

        DB::transaction(function () use ($inventario, $motivo, $causa) {
            SalidaInventario::registrarDesde(Inventario::whereKey($inventario->getKey()), $motivo, $causa);
            $inventario->delete();
        });

        Bitacora::registrar('quitar_inventario', 'inventario', null, ['ingrediente' => $nombre]);

        return back()->with('exito', match ($motivo) {
            'consumido'     => "Anotado: «{$nombre}» se consumió.",
            'desperdiciado' => "Anotado: «{$nombre}» se desperdició (" . mb_strtolower(SalidaInventario::TEXTO_CAUSA[$causa]) . ').',
            default         => "«{$nombre}» se quitó de tu despensa.",
        });
    }

    /** Vacía la despensa de golpe, con un mismo motivo para todo. */
    public function vaciar(Request $request): RedirectResponse
    {
        ['mot_sal' => $motivo, 'cau_sal' => $causa] = $this->motivoDeSalida($request);

        $mios    = Inventario::where('cod_usuario_inv', Auth::id());
        $cuantos = (clone $mios)->count();

        DB::transaction(function () use ($mios, $motivo, $causa) {
            SalidaInventario::registrarDesde($mios, $motivo, $causa);
            (clone $mios)->delete();
        });

        Bitacora::registrar('vaciar_inventario', 'inventario', null, ['cantidad' => $cuantos]);

        return back()->with('exito', "Se quitaron {$cuantos} ingrediente(s) de tu despensa.");
    }

    /**
     * Motivo y causa de una salida. La causa solo se pide (y solo se guarda)
     * cuando se desperdició. 'sin_resolver' no se elige: lo pone el cierre
     * automático a lo que nadie respondió.
     *
     * @return array{mot_sal: string, cau_sal: ?string}
     */
    private function motivoDeSalida(Request $request): array
    {
        $datos = $request->validate([
            'mot_sal' => ['required', Rule::in(SalidaInventario::MOTIVOS_ELEGIBLES)],
            'cau_sal' => ['exclude_unless:mot_sal,desperdiciado', 'required', Rule::in(SalidaInventario::CAUSAS)],
        ], [
            'mot_sal.required' => 'Indica qué pasó con el ingrediente.',
            'mot_sal.in'       => 'Ese motivo no es válido.',
            'cau_sal.required' => 'Indica por qué se desperdició.',
            'cau_sal.in'       => 'Esa causa no es válida.',
        ]);

        return ['mot_sal' => $datos['mot_sal'], 'cau_sal' => $datos['cau_sal'] ?? null];
    }

    /** Nadie puede tocar la despensa de otra persona. */
    private function comprobarPropiedad(Inventario $inventario): void
    {
        abort_unless($inventario->cod_usuario_inv === Auth::id(), 403,
            'Ese ingrediente no pertenece a tu despensa.');
    }
}
