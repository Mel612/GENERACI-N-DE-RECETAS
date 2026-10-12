<?php

namespace App\Http\Controllers\Nutricionista;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\CategoriaReceta;
use App\Models\Receta;
use App\Models\ValidacionReceta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Panel del nutricionista (Historias 9 a 13, Requerimientos 8, 9, 10, 16 y 17).
 *
 * El flujo de una receta es:
 *
 *   pendiente ──► aprobada ──► verificada
 *       ▲            │
 *       │            └──► (puede volver a revisarse)
 *       └── observada / rechazada
 *
 * Marcar «verificada» solo es posible sobre una receta aprobada. La base lo
 * impone con un CHECK, y aquí se comprueba antes para dar un mensaje claro
 * en lugar de un error de PostgreSQL.
 *
 * Cada dictamen queda guardado en validaciones_receta: no se sobrescribe el
 * anterior. El historial de revisiones es parte de la trazabilidad.
 */
class ValidacionController extends Controller
{
    public const ESTADOS = [
        'aprobada'  => 'Aprobar',
        'observada' => 'Devolver con observaciones',
        'rechazada' => 'Rechazar',
    ];

    /** Bandeja de revisión (Historia 9). */
    public function index(Request $request): View
    {
        $consulta = Receta::with(['categoria', 'autor'])
            ->withCount(['ingredientes', 'pasos', 'validaciones'])
            ->activas();

        // Por defecto se muestra lo que espera revisión. Un valor raro en la
        // URL no debe dejar la bandeja en blanco sin explicación.
        $estado = $request->input('estado', 'por_revisar');

        if (! in_array($estado, ['por_revisar', 'sin_verificar', 'todas', ...Receta::ESTADOS_VALIDACION], true)) {
            $estado = 'por_revisar';
        }

        match ($estado) {
            'por_revisar' => $consulta->whereIn('est_val_rec', ['pendiente', 'observada']),
            'sin_verificar' => $consulta->where('est_val_rec', 'aprobada')->where('ver_rec', false),
            'todas'       => null,
            default       => $consulta->where('est_val_rec', $estado),
        };

        if ($request->filled('buscar')) {
            $texto = '%' . mb_strtolower($request->string('buscar')->toString()) . '%';
            $consulta->whereRaw('LOWER(nom_rec) LIKE ?', [$texto]);
        }

        if ($request->filled('categoria')) {
            $consulta->where('cod_categoria_receta_rec', $request->integer('categoria'));
        }

        if ($request->boolean('sin_nutricion')) {
            $consulta->whereNull('cal_por_rec');
        }

        $activas = Receta::activas();

        return view('nutricionista.index', [
            'recetas'    => $consulta->orderByRaw("
                    CASE est_val_rec
                        WHEN 'observada' THEN 1
                        WHEN 'pendiente' THEN 2
                        WHEN 'aprobada'  THEN 3
                        ELSE 4
                    END
                ")->orderBy('fec_cre_rec')->paginate(15)->withQueryString(),

            'categorias' => CategoriaReceta::where('act_car', true)->orderBy('nom_car')->get(),
            'estado'     => $estado,

            'resumen' => [
                'pendientes'   => (clone $activas)->where('est_val_rec', 'pendiente')->count(),
                'observadas'   => (clone $activas)->where('est_val_rec', 'observada')->count(),
                'aprobadas'    => (clone $activas)->where('est_val_rec', 'aprobada')->count(),
                'verificadas'  => (clone $activas)->where('ver_rec', true)->count(),
                'sin_nutricion' => (clone $activas)->whereNull('cal_por_rec')->count(),
            ],

            'misRevisiones' => ValidacionReceta::where('cod_usuario_vre', Auth::id())->count(),
        ]);
    }

    /** Pantalla de revisión de una receta (Historias 10 y 11). */
    public function revisar(Receta $receta): View
    {
        abort_if($receta->fec_eli_rec !== null, 404);

        $receta->load([
            'categoria', 'autor', 'etiquetas',
            'ingredientes.ingrediente.categoria', 'ingredientes.unidad',
            'pasos' => fn ($q) => $q->orderBy('num_pas'),
            'validaciones.nutricionista' => fn ($q) => $q->orderByDesc('fec_rev_vre'),
        ]);

        return view('nutricionista.revisar', [
            'receta'   => $receta,
            'avisos'   => $this->avisos($receta),
            'calculo'  => $this->calcularNutricion($receta),
            'estados'  => self::ESTADOS,
        ]);
    }

    /**
     * Guarda el dictamen (Historias 11, 12 y 13).
     *
     * En una sola operación se puede corregir la información nutricional y
     * emitir el dictamen: son dos cosas que el nutricionista hace a la vez.
     */
    public function dictaminar(Request $request, Receta $receta): RedirectResponse
    {
        $datos = $request->validate([
            'est_vre'     => ['required', Rule::in(array_keys(self::ESTADOS))],
            'obs_vre'     => ['nullable', 'string', 'max:2000'],
            'val_ing_vre' => ['nullable', 'boolean'],
            'val_pas_vre' => ['nullable', 'boolean'],
            'val_nut_vre' => ['nullable', 'boolean'],
            'verificar'   => ['nullable', 'boolean'],

            // Información nutricional por porción (Historia 11)
            'cal_por_rec' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'pro_por_rec' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'car_por_rec' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'gra_por_rec' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'obs_nut_rec' => ['nullable', 'string', 'max:1000'],
        ], [
            'est_vre.required' => 'Elige qué dictamen le das a la receta.',
        ]);

        // Rechazar u observar sin explicar por qué no le sirve a nadie
        if (in_array($datos['est_vre'], ['rechazada', 'observada'], true) && blank($datos['obs_vre'])) {
            return back()->withInput()->withErrors([
                'obs_vre' => 'Explica qué hay que corregir. Sin observaciones, quien escribió la receta no sabe qué cambiar.',
            ]);
        }

        // Verificar solo tiene sentido sobre una receta aprobada
        $verificar = $request->boolean('verificar') && $datos['est_vre'] === 'aprobada';

        if ($request->boolean('verificar') && $datos['est_vre'] !== 'aprobada') {
            return back()->withInput()->withErrors([
                'verificar' => 'Solo se puede marcar como verificada una receta aprobada.',
            ]);
        }

        DB::transaction(function () use ($receta, $datos, $verificar) {
            // El dictamen se agrega al historial, no reemplaza al anterior
            ValidacionReceta::create([
                'cod_receta_vre'  => $receta->cod_receta,
                'cod_usuario_vre' => Auth::id(),
                'est_vre'         => $datos['est_vre'],
                'obs_vre'         => $datos['obs_vre'] ?: null,
                'val_ing_vre'     => (bool) ($datos['val_ing_vre'] ?? false),
                'val_pas_vre'     => (bool) ($datos['val_pas_vre'] ?? false),
                'val_nut_vre'     => (bool) ($datos['val_nut_vre'] ?? false),
            ]);

            $receta->update([
                'est_val_rec' => $datos['est_vre'],
                // Una receta que deja de estar aprobada pierde la verificación
                'ver_rec'     => $verificar,
                'cal_por_rec' => $datos['cal_por_rec'] ?? $receta->cal_por_rec,
                'pro_por_rec' => $datos['pro_por_rec'] ?? $receta->pro_por_rec,
                'car_por_rec' => $datos['car_por_rec'] ?? $receta->car_por_rec,
                'gra_por_rec' => $datos['gra_por_rec'] ?? $receta->gra_por_rec,
                'obs_nut_rec' => $datos['obs_nut_rec'] ?? $receta->obs_nut_rec,
                'fec_act_rec' => now(),
            ]);
        });

        Bitacora::registrar('validar_receta', 'recetas', $receta->cod_receta, [
            'receta'     => $receta->nom_rec,
            'dictamen'   => $datos['est_vre'],
            'verificada' => $verificar,
        ]);

        $mensajes = [
            'aprobada'  => $verificar
                ? "«{$receta->nom_rec}» quedó aprobada y marcada como verificada."
                : "«{$receta->nom_rec}» quedó aprobada.",
            'observada' => "«{$receta->nom_rec}» se devolvió con observaciones.",
            'rechazada' => "«{$receta->nom_rec}» quedó rechazada.",
        ];

        return redirect()->route('nutricionista.index')
                         ->with('exito', $mensajes[$datos['est_vre']]);
    }

    // ------------------------------------------------------------------ apoyo

    /**
     * Revisa la receta y devuelve lo que merece atención.
     *
     * No decide por el nutricionista: le señala dónde mirar.
     */
    private function avisos(Receta $receta): array
    {
        $avisos = [];

        if ($receta->ingredientes->isEmpty()) {
            $avisos[] = ['error', 'La receta no tiene ingredientes.'];
        }

        if ($receta->pasos->isEmpty()) {
            $avisos[] = ['error', 'La receta no tiene pasos de preparación.'];
        }

        $sinDatos = $receta->ingredientes->filter(fn ($i) => $i->ingrediente->cal_ing === null);

        if ($sinDatos->isNotEmpty()) {
            $avisos[] = ['alerta', 'Sin datos nutricionales: '
                . $sinDatos->map(fn ($i) => $i->ingrediente->nom_ing)->implode(', ')
                . '. El cálculo de calorías queda incompleto.'];
        }

        $alergenos = $receta->ingredientes->filter(fn ($i) => $i->ingrediente->ale_ing);

        if ($alergenos->isNotEmpty()) {
            $avisos[] = ['aviso', 'Contiene alérgenos comunes: '
                . $alergenos->map(fn ($i) => $i->ingrediente->nom_ing)->implode(', ') . '.'];
        }

        if ($receta->cal_por_rec === null) {
            $avisos[] = ['alerta', 'La receta no tiene información nutricional registrada.'];
        }

        if ($receta->ori_rec === 'ia_generada') {
            $avisos[] = ['aviso', 'Esta receta la generó la inteligencia artificial. '
                . 'Conviene revisar con cuidado las cantidades y los tiempos.'];
        }

        if ($receta->tie_tot_rec > 180) {
            $avisos[] = ['aviso', 'El tiempo total supera las tres horas. Verifica que sea correcto.'];
        }

        return $avisos;
    }

    /**
     * Calcula la nutrición a partir de los ingredientes, para contrastarla
     * con la que declara la receta.
     *
     * Solo cuenta lo que se puede llevar a gramos: masa directa, o volumen
     * cuando el ingrediente tiene densidad. Devuelve también qué quedó fuera,
     * para que el nutricionista sepa cuánto vale el número.
     */
    private function calcularNutricion(Receta $receta): array
    {
        $suma = ['cal' => 0.0, 'pro' => 0.0, 'car' => 0.0, 'gra' => 0.0];
        $contados = 0;
        $ignorados = [];

        foreach ($receta->ingredientes as $fila) {
            $ingrediente = $fila->ingrediente;
            $unidad      = $fila->unidad;

            if ($ingrediente->cal_ing === null) {
                $ignorados[] = "{$ingrediente->nom_ing} (sin datos)";
                continue;
            }

            $gramos = match (true) {
                $unidad->tip_uni === 'masa' => (float) $fila->can_inr * (float) $unidad->fac_uni,
                $unidad->tip_uni === 'volumen' && $ingrediente->den_ing !== null
                    => (float) $fila->can_inr * (float) $unidad->fac_uni * (float) $ingrediente->den_ing,
                default => null,
            };

            if ($gramos === null) {
                $ignorados[] = "{$ingrediente->nom_ing} (medido en {$unidad->tip_uni})";
                continue;
            }

            $factor = $gramos / 100;
            $suma['cal'] += (float) $ingrediente->cal_ing * $factor;
            $suma['pro'] += (float) $ingrediente->pro_ing * $factor;
            $suma['car'] += (float) $ingrediente->car_ing * $factor;
            $suma['gra'] += (float) $ingrediente->gra_ing * $factor;
            $contados++;
        }

        $porciones = max(1, (int) $receta->por_rec);

        return [
            'por_porcion' => array_map(fn ($v) => round($v / $porciones, 2), $suma),
            'contados'    => $contados,
            'ignorados'   => $ignorados,
            'total'       => $receta->ingredientes->count(),
        ];
    }
}
