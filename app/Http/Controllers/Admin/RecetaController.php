<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GuardaImagenes;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\CategoriaReceta;
use App\Models\Etiqueta;
use App\Models\Ingrediente;
use App\Models\Receta;
use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gestión de recetas (Requerimientos 11 y 12).
 *
 * Una receta no es una fila: son cuatro tablas que tienen que quedar
 * coherentes entre sí (recetas, ingredientes_receta, pasos_receta y
 * receta_etiquetas). Por eso cada guardado va dentro de una transacción:
 * si algo falla a mitad de camino, no queda una receta sin pasos ni pasos
 * huérfanos.
 */
class RecetaController extends Controller
{
    use GuardaImagenes;

    public const NIVELES = ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'];

    /**
     * Origen de la receta.
     *
     * 'usuario' existe en la base pero no se ofrece aquí: se decidió que los
     * usuarios finales no crean recetas. Queda disponible por si eso cambia.
     */
    public const ORIGENES = ['sistema' => 'Cargada por el sistema', 'ia_generada' => 'Generada por IA'];

    public const ESTADOS = [
        'pendiente' => 'Pendiente de revisión',
        'aprobada'  => 'Aprobada',
        'observada' => 'Con observaciones',
        'rechazada' => 'Rechazada',
    ];

    public function index(Request $request): View
    {
        $consulta = Receta::with(['categoria', 'autor'])
            ->withCount(['ingredientes', 'pasos', 'valoraciones'])
            ->whereNull('fec_eli_rec');

        if ($request->filled('buscar')) {
            $texto = '%' . mb_strtolower($request->string('buscar')->toString()) . '%';
            $consulta->where(function ($q) use ($texto) {
                $q->whereRaw('LOWER(nom_rec) LIKE ?', [$texto])
                  ->orWhereRaw('LOWER(COALESCE(des_rec, \'\')) LIKE ?', [$texto]);
            });
        }

        foreach (['categoria' => 'cod_categoria_receta_rec', 'estado' => 'est_val_rec',
                  'origen' => 'ori_rec', 'nivel' => 'niv_rec'] as $filtro => $columna) {
            if ($request->filled($filtro)) {
                $consulta->where($columna, $request->input($filtro));
            }
        }

        if ($request->boolean('incompletas')) {
            $consulta->where(fn ($q) => $q->doesntHave('ingredientes')->orDoesntHave('pasos'));
        }

        return view('admin.recetas.index', [
            'recetas'    => $consulta->orderByDesc('fec_cre_rec')->paginate(15)->withQueryString(),
            'categorias' => CategoriaReceta::where('act_car', true)->orderBy('nom_car')->get(),
            'niveles'    => self::NIVELES,
            'origenes'   => self::ORIGENES,
            'estados'    => self::ESTADOS,
        ]);
    }

    public function show(Receta $receta): View
    {
        $receta->load([
            'categoria', 'autor', 'etiquetas',
            'ingredientes.ingrediente', 'ingredientes.unidad',
            'pasos' => fn ($q) => $q->orderBy('num_pas'),
            'validaciones.nutricionista' => fn ($q) => $q->orderByDesc('fec_rev_vre'),
        ]);

        return view('admin.recetas.show', [
            'receta'  => $receta,
            'niveles' => self::NIVELES,
            'estados' => self::ESTADOS,
        ]);
    }

    public function create(): View
    {
        return view('admin.recetas.form', $this->catalogos() + ['receta' => new Receta()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        // La imagen se resuelve fuera de la transacción: escribir el archivo
        // no se deshace con un rollback, así que si algo falla después es
        // preferible un archivo huérfano que una receta apuntando a nada.
        $imagen = $this->resolverImagen($request, 'img_rec', 'recetas');

        $receta = DB::transaction(function () use ($datos, $imagen) {
            $receta = Receta::create($this->camposReceta($datos) + [
                'cod_usuario_rec' => Auth::id(),
                'img_rec'         => $imagen,
                'est_val_rec'     => 'pendiente',
                'ver_rec'         => false,
            ]);

            $this->guardarDetalle($receta, $datos);

            return $receta;
        });

        Bitacora::registrar('crear', 'recetas', $receta->cod_receta, ['nombre' => $receta->nom_rec]);

        return redirect()->route('admin.recetas.show', $receta)
                         ->with('exito', "Receta «{$receta->nom_rec}» registrada correctamente.");
    }

    public function edit(Receta $receta): View
    {
        $receta->load(['ingredientes.ingrediente', 'ingredientes.unidad', 'pasos', 'etiquetas']);

        return view('admin.recetas.form', $this->catalogos() + ['receta' => $receta]);
    }

    public function update(Request $request, Receta $receta): RedirectResponse
    {
        $datos = $this->validar($request, $receta->cod_receta);

        // Huella del contenido antes de tocarlo. Si cambia, lo que revisó el
        // nutricionista ya no es lo que está guardado.
        $antes = $this->huellaDeContenido($receta);

        $reabierta = false;

        $imagen = $this->resolverImagen($request, 'img_rec', 'recetas', $receta->img_rec);

        DB::transaction(function () use ($receta, $datos, $antes, $imagen, &$reabierta) {
            $receta->update($this->camposReceta($datos) + [
                'img_rec'     => $imagen,
                'fec_act_rec' => now(),
            ]);

            // Se reemplaza el detalle completo. Es más simple y más seguro que
            // intentar averiguar qué fila cambió, y la transacción lo protege.
            $receta->ingredientes()->delete();
            $receta->pasos()->delete();

            $this->guardarDetalle($receta, $datos);

            // Una receta aprobada o verificada que cambia de contenido vuelve a
            // la cola de revisión. Sin esto, bastaría editarla después de
            // aprobarla para publicar cualquier cosa con el sello «Verificada».
            if (in_array($receta->est_val_rec, ['aprobada', 'observada'], true) || $receta->ver_rec) {
                if ($this->huellaDeContenido($receta->fresh()) !== $antes) {
                    $receta->update(['est_val_rec' => 'pendiente', 'ver_rec' => false]);
                    $reabierta = true;
                }
            }
        });

        Bitacora::registrar('editar', 'recetas', $receta->cod_receta,
            $reabierta ? ['reabierta_para_revision' => true] : []);

        return redirect()->route('admin.recetas.show', $receta)
                         ->with('exito', $reabierta
                             ? 'Receta actualizada. Como cambió su contenido, vuelve a la bandeja del nutricionista para revisarse.'
                             : 'Receta actualizada correctamente.');
    }

    /**
     * Baja lógica.
     *
     * Nunca se borra de verdad: la receta puede estar en el historial de
     * consultas y en las favoritas de otras personas.
     */
    public function destroy(Receta $receta): RedirectResponse
    {
        $receta->update([
            'act_rec'     => false,
            'fec_eli_rec' => now(),
            'ver_rec'     => false,
        ]);

        Bitacora::registrar('eliminar', 'recetas', $receta->cod_receta, ['nombre' => $receta->nom_rec]);

        return redirect()->route('admin.recetas.index')
                         ->with('exito', "Receta «{$receta->nom_rec}» dada de baja.");
    }

    // ------------------------------------------------------------------ apoyo

    private function catalogos(): array
    {
        return [
            'categorias'   => CategoriaReceta::where('act_car', true)->orderBy('nom_car')->get(),
            'ingredientes' => Ingrediente::with('categoria')->activos()->orderBy('nom_ing')->get(),
            'unidades'     => UnidadMedida::where('act_uni', true)->orderBy('tip_uni')->orderBy('nom_uni')->get(),
            'etiquetas'    => Etiqueta::where('act_eti', true)->orderBy('nom_eti')->get(),
            'niveles'      => self::NIVELES,
            'origenes'     => self::ORIGENES,
        ];
    }

    /**
     * Resumen de lo que el nutricionista revisa: ingredientes, cantidades y
     * pasos. Si dos huellas son iguales, la revisión anterior sigue valiendo.
     *
     * Quedan fuera a propósito el nombre, la descripción y la imagen: corregir
     * una falta de ortografía no debería mandar la receta de vuelta a la cola.
     */
    private function huellaDeContenido(Receta $receta): string
    {
        $ingredientes = $receta->ingredientes()
            ->orderBy('cod_ingrediente_inr')
            ->get(['cod_ingrediente_inr', 'cod_unidad_inr', 'can_inr', 'opc_inr'])
            ->map(fn ($f) => $f->cod_ingrediente_inr . ':' . $f->cod_unidad_inr
                           . ':' . (float) $f->can_inr . ':' . (int) $f->opc_inr)
            ->implode('|');

        $pasos = $receta->pasos()->orderBy('num_pas')
            ->get(['ins_pas', 'tie_pas', 'cri_pas'])
            ->map(fn ($p) => $p->ins_pas . ':' . $p->tie_pas . ':' . (int) $p->cri_pas)
            ->implode('|');

        return md5($ingredientes . '#' . $pasos . '#' . $receta->por_rec);
    }

    private function camposReceta(array $datos): array
    {
        return [
            'cod_categoria_receta_rec' => $datos['cod_categoria_receta_rec'],
            'nom_rec'     => $datos['nom_rec'],
            'des_rec'     => $datos['des_rec'] ?? null,
            'tie_pre_rec' => $datos['tie_pre_rec'],
            'tie_coc_rec' => $datos['tie_coc_rec'],
            'por_rec'     => $datos['por_rec'],
            'niv_rec'     => $datos['niv_rec'],
            'ori_rec'     => $datos['ori_rec'],
            
            'cal_por_rec' => $datos['cal_por_rec'] ?? null,
            'pro_por_rec' => $datos['pro_por_rec'] ?? null,
            'car_por_rec' => $datos['car_por_rec'] ?? null,
            'gra_por_rec' => $datos['gra_por_rec'] ?? null,
            'act_rec'     => true,
        ];
    }

    /** Guarda ingredientes, pasos y etiquetas de una receta ya existente. */
    private function guardarDetalle(Receta $receta, array $datos): void
    {
        foreach (array_values($datos['ingredientes']) as $orden => $fila) {
            $receta->ingredientes()->create([
                'cod_ingrediente_inr' => $fila['ingrediente'],
                'cod_unidad_inr'      => $fila['unidad'],
                'can_inr'             => $fila['cantidad'],
                'opc_inr'             => (bool) ($fila['opcional'] ?? false),
                'not_inr'             => $fila['nota'] ?? null,
                'ord_inr'             => $orden + 1,
            ]);
        }

        // Los pasos se renumeran de 1 a N al guardar: así el orden que se ve
        // en pantalla es el que queda en la base, sin huecos.
        foreach (array_values($datos['pasos']) as $i => $paso) {
            $receta->pasos()->create([
                'num_pas' => $i + 1,
                'ins_pas' => $paso['instruccion'],
                'tie_pas' => $paso['tiempo'] ?: null,
                'cri_pas' => (bool) ($paso['critico'] ?? false),
            ]);
        }

        $receta->etiquetas()->sync($datos['etiquetas'] ?? []);
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nom_rec' => ['required', 'string', 'max:200',
                          Rule::unique('recetas', 'nom_rec')->ignore($id, 'cod_receta')->whereNull('fec_eli_rec')],
            'cod_categoria_receta_rec' => ['required', 'integer', 'exists:categorias_receta,cod_categoria_receta'],
            'des_rec'     => ['nullable', 'string', 'max:2000'],
            'tie_pre_rec' => ['required', 'integer', 'min:0', 'max:1440'],
            'tie_coc_rec' => ['required', 'integer', 'min:0', 'max:1440'],
            'por_rec'     => ['required', 'integer', 'min:1', 'max:100'],
            'niv_rec'     => ['required', Rule::in(array_keys(self::NIVELES))],
            'ori_rec'     => ['required', Rule::in(array_keys(self::ORIGENES))],
            'img_rec'     => self::IMAGEN_REGLAS,

            'cal_por_rec' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'pro_por_rec' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'car_por_rec' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'gra_por_rec' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],

            'ingredientes'               => ['required', 'array', 'min:1'],
            'ingredientes.*.ingrediente' => ['required', 'integer', 'distinct', 'exists:ingredientes,cod_ingrediente'],
            'ingredientes.*.unidad'      => ['required', 'integer', 'exists:unidades_medida,cod_unidad'],
            'ingredientes.*.cantidad'    => ['required', 'numeric', 'gt:0', 'max:99999'],
            'ingredientes.*.nota'        => ['nullable', 'string', 'max:200'],
            'ingredientes.*.opcional'    => ['nullable'],

            'pasos'               => ['required', 'array', 'min:1'],
            'pasos.*.instruccion' => ['required', 'string', 'max:2000'],
            'pasos.*.tiempo'      => ['nullable', 'integer', 'min:0', 'max:1440'],
            'pasos.*.critico'     => ['nullable'],

            'etiquetas'   => ['nullable', 'array'],
            'etiquetas.*' => ['integer', 'exists:etiquetas,cod_etiqueta'],
        ], [
            'nom_rec.required' => 'El nombre de la receta es obligatorio.',
            'nom_rec.unique'   => 'Ya existe una receta activa con ese nombre.',
            'cod_categoria_receta_rec.required' => 'Debe elegir una categoría.',
            'por_rec.min'      => 'La receta debe rendir al menos una porción.',

            'ingredientes.required'               => 'La receta necesita al menos un ingrediente.',
            'ingredientes.*.ingrediente.required' => 'Elija un ingrediente en cada fila, o quite la fila vacía.',
            'ingredientes.*.ingrediente.distinct' => 'Hay un ingrediente repetido: júntelos en una sola fila.',
            'ingredientes.*.unidad.required'      => 'Cada ingrediente necesita una unidad de medida.',
            'ingredientes.*.cantidad.required'    => 'Cada ingrediente necesita una cantidad.',
            'ingredientes.*.cantidad.gt'          => 'La cantidad debe ser mayor que cero.',

            'pasos.required'               => 'La receta necesita al menos un paso de preparación.',
            'pasos.*.instruccion.required' => 'Escriba la instrucción del paso, o quite el paso vacío.',
        ]);
    }
}
