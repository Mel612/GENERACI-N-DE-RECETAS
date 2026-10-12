<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GestionaCatalogo;
use App\Http\Controllers\Concerns\ValidaNombres;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\UnidadMedida;
use App\Services\AsistenteIA;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Catalogo de unidades de medida.
 *
 * El campo fac_uni es el corazon del sistema: convierte cualquier cantidad a
 * la unidad base de su tipo (masa a gramo, volumen a mililitro, unidad a
 * pieza). Sin ese factor no se puede comparar el inventario del usuario
 * contra lo que pide una receta.
 */
class UnidadMedidaController extends Controller
{
    use GestionaCatalogo;

    use ValidaNombres;

    public const TIPOS = [
        'masa'    => 'Masa (base: gramo)',
        'volumen' => 'Volumen (base: mililitro)',
        'unidad'  => 'Unidad (base: pieza)',
    ];

    /** Cómo se llama la unidad base de cada tipo en los mensajes. */
    private const NOMBRE_BASE = ['masa' => 'g', 'volumen' => 'ml', 'unidad' => 'pieza(s)'];

    /**
     * Factor mínimo y máximo razonable por tipo, en la unidad base.
     * Masa: de un miligramo a 100 kg. Volumen: de unas gotas a 20 litros.
     * Unidad: de 1 a 1000 piezas.
     */
    public const RANGOS = [
        'masa'    => [0.001, 100000],
        'volumen' => [0.1, 20000],
        'unidad'  => [1, 1000],
    ];

    /** Abreviaturas estándar con su tipo y factor reales: no se pueden registrar con otros. */
    public const CONOCIDAS = [
        'mg'  => ['masa', 0.001],
        'g'   => ['masa', 1],
        'kg'  => ['masa', 1000],
        'oz'  => ['masa', 28.349527],
        'lb'  => ['masa', 453.592],
        'ml'  => ['volumen', 1],
        'cl'  => ['volumen', 10],
        'dl'  => ['volumen', 100],
        'l'   => ['volumen', 1000],
        'pz'  => ['unidad', 1],
        'doc' => ['unidad', 12],
    ];

    public function index(Request $request): View
    {
        $consulta = UnidadMedida::query();
        $this->buscarEn($consulta, $request->string('buscar')->toString(), ['nom_uni', 'abr_uni']);

        if ($request->filled('tipo')) {
            $consulta->where('tip_uni', $request->input('tipo'));
        }

        return view('admin.unidades.index', [
            'unidades' => $consulta->orderBy('tip_uni')->orderBy('fac_uni')->paginate(20)->withQueryString(),
            'tipos'    => self::TIPOS,
        ]);
    }

    public function create(): View
    {
        return view('admin.unidades.form', ['unidad' => new UnidadMedida(), 'tipos' => self::TIPOS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $unidad = UnidadMedida::create(
            $this->validar($request) + ['act_uni' => $request->boolean('act_uni', true)]
        );

        Bitacora::registrar('crear', 'unidades_medida', $unidad->cod_unidad, [
            'nombre' => $unidad->nom_uni, 'factor' => $unidad->fac_uni,
        ]);

        return redirect()->route('admin.unidades.index')
                         ->with('exito', 'Unidad de medida registrada correctamente.');
    }

    public function edit(UnidadMedida $unidad): View
    {
        return view('admin.unidades.form', ['unidad' => $unidad, 'tipos' => self::TIPOS]);
    }

    public function update(Request $request, UnidadMedida $unidad): RedirectResponse
    {
        $datos = $this->validar($request, $unidad->cod_unidad);

        // Cambiar el factor altera todos los calculos de compatibilidad ya hechos.
        if ((float) $datos['fac_uni'] !== (float) $unidad->fac_uni) {
            Bitacora::registrar('cambio_factor', 'unidades_medida', $unidad->cod_unidad, [
                'antes'   => (float) $unidad->fac_uni,
                'despues' => (float) $datos['fac_uni'],
            ]);
        }

        $unidad->update($datos + ['act_uni' => $request->boolean('act_uni', true)]);

        Bitacora::registrar('editar', 'unidades_medida', $unidad->cod_unidad);

        return redirect()->route('admin.unidades.index')
                         ->with('exito', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnidadMedida $unidad): RedirectResponse
    {
        if ($unidad->esBase()) {
            return redirect()->route('admin.unidades.index')->with('aviso',
                "«{$unidad->nom_uni}» es la unidad base de {$unidad->tip_uni}: todas las demás se convierten a ella, así que no se puede eliminar.");
        }

        [$recetas, $despensas] = $this->usos($unidad);

        if ($recetas + $despensas > 0) {
            return redirect()->route('admin.unidades.index')->with('aviso',
                "No se puede eliminar «{$unidad->nom_uni}»: la usan {$recetas} receta(s) y {$despensas} despensa(s). "
                . 'Cámbialas a otra unidad primero.');
        }

        [$tipo, $mensaje] = $this->eliminarODesactivar($unidad, ['ingredientesReceta', 'inventarios'], 'act_uni');

        return redirect()->route('admin.unidades.index')->with($tipo, $mensaje);
    }

    /**
     * Tipo y factor que la IA sugiere para un nombre de unidad, para
     * rellenar el formulario. No guarda nada.
     */
    public function sugerenciaIa(Request $request, AsistenteIA $asistente): JsonResponse
    {
        $this->limpiarTextos($request, ['nombre']);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:40', 'regex:' . self::PATRON_NOMBRE],
        ], [
            'nombre.required' => 'Escribe primero el nombre de la unidad.',
            'nombre.regex'    => 'El nombre de la unidad solo puede llevar letras y espacios.',
        ]);

        $sugerencia = $asistente->datosUnidad(Auth::user(), $datos['nombre']);

        if ($sugerencia === null) {
            return response()->json(['mensaje' => 'No pudimos consultar a la IA. Intenta de nuevo o llena los datos a mano.'], 422);
        }

        if (! $sugerencia['es_unidad']) {
            return response()->json(['mensaje' => "«{$datos['nombre']}» no es una unidad de medida de cocina. {$sugerencia['motivo']}"], 422);
        }

        return response()->json($sugerencia);
    }

    /**
     * Valida y limpia los datos de la unidad.
     *
     * Además de las reglas comunes de nombres (ValidaNombres):
     * 1. Las unidades base (g, ml, pz) no cambian de tipo ni de factor.
     * 2. El factor debe estar en un rango lógico para su tipo.
     * 3. Las abreviaturas conocidas (kg, l, oz...) deben tener su tipo y factor reales.
     * 4. No se aceptan duplicados disfrazados: mismo factor o abreviatura igual
     *    al nombre de otra unidad (singular/plural ya lo revisa ValidaNombres).
     * 5. Una unidad en uso no cambia de tipo ni se desactiva, y su factor solo
     *    cambia si se confirma.
     * 6. La IA revisa que sea una unidad de cocina y que tipo y factor sean
     *    razonables; si el factor es raro pero correcto, se puede confirmar.
     *
     * @return array{nom_uni: string, abr_uni: string, tip_uni: string, fac_uni: float}
     *
     * @throws ValidationException
     */
    private function validar(Request $request, ?int $id = null): array
    {
        $this->limpiarTextos($request, ['nom_uni']);
        $request->merge(['abr_uni' => mb_strtolower(preg_replace('/\s+/', '', (string) $request->input('abr_uni')))]);

        $datos = $request->validate([
            'nom_uni' => $this->reglasNombre(UnidadMedida::class, 'nom_uni', $id, 'la unidad', maximo: 40),
            'abr_uni' => ['required', 'string', 'max:6', 'regex:/^[\pL@][\pL.]*$/u',
                          $this->reglaUnicoNormalizado(UnidadMedida::class, 'abr_uni', $id, 'la abreviatura')],
            'tip_uni' => ['required', 'string', 'in:' . implode(',', array_keys(self::TIPOS))],
            'fac_uni' => ['required', 'numeric', 'gt:0', 'max:999999'],
        ], $this->mensajesNombre('nom_uni', 'la unidad') + [
            'abr_uni.required' => 'La abreviatura es obligatoria.',
            'abr_uni.max'      => 'La abreviatura no puede pasar de :max caracteres.',
            'abr_uni.regex'    => 'La abreviatura solo puede llevar letras, un punto o @: nada de números.',
            'tip_uni.in'       => 'El tipo debe ser masa, volumen o unidad.',
            'fac_uni.gt'       => 'El factor de conversión debe ser mayor que cero.',
        ]);

        $actual     = $id ? UnidadMedida::find($id) : null;
        $tipo       = $datos['tip_uni'];
        $factor     = (float) $datos['fac_uni'];
        $base       = self::NOMBRE_BASE[$tipo];
        $confirmado = $request->boolean('confirmar_factor');
        $activa     = $request->boolean('act_uni', true);
        $errores    = [];

        // 1. Unidades base
        if ($actual?->esBase()) {
            if ($tipo !== $actual->tip_uni) {
                $errores['tip_uni'] = "«{$actual->nom_uni}» es la unidad base de {$actual->tip_uni}: no puede cambiar de tipo.";
            }
            if ($factor !== 1.0) {
                $errores['fac_uni'] = "«{$actual->nom_uni}» es la unidad base: su factor siempre es 1.";
            }
            if (! $activa) {
                $errores['act_uni'] = "«{$actual->nom_uni}» es la unidad base: no se puede desactivar.";
            }
        }

        // 2. Rango lógico según el tipo
        [$minimo, $maximo] = self::RANGOS[$tipo];

        if (! isset($errores['fac_uni']) && ($factor < $minimo || $factor > $maximo)) {
            $errores['fac_uni'] = sprintf('Para %s el factor debe estar entre %s y %s %s.',
                $tipo, $this->numero($minimo), $this->numero($maximo), $base);
        } elseif ($tipo === 'unidad' && floor($factor) !== $factor) {
            $errores['fac_uni'] = 'Las unidades que cuentan piezas usan números enteros (una docena = 12 piezas, no 12,5).';
        }

        // 3. Abreviaturas conocidas
        if (isset(self::CONOCIDAS[$datos['abr_uni']])) {
            [$tipoReal, $factorReal] = self::CONOCIDAS[$datos['abr_uni']];

            if ($tipo !== $tipoReal) {
                $errores['tip_uni'] ??= "«{$datos['abr_uni']}» es una unidad de {$tipoReal}, no de {$tipo}.";
            } elseif (abs($factor - $factorReal) / $factorReal > 0.01) {
                $errores['fac_uni'] ??= "Un «{$datos['abr_uni']}» equivale a {$this->numero($factorReal)} {$base}; pusiste {$this->numero($factor)}.";
            }
        }

        // 4. Duplicados disfrazados
        $otras = UnidadMedida::query()->when($id, fn ($q) => $q->where('cod_unidad', '!=', $id))->get();

        if ($gemela = $otras->first(fn ($u) => $this->normalizar($u->nom_uni) === $this->normalizar($datos['abr_uni']))) {
            $errores['abr_uni'] ??= "La abreviatura no puede ser el nombre de otra unidad («{$gemela->nom_uni}»).";
        }

        if ($gemela = $otras->first(fn ($u) => $this->normalizar($u->abr_uni) === $this->normalizar($datos['nom_uni']))) {
            $errores['nom_uni'] ??= "El nombre no puede ser la abreviatura de otra unidad («{$gemela->nom_uni}»).";
        }

        if ($tipo !== 'unidad'
            && $gemela = $otras->first(fn ($u) => $u->tip_uni === $tipo && abs((float) $u->fac_uni - $factor) < 0.000001)) {
            $errores['fac_uni'] ??= "«{$gemela->nom_uni}» ya mide exactamente {$this->numero($factor)} {$base}: "
                                  . 'usa esa unidad en lugar de crear otra igual.';
        }

        // 5. Unidad en uso
        if ($actual && ! $actual->esBase()) {
            [$recetas, $despensas] = $this->usos($actual);
            $usos = "la usan {$recetas} receta(s) y {$despensas} despensa(s)";

            if ($recetas + $despensas > 0) {
                if ($tipo !== $actual->tip_uni) {
                    $errores['tip_uni'] ??= "No puedes cambiar el tipo: {$usos}, y sus cantidades dejarían de tener sentido.";
                }
                if (! $activa) {
                    $errores['act_uni'] ??= "No puedes desactivarla: {$usos}. Cámbialas a otra unidad primero.";
                }
                if (abs($factor - (float) $actual->fac_uni) > 0.000001 && ! $confirmado) {
                    $errores['fac_uni'] ??= "Cambiar el factor cambia todas las cantidades guardadas: {$usos}. "
                                          . 'Si estás seguro, marca «Confirmo el factor» y guarda de nuevo.';
                }
            }
        }

        if ($errores !== []) {
            throw ValidationException::withMessages($errores);
        }

        // 6. Revisión con la IA, solo si cambió algo que pueda revisar
        $cambio = ! $actual
            || $this->normalizar($actual->nom_uni) !== $this->normalizar($datos['nom_uni'])
            || $actual->tip_uni !== $tipo
            || abs((float) $actual->fac_uni - $factor) > 0.000001;

        if ($cambio && ! $actual?->esBase()) {
            $this->revisarConIa($datos, $confirmado);
        }

        return $datos;
    }

    /**
     * Compara lo que se escribió con lo que la IA espera de esa unidad.
     *
     * @param  array{nom_uni: string, abr_uni: string, tip_uni: string, fac_uni: float}  $datos
     *
     * @throws ValidationException
     */
    private function revisarConIa(array $datos, bool $confirmado): void
    {
        $ia = app(AsistenteIA::class)->datosUnidad(Auth::user(), $datos['nom_uni']);

        if ($ia === null) {
            return;
        }

        if (! $ia['es_unidad']) {
            throw ValidationException::withMessages([
                'nom_uni' => "«{$datos['nom_uni']}» no es una unidad de medida de cocina. {$ia['motivo']}",
            ]);
        }

        if ($confirmado) {
            return;
        }

        $esperado = "{$this->numero($ia['factor'])} " . self::NOMBRE_BASE[$ia['tipo']];

        if ($ia['tipo'] !== $datos['tip_uni']) {
            throw ValidationException::withMessages([
                'tip_uni' => "«{$datos['nom_uni']}» suele ser de {$ia['tipo']} ({$esperado}), no de {$datos['tip_uni']}. "
                           . 'Si estás seguro, marca «Confirmo el factor» y guarda de nuevo.',
            ]);
        }

        $proporcion = (float) $datos['fac_uni'] / $ia['factor'];

        if ($proporcion > 3 || $proporcion < 1 / 3) {
            throw ValidationException::withMessages([
                'fac_uni' => "«{$datos['nom_uni']}» suele equivaler a unos {$esperado}; pusiste "
                           . "{$this->numero((float) $datos['fac_uni'])}. Si estás seguro, marca «Confirmo el factor» y guarda de nuevo.",
            ]);
        }
    }

    /**
     * Cuántas recetas y despensas usan la unidad.
     *
     * @return array{0: int, 1: int}
     */
    private function usos(UnidadMedida $unidad): array
    {
        return [
            $unidad->ingredientesReceta()->distinct()->count('cod_receta_inr'),
            $unidad->inventarios()->count(),
        ];
    }

    /** Número sin ceros de más y con separador de miles: 11500 → «11 500», 0.001 → «0,001». */
    private function numero(float $valor): string
    {
        $texto = number_format($valor, 6, ',', ' ');

        return rtrim(rtrim($texto, '0'), ',');
    }
}
