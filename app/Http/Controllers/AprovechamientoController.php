<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Cuánto de lo que entra a la despensa se aprovecha.
 *
 * Solo lee: las cuentas las hacen las vistas v_salidas_medidas (cada salida
 * en gramos y en bolivianos) y v_aprovechamiento_usuario (las dos tasas).
 * Lo 'sin_resolver' y lo 'otro' no entran en ninguna tasa.
 */
class AprovechamientoController extends Controller
{
    public function index(): View
    {
        $id      = Auth::id();
        $salidas = fn () => DB::table('v_salidas_medidas')->where('cod_usuario_sal', $id);

        // Una fila por usuario; null si todavía no sacó nada de la despensa
        $totales = DB::table('v_aprovechamiento_usuario')->where('cod_usuario_sal', $id)->first();

        // ¿Cuánto de lo desperdiciado tiene precio? Sin precio la vista da
        // null y la suma lo trata como cero: hay que saberlo para no mostrar
        // «Bs 0» cuando en realidad no se sabe.
        $precios = $salidas()->where('mot_sal', 'desperdiciado')
            ->selectRaw('COUNT(*) AS total, COUNT(bolivianos) AS con_precio')
            ->first();

        $ranking = $salidas()->where('mot_sal', 'desperdiciado')
            ->groupBy('cod_ingrediente_sal', 'nom_ing')
            ->selectRaw('nom_ing AS etiqueta, COUNT(*) AS valor, SUM(gramos) AS gramos')
            ->orderByDesc('valor')
            ->orderByRaw('SUM(gramos) DESC NULLS LAST')
            ->orderBy('nom_ing')
            ->limit(8)
            ->get();

        return view('aprovechamiento.index', [
            'totales'   => $totales,
            'precios'   => $precios,
            'ranking'   => $ranking,
            'historial' => $salidas()
                ->orderByDesc('fec_sal')->orderByDesc('cod_salida')
                ->paginate(15),
        ]);
    }
}
