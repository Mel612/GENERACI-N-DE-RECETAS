<?php

namespace App\Services;

use App\Models\HistorialConsulta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Generador de recetas a partir de los ingredientes disponibles.
 *
 * Es el núcleo del sistema. Compara la despensa de un usuario contra cada
 * receta activa y devuelve las que mejor encajan, indicando qué falta.
 *
 * Toda la comparación se hace en PostgreSQL, en una sola consulta. Traer las
 * recetas a PHP para recorrerlas una por una funcionaría con treinta recetas
 * y se volvería inviable con mil.
 *
 * ── Cómo se compara ──────────────────────────────────────────────────────
 *
 * Cada cantidad se lleva a la unidad base de su tipo (masa a gramo, volumen
 * a mililitro, unidad a pieza) multiplicando por el factor de la unidad.
 * Cuando la receta pide volumen y la despensa guarda masa, o al revés, se
 * usa la densidad del ingrediente para pasar todo a gramos. Si el ingrediente
 * no tiene densidad registrada, no se puede comparar y cuenta como faltante.
 *
 * ── Cómo se ordena ───────────────────────────────────────────────────────
 *
 * No alcanza con ordenar por compatibilidad: una receta de tres ingredientes
 * que tienes completa saldría antes que una de diez que aprovecha media
 * despensa. El puntaje pondera tres cosas:
 *
 *   compatibilidad  70 %  cuánto de la receta puedes cubrir
 *   aprovechamiento 20 %  cuánto de tu despensa usa la receta
 *   urgencia        10 %  si usa ingredientes por caducar
 *
 * La urgencia responde al objetivo del proyecto: que los alimentos se usen
 * antes de echarse a perder.
 */
class GeneradorRecetas
{
    /** Días dentro de los cuales un ingrediente se considera urgente. */
    public const DIAS_URGENCIA = 7;

    private const PESO_COMPATIBILIDAD = 0.70;
    private const PESO_APROVECHAMIENTO = 0.20;
    private const PESO_URGENCIA = 0.10;

    /**
     * Busca recetas para un usuario.
     *
     * @param  array  $filtros  categoria, nivel, tiempo_max, etiquetas[],
     *                          solo_completas, ingredientes[] (limitar la despensa),
     *                          preferencias[] (códigos de etiqueta exigidos)
     */
    public function generar(int $codUsuario, array $filtros = [], int $limite = 30): Collection
    {
        $despensa = $this->despensa($codUsuario, $filtros['ingredientes'] ?? null);

        if ($despensa->isEmpty()) {
            return collect();
        }

        $resultados = $this->comparar($codUsuario, $filtros, $despensa->count(), $limite);

        return $resultados->map(function ($fila) {
            $fila->ingredientes_faltantes = $this->desdeArreglo($fila->ingredientes_faltantes);
            $fila->ingredientes_usados    = $this->desdeArreglo($fila->ingredientes_usados);
            return $fila;
        });
    }

    /** Ingredientes disponibles del usuario, opcionalmente acotados a una selección. */
    public function despensa(int $codUsuario, ?array $soloEstos = null): Collection
    {
        return DB::table('inventario as inv')
            ->join('ingredientes as i', 'i.cod_ingrediente', '=', 'inv.cod_ingrediente_inv')
            ->join('unidades_medida as u', 'u.cod_unidad', '=', 'inv.cod_unidad_inv')
            ->join('categorias_ingrediente as c', 'c.cod_categoria_ingrediente', '=', 'i.cod_categoria_ingrediente_ing')
            ->where('inv.cod_usuario_inv', $codUsuario)
            ->where('inv.act_inv', true)
            ->where('inv.can_inv', '>', 0)
            // Lo vencido no se cocina (mismo criterio que Inventario::vigentes())
            ->where(fn ($q) => $q->whereNull('inv.fec_cad_inv')->orWhereRaw('inv.fec_cad_inv >= CURRENT_DATE'))
            ->when($soloEstos, fn ($q) => $q->whereIn('inv.cod_ingrediente_inv', $soloEstos))
            ->select(
                'inv.cod_inventario', 'inv.cod_ingrediente_inv', 'inv.can_inv', 'inv.fec_cad_inv',
                'i.nom_ing', 'i.den_ing', 'c.nom_cat',
                'u.abr_uni', 'u.tip_uni', 'u.fac_uni',
                DB::raw("(inv.fec_cad_inv IS NOT NULL AND inv.fec_cad_inv <= CURRENT_DATE + INTERVAL '" . self::DIAS_URGENCIA . " days') AS urgente"),
                DB::raw('(inv.fec_cad_inv IS NOT NULL AND inv.fec_cad_inv < CURRENT_DATE) AS vencido')
            )
            ->orderBy('i.nom_ing')
            ->get();
    }

    /**
     * La consulta que hace el trabajo.
     *
     * Se arma con el query builder en lugar de SQL crudo para que los filtros
     * del usuario viajen como parámetros y no por concatenación.
     */
    private function comparar(int $codUsuario, array $filtros, int $totalDespensa, int $limite): Collection
    {
        $urgencia = self::DIAS_URGENCIA;

        // Disponibilidad de cada ingrediente de cada receta para este usuario
        $detalle = DB::table('ingredientes_receta as ir')
            ->join('recetas as r', 'r.cod_receta', '=', 'ir.cod_receta_inr')
            ->join('ingredientes as i', 'i.cod_ingrediente', '=', 'ir.cod_ingrediente_inr')
            ->join('unidades_medida as ur', 'ur.cod_unidad', '=', 'ir.cod_unidad_inr')
            ->leftJoin('inventario as inv', function ($j) use ($codUsuario) {
                $j->on('inv.cod_ingrediente_inv', '=', 'ir.cod_ingrediente_inr')
                  ->where('inv.cod_usuario_inv', '=', $codUsuario)
                  ->where('inv.act_inv', '=', true)
                  ->where('inv.can_inv', '>', 0)
                  ->where(fn ($q) => $q->whereNull('inv.fec_cad_inv')->orWhereRaw('inv.fec_cad_inv >= CURRENT_DATE'));
            })
            ->leftJoin('unidades_medida as uv', 'uv.cod_unidad', '=', 'inv.cod_unidad_inv')
            ->where('r.act_rec', true)
            ->whereNull('r.fec_eli_rec')
            // Lo que el nutricionista rechazó no se vuelve a sugerir
            ->where('r.est_val_rec', '!=', 'rechazada')
            ->select(
                'r.cod_receta',
                'ir.opc_inr',
                'i.nom_ing',
                'inv.cod_ingrediente_inv',
                DB::raw("(inv.fec_cad_inv IS NOT NULL AND inv.fec_cad_inv <= CURRENT_DATE + INTERVAL '{$urgencia} days') AS urgente"),
                // ¿La despensa cubre lo que pide la receta?
                DB::raw("
                    CASE
                        WHEN inv.cod_inventario IS NULL THEN false
                        WHEN ur.tip_uni = uv.tip_uni
                            THEN inv.can_inv * uv.fac_uni >= ir.can_inr * ur.fac_uni
                        WHEN i.den_ing IS NOT NULL
                         AND ur.tip_uni IN ('masa','volumen')
                         AND uv.tip_uni IN ('masa','volumen')
                            THEN (CASE WHEN uv.tip_uni = 'masa'
                                       THEN inv.can_inv * uv.fac_uni
                                       ELSE inv.can_inv * uv.fac_uni * i.den_ing END)
                              >= (CASE WHEN ur.tip_uni = 'masa'
                                       THEN ir.can_inr * ur.fac_uni
                                       ELSE ir.can_inr * ur.fac_uni * i.den_ing END)
                        ELSE false
                    END AS cubierto
                ")
            );

        // Filtros sobre la receta
        $detalle
            ->when($filtros['categoria'] ?? null, fn ($q, $v) => $q->where('r.cod_categoria_receta_rec', $v))
            ->when($filtros['nivel'] ?? null,     fn ($q, $v) => $q->where('r.niv_rec', $v))
            ->when($filtros['tiempo_max'] ?? null, fn ($q, $v) => $q->where('r.tie_tot_rec', '<=', $v))
            ->when($filtros['solo_verificadas'] ?? false, fn ($q) => $q->where('r.ver_rec', true));

        if (! empty($filtros['etiquetas'])) {
            $detalle->whereExists(function ($q) use ($filtros) {
                $q->select(DB::raw(1))->from('receta_etiquetas')
                  ->whereColumn('receta_etiquetas.cod_receta_ret', 'r.cod_receta')
                  ->whereIn('receta_etiquetas.cod_etiqueta_ret', $filtros['etiquetas']);
            });
        }

        // Preferencias alimentarias del usuario.
        //
        // A diferencia de las etiquetas elegidas a mano, que se cumplen con
        // cualquiera (OR), estas se exigen TODAS: quien es vegetariano y
        // celíaco necesita las dos cosas a la vez, no una u otra.
        foreach ($filtros['preferencias'] ?? [] as $codEtiqueta) {
            $detalle->whereExists(function ($q) use ($codEtiqueta) {
                $q->select(DB::raw(1))->from('receta_etiquetas')
                  ->whereColumn('receta_etiquetas.cod_receta_ret', 'r.cod_receta')
                  ->where('receta_etiquetas.cod_etiqueta_ret', $codEtiqueta);
            });
        }

        // Agregación por receta
        $agregada = DB::query()->fromSub($detalle, 'd')
            ->join('recetas as r', 'r.cod_receta', '=', 'd.cod_receta')
            ->join('categorias_receta as cr', 'cr.cod_categoria_receta', '=', 'r.cod_categoria_receta_rec')
            ->groupBy('r.cod_receta', 'r.nom_rec', 'r.des_rec', 'r.tie_tot_rec', 'r.por_rec',
                      'r.niv_rec', 'r.img_rec', 'r.cal_por_rec', 'r.ver_rec', 'cr.nom_car')
            ->select(
                'r.cod_receta', 'r.nom_rec', 'r.des_rec', 'r.tie_tot_rec', 'r.por_rec',
                'r.niv_rec', 'r.img_rec', 'r.cal_por_rec', 'r.ver_rec',
                'cr.nom_car as categoria',
                DB::raw('COUNT(*) FILTER (WHERE NOT d.opc_inr) AS requeridos'),
                DB::raw('COUNT(*) FILTER (WHERE NOT d.opc_inr AND d.cubierto) AS cubiertos'),
                DB::raw('COUNT(DISTINCT d.cod_ingrediente_inv) FILTER (WHERE d.cubierto) AS usa_de_despensa'),
                DB::raw('COUNT(DISTINCT d.cod_ingrediente_inv) FILTER (WHERE d.cubierto AND d.urgente) AS usa_urgentes'),
                DB::raw("ARRAY_AGG(d.nom_ing ORDER BY d.nom_ing) FILTER (WHERE NOT d.opc_inr AND NOT d.cubierto) AS ingredientes_faltantes"),
                DB::raw("ARRAY_AGG(d.nom_ing ORDER BY d.nom_ing) FILTER (WHERE d.cubierto) AS ingredientes_usados")
            );

        // Puntaje final
        $c = self::PESO_COMPATIBILIDAD;
        $a = self::PESO_APROVECHAMIENTO;
        $u = self::PESO_URGENCIA;

        $consulta = DB::query()->fromSub($agregada, 'a')
            ->select('a.*')
            ->selectRaw('ROUND(100.0 * a.cubiertos / NULLIF(a.requeridos, 0), 2) AS compatibilidad')
            ->selectRaw('ROUND(100.0 * a.usa_de_despensa / ?, 2) AS aprovechamiento', [max(1, $totalDespensa)])
            ->selectRaw("ROUND(
                    {$c} * (100.0 * a.cubiertos / NULLIF(a.requeridos, 0))
                  + {$a} * (100.0 * a.usa_de_despensa / ?)
                  + {$u} * (CASE WHEN a.usa_urgentes > 0 THEN 100.0 ELSE 0 END)
                , 2) AS puntaje", [max(1, $totalDespensa)])
            ->where('a.requeridos', '>', 0);

        // «Solo lo que puedo cocinar ahora»
        if ($filtros['solo_completas'] ?? false) {
            $consulta->whereRaw('a.cubiertos = a.requeridos');
        } else {
            // Sin al menos un ingrediente en común no tiene sentido sugerirla
            $consulta->where('a.cubiertos', '>', 0);
        }

        return $consulta->orderByDesc('puntaje')
                        ->orderByDesc('compatibilidad')
                        ->limit($limite)
                        ->get();
    }

    /**
     * Guarda la consulta y sus resultados (Historia 7, Requerimiento 15).
     *
     * @param  int  $milisegundos  Cuánto tardó, para el reporte de eficiencia.
     */
    public function registrar(int $codUsuario, Collection $despensa, array $filtros,
                              Collection $resultados, int $milisegundos): HistorialConsulta
    {
        return DB::transaction(function () use ($codUsuario, $despensa, $filtros, $resultados, $milisegundos) {
            $consulta = HistorialConsulta::create([
                'cod_usuario_con' => $codUsuario,
                'ing_json_con'    => $despensa->map(fn ($d) => [
                    'id'       => $d->cod_ingrediente_inv,
                    'nombre'   => $d->nom_ing,
                    'cantidad' => (float) $d->can_inv,
                    'unidad'   => $d->abr_uni,
                ])->values()->all(),
                'fil_json_con'    => array_filter($filtros, fn ($v) => $v !== null && $v !== '' && $v !== []),
                'tot_res_con'     => min($resultados->count(), 32767),
                'tie_res_con'     => $milisegundos,
            ]);

            foreach ($resultados->take(20)->values() as $posicion => $receta) {
                $consulta->resultados()->create([
                    'cod_receta_res' => $receta->cod_receta,
                    'pos_res'        => $posicion + 1,
                    'sco_res'        => min(100, (float) $receta->compatibilidad),
                    'fal_json_res'   => $receta->ingredientes_faltantes ?: null,
                ]);
            }

            return $consulta;
        });
    }

    /** PostgreSQL devuelve los ARRAY_AGG como texto: {a,b,c}. */
    private function desdeArreglo($valor): array
    {
        if (is_array($valor)) return $valor;
        if (blank($valor))    return [];

        $limpio = trim((string) $valor, '{}');
        if ($limpio === '') return [];

        return array_map(
            fn ($x) => trim($x, '"'),
            str_getcsv($limpio)
        );
    }
}
