<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['cod_rol' => 1, 'nom_rol' => 'Administrador', 'des_rol' => 'Administra usuarios, catalogos, reportes y monitoreo del sistema.', 'act_rol' => true],
            ['cod_rol' => 2, 'nom_rol' => 'Nutricionista', 'des_rol' => 'Revisa, observa y aprueba las recetas y su informacion nutricional.', 'act_rol' => true],
            ['cod_rol' => 3, 'nom_rol' => 'Usuario',       'des_rol' => 'Gestiona su inventario y genera recetas con los ingredientes disponibles.', 'act_rol' => true],
        ]);
    }
}
