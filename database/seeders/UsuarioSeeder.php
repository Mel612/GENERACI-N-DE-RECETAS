<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insertOrIgnore([
            [
                'nom_usu'     => 'Administrador del Sistema',
                'cor_usu'     => 'admin@recetas.bo',
                'con_usu'     => Hash::make('Admin.2026'),
                'cod_rol_usu' => 1,
                'act_usu'     => true,
            ],
            [
                'nom_usu'     => 'Nutricionista de Prueba',
                'cor_usu'     => 'nutricionista@recetas.bo',
                'con_usu'     => Hash::make('Nutri.2026'),
                'cod_rol_usu' => 2,
                'act_usu'     => true,
            ],
            [
                'nom_usu'     => 'Usuario de Prueba',
                'cor_usu'     => 'usuario@recetas.bo',
                'con_usu'     => Hash::make('Usuario.2026'),
                'cod_rol_usu' => 3,
                'act_usu'     => true,
            ],
        ]);
    }
}
