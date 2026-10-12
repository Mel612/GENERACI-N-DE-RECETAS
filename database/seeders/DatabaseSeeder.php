<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Catálogos base. Si creaste la base con bd_comida.sql, estos
            // datos ya existen y los seeders no duplican nada.
            RolSeeder::class,
            UnidadMedidaSeeder::class,
            CatalogoSeeder::class,
            UsuarioSeeder::class,

            // Contenido de ejemplo
            IngredienteSeeder::class,
            RecetaSeeder::class,
        ]);
    }
}
