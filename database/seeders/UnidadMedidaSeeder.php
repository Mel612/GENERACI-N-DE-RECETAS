<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Unidades de medida del sistema.
 *
 * fac_uni es el factor de conversion hacia la unidad base de cada tipo:
 *   masa    -> gramo (g)
 *   volumen -> mililitro (ml)
 *   unidad  -> pieza (pz)
 *
 * Asi, para llevar cualquier cantidad a la unidad base basta multiplicar:
 *   cantidad_base = can * fac_uni
 * Esto es lo que permite comparar el inventario del usuario contra los
 * ingredientes que pide una receta aunque esten en unidades distintas.
 */
class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('unidades_medida')->insertOrIgnore([
            // Masa (base: gramo)
            ['nom_uni' => 'Miligramos',  'abr_uni' => 'mg',   'tip_uni' => 'masa',    'fac_uni' => 0.001],
            ['nom_uni' => 'Gramos',      'abr_uni' => 'g',    'tip_uni' => 'masa',    'fac_uni' => 1],
            ['nom_uni' => 'Kilogramos',  'abr_uni' => 'kg',   'tip_uni' => 'masa',    'fac_uni' => 1000],
            ['nom_uni' => 'Libras',      'abr_uni' => 'lb',   'tip_uni' => 'masa',    'fac_uni' => 453.592],
            ['nom_uni' => 'Onzas',       'abr_uni' => 'oz',   'tip_uni' => 'masa',    'fac_uni' => 28.3495],
            ['nom_uni' => 'Arrobas',     'abr_uni' => '@',    'tip_uni' => 'masa',    'fac_uni' => 11500],

            // Volumen (base: mililitro)
            ['nom_uni' => 'Mililitros',  'abr_uni' => 'ml',   'tip_uni' => 'volumen', 'fac_uni' => 1],
            ['nom_uni' => 'Litros',      'abr_uni' => 'l',    'tip_uni' => 'volumen', 'fac_uni' => 1000],
            ['nom_uni' => 'Cucharadita', 'abr_uni' => 'cdta', 'tip_uni' => 'volumen', 'fac_uni' => 5],
            ['nom_uni' => 'Cucharada',   'abr_uni' => 'cda',  'tip_uni' => 'volumen', 'fac_uni' => 15],
            ['nom_uni' => 'Taza',        'abr_uni' => 'tz',   'tip_uni' => 'volumen', 'fac_uni' => 240],
            ['nom_uni' => 'Vaso',        'abr_uni' => 'vs',   'tip_uni' => 'volumen', 'fac_uni' => 200],

            // Unidad (base: pieza)
            ['nom_uni' => 'Pieza',       'abr_uni' => 'pz',   'tip_uni' => 'unidad',  'fac_uni' => 1],
            ['nom_uni' => 'Docena',      'abr_uni' => 'doc',  'tip_uni' => 'unidad',  'fac_uni' => 12],
            ['nom_uni' => 'Rebanada',    'abr_uni' => 'reb',  'tip_uni' => 'unidad',  'fac_uni' => 1],
            ['nom_uni' => 'Diente',      'abr_uni' => 'dte',  'tip_uni' => 'unidad',  'fac_uni' => 1],
            ['nom_uni' => 'Pizca',       'abr_uni' => 'pzc',  'tip_uni' => 'unidad',  'fac_uni' => 1],
            ['nom_uni' => 'Al gusto',    'abr_uni' => 'ag',   'tip_uni' => 'unidad',  'fac_uni' => 1],
        ]);
    }
}
