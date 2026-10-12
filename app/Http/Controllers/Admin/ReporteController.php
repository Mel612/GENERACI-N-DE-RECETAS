<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\HistorialConsulta;
use App\Models\Ingrediente;
use App\Models\Receta;
use App\Models\Usuario;
use App\Models\Valoracion;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Reportes del sistema (Historia 19, Requerimiento 18).
 *
 * Cada informe devuelve conteos ya agregados por PostgreSQL: nunca se traen
 * todas las filas a PHP para contarlas aquí. Con pocos datos da igual, pero
 * con miles de consultas registradas la diferencia es enorme.
 */
class ReporteController extends Controller
{
    /** Los valores que guarda la base, escritos como se leen en pantalla. */
    private const ETIQUETAS = [
        'sistema'     => 'Cargadas por el sistema',
        'ia_generada' => 'Generadas por IA',
        'usuario'     => 'Creadas por usuarios',
        'facil'       => 'Fácil',
        'medio'       => 'Medio',
        'dificil'     => 'Difícil',
    ];

    /** Reemplaza el valor crudo de la columna por su etiqueta legible. */
    private function legibles($filas)
    {
        return $filas->map(function ($fila) {
            $fila->etiqueta = self::ETIQUETAS[$fila->etiqueta] ?? $fila->etiqueta;
            return $fila;
        });
    }

    public function index(): View
    {
        return view('admin.reportes.index', [
            'resumen' => [
                'usuarios'     => Usuario::activos()->count(),
                'ingredientes' => Ingrediente::activos()->count(),
                'recetas'      => Receta::activas()->count(),
                'consultas'    => HistorialConsulta::count(),
                'valoraciones' => Valoracion::count(),
                'acciones'     => Bitacora::count(),
            ],
        ]);
    }

    /** Usuarios: distribución por rol, estado y altas por mes. */
    public function usuarios(): View
    {
        return view('admin.reportes.usuarios', [
            'porRol' => DB::table('usuarios')
                ->join('roles', 'roles.cod_rol', '=', 'usuarios.cod_rol_usu')
                ->whereNull('usuarios.fec_eli_usu')
                ->select('roles.nom_rol as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('roles.nom_rol')
                ->orderByDesc('valor')
                ->get(),

            'activos'   => Usuario::where('act_usu', true)->whereNull('fec_eli_usu')->count(),
            'inactivos' => Usuario::where('act_usu', false)->orWhereNotNull('fec_eli_usu')->count(),

            'porMes' => $this->porMes('usuarios', 'fec_cre_usu'),

            'conPreferencias' => DB::table('usuario_preferencias')
                ->distinct('cod_usuario_upr')->count('cod_usuario_upr'),

            'preferenciasMasElegidas' => DB::table('usuario_preferencias')
                ->join('preferencias_dieta', 'preferencias_dieta.cod_preferencia', '=', 'usuario_preferencias.cod_preferencia_upr')
                ->select('preferencias_dieta.nom_pre as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('preferencias_dieta.nom_pre')
                ->orderByDesc('valor')
                ->limit(10)
                ->get(),
        ]);
    }

    /** Ingredientes: por categoría, faltantes de datos y los más usados. */
    public function ingredientes(): View
    {
        return view('admin.reportes.ingredientes', [
            'porCategoria' => DB::table('ingredientes')
                ->join('categorias_ingrediente', 'categorias_ingrediente.cod_categoria_ingrediente', '=', 'ingredientes.cod_categoria_ingrediente_ing')
                ->where('ingredientes.act_ing', true)
                ->select('categorias_ingrediente.nom_cat as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('categorias_ingrediente.nom_cat')
                ->orderByDesc('valor')
                ->get(),

            'total'        => Ingrediente::activos()->count(),
            'sinNutricion' => Ingrediente::activos()->whereNull('cal_ing')->count(),
            'alergenos'    => Ingrediente::activos()->where('ale_ing', true)->count(),

            'masUsadosEnRecetas' => DB::table('ingredientes_receta')
                ->join('ingredientes', 'ingredientes.cod_ingrediente', '=', 'ingredientes_receta.cod_ingrediente_inr')
                ->select('ingredientes.nom_ing as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('ingredientes.nom_ing')
                ->orderByDesc('valor')
                ->limit(10)
                ->get(),

            'masEnDespensas' => DB::table('inventario')
                ->join('ingredientes', 'ingredientes.cod_ingrediente', '=', 'inventario.cod_ingrediente_inv')
                ->where('inventario.act_inv', true)
                ->select('ingredientes.nom_ing as etiqueta', DB::raw('COUNT(DISTINCT inventario.cod_usuario_inv) as valor'))
                ->groupBy('ingredientes.nom_ing')
                ->orderByDesc('valor')
                ->limit(10)
                ->get(),

            'porCaducar' => DB::table('inventario')
                ->whereNotNull('fec_cad_inv')
                ->whereBetween('fec_cad_inv', [now()->toDateString(), now()->addDays(7)->toDateString()])
                ->count(),
        ]);
    }

    /** Recetas: estado de validación, categorías, origen y las mejor valoradas. */
    public function recetas(): View
    {
        $estados = DB::table('recetas')
            ->whereNull('fec_eli_rec')
            ->select('est_val_rec', DB::raw('COUNT(*) as valor'))
            ->groupBy('est_val_rec')
            ->pluck('valor', 'est_val_rec');

        return view('admin.reportes.recetas', [
            'estados' => [
                'pendiente' => (int) ($estados['pendiente'] ?? 0),
                'aprobada'  => (int) ($estados['aprobada']  ?? 0),
                'observada' => (int) ($estados['observada'] ?? 0),
                'rechazada' => (int) ($estados['rechazada'] ?? 0),
            ],

            'verificadas' => Receta::activas()->where('ver_rec', true)->count(),
            'total'       => Receta::activas()->count(),

            'porCategoria' => DB::table('recetas')
                ->join('categorias_receta', 'categorias_receta.cod_categoria_receta', '=', 'recetas.cod_categoria_receta_rec')
                ->where('recetas.act_rec', true)->whereNull('recetas.fec_eli_rec')
                ->select('categorias_receta.nom_car as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('categorias_receta.nom_car')
                ->orderByDesc('valor')
                ->get(),

            'porOrigen' => $this->legibles(DB::table('recetas')
                ->where('act_rec', true)->whereNull('fec_eli_rec')
                ->select('ori_rec as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('ori_rec')
                ->orderByDesc('valor')
                ->get()),

            // Se ordena por dificultad real, no alfabéticamente
            'porDificultad' => $this->legibles(DB::table('recetas')
                ->where('act_rec', true)->whereNull('fec_eli_rec')
                ->select('niv_rec as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('niv_rec')
                ->orderByRaw("CASE niv_rec WHEN 'facil' THEN 1 WHEN 'medio' THEN 2 ELSE 3 END")
                ->get()),

            'mejorValoradas' => DB::table('recetas')
                ->join('valoraciones', 'valoraciones.cod_receta_val', '=', 'recetas.cod_receta')
                ->select(
                    'recetas.nom_rec as etiqueta',
                    DB::raw('ROUND(AVG(valoraciones.pun_val), 2) as promedio'),
                    DB::raw('COUNT(*) as votos')
                )
                ->groupBy('recetas.cod_receta', 'recetas.nom_rec')
                ->havingRaw('COUNT(*) >= 1')
                ->orderByDesc('promedio')
                ->orderByDesc('votos')
                ->limit(10)
                ->get(),

            'masFavoritas' => DB::table('favoritos')
                ->join('recetas', 'recetas.cod_receta', '=', 'favoritos.cod_receta_fav')
                ->select('recetas.nom_rec as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('recetas.nom_rec')
                ->orderByDesc('valor')
                ->limit(10)
                ->get(),

            'sinIngredientes' => DB::table('recetas')
                ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('ingredientes_receta')
                    ->whereColumn('ingredientes_receta.cod_receta_inr', 'recetas.cod_receta'))
                ->whereNull('fec_eli_rec')->count(),

            'sinPasos' => DB::table('recetas')
                ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('pasos_receta')
                    ->whereColumn('pasos_receta.cod_receta_pas', 'recetas.cod_receta'))
                ->whereNull('fec_eli_rec')->count(),
        ]);
    }

    /** Consultas: volumen, eficiencia del generador e ingredientes más buscados. */
    public function consultas(): View
    {
        return view('admin.reportes.consultas', [
            'total'           => HistorialConsulta::count(),
            'porMes'          => $this->porMes('historial_consulta', 'fec_cre_con'),

            'promedioResultados' => round((float) HistorialConsulta::avg('tot_res_con'), 1),
            'sinResultados'      => HistorialConsulta::where('tot_res_con', 0)->count(),

            // Objetivo específico: evaluar la eficiencia del sistema.
            'tiempoPromedio' => round((float) HistorialConsulta::whereNotNull('tie_res_con')->avg('tie_res_con')),
            'tiempoMaximo'   => (int) HistorialConsulta::max('tie_res_con'),

            'compatibilidadPromedio' => round((float) DB::table('historial_resultado')->avg('sco_res'), 1),

            'aperturas' => DB::table('historial_resultado')->where('sel_res', true)->count(),
            'resultados' => DB::table('historial_resultado')->count(),

            'recetasMasSugeridas' => DB::table('historial_resultado')
                ->join('recetas', 'recetas.cod_receta', '=', 'historial_resultado.cod_receta_res')
                ->select('recetas.nom_rec as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('recetas.nom_rec')
                ->orderByDesc('valor')
                ->limit(10)
                ->get(),
        ]);
    }

    /**
     * Conteo por mes de los últimos 12 meses.
     *
     * Se agrupa en la base con date_trunc, que es específico de PostgreSQL
     * y es lo que usa este proyecto.
     */
    private function porMes(string $tabla, string $columna)
    {
        return DB::table($tabla)
            ->where($columna, '>=', now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw("TO_CHAR(DATE_TRUNC('month', {$columna}), 'YYYY-MM') as etiqueta"),
                DB::raw('COUNT(*) as valor')
            )
            ->groupBy(DB::raw("DATE_TRUNC('month', {$columna})"))
            ->orderBy(DB::raw("DATE_TRUNC('month', {$columna})"))
            ->get()
            ->map(function ($fila) {
                // 2026-03 -> mar 2026
                [$anio, $mes] = explode('-', $fila->etiqueta);
                $nombres = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun',
                            'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
                $fila->etiqueta = $nombres[(int) $mes] . ' ' . $anio;
                return $fila;
            });
    }
}
