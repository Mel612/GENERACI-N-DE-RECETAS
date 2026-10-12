<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $categoriasIngrediente = [
            ['Verduras', 'Hortalizas y verduras frescas'],
            ['Frutas', 'Frutas frescas de estacion'],
            ['Tuberculos', 'Papa, camote, yuca, oca y similares'],
            ['Carnes rojas', 'Res, cerdo, cordero y llama'],
            ['Aves', 'Pollo, pavo y otras aves'],
            ['Pescados y mariscos', 'Pescados de rio, de mar y mariscos'],
            ['Lacteos', 'Leche, yogur, mantequilla y quesos'],
            ['Huevos', 'Huevos de gallina y codorniz'],
            ['Cereales y granos', 'Arroz, quinua, trigo, maiz y avena'],
            ['Legumbres', 'Frejol, lenteja, haba, arveja y garbanzo'],
            ['Frutos secos y semillas', 'Mani, nueces, almendras y semillas'],
            ['Aceites y grasas', 'Aceites vegetales, manteca y margarina'],
            ['Condimentos y especias', 'Sal, pimienta, comino, aji y especias'],
            ['Hierbas', 'Hierbas frescas y secas para sazonar'],
            ['Endulzantes', 'Azucar, miel, panela y edulcorantes'],
            ['Harinas y panaderia', 'Harinas, levaduras, pan y masas'],
            ['Salsas y aderezos', 'Salsas envasadas, vinagres y aderezos'],
            ['Enlatados y conservas', 'Productos enlatados y en conserva'],
            ['Congelados', 'Productos congelados listos para usar'],
            ['Bebidas', 'Agua, jugos, infusiones y bebidas'],
        ];

        foreach ($categoriasIngrediente as $i => [$nombre, $descripcion]) {
            DB::table('categorias_ingrediente')->insertOrIgnore([
                'cod_categoria_ingrediente' => $i + 1,
                'nom_cat' => $nombre,
                'des_cat' => $descripcion,
                'act_cat' => true,
            ]);
        }

        $categoriasReceta = [
            ['Desayunos', 'Preparaciones para comenzar el dia'],
            ['Almuerzos', 'Platos para el almuerzo'],
            ['Cenas', 'Preparaciones ligeras para la noche'],
            ['Sopas y caldos', 'Sopas, cremas y caldos'],
            ['Ensaladas', 'Ensaladas frias y tibias'],
            ['Platos principales', 'Platos de fondo con carne, ave, pescado o vegetales'],
            ['Guarniciones', 'Acompanamientos y contornos'],
            ['Comida boliviana', 'Platos tradicionales bolivianos'],
            ['Postres', 'Dulces y postres'],
            ['Panaderia y reposteria', 'Panes, tortas, galletas y masas'],
            ['Snacks y aperitivos', 'Bocaditos y entradas'],
            ['Bebidas', 'Refrescos, jugos, infusiones y batidos'],
        ];

        foreach ($categoriasReceta as $i => [$nombre, $descripcion]) {
            DB::table('categorias_receta')->insertOrIgnore([
                'cod_categoria_receta' => $i + 1,
                'nom_car' => $nombre,
                'des_car' => $descripcion,
                'act_car' => true,
            ]);
        }

        $etiquetas = [
            ['Saludable', '#16A34A'], ['Rapido', '#F59E0B'], ['Economico', '#0EA5E9'],
            ['Facil', '#22C55E'], ['Vegetariano', '#65A30D'], ['Vegano', '#4D7C0F'],
            ['Sin gluten', '#A855F7'], ['Sin lactosa', '#8B5CF6'], ['Alto en proteinas', '#DC2626'],
            ['Bajo en calorias', '#06B6D4'], ['Para ninos', '#EC4899'], ['Tradicional', '#B45309'],
            ['Picante', '#EF4444'], ['Para llevar', '#64748B'], ['De temporada', '#14B8A6'],
        ];

        foreach ($etiquetas as $i => [$nombre, $color]) {
            DB::table('etiquetas')->insertOrIgnore([
                'cod_etiqueta' => $i + 1,
                'nom_eti' => $nombre,
                'col_eti' => $color,
                'act_eti' => true,
            ]);
        }

        $preferencias = [
            ['Vegetariano', 'No consume carne ni pescado'],
            ['Vegano', 'No consume ningun producto de origen animal'],
            ['Sin gluten', 'Evita el trigo, la cebada y el centeno'],
            ['Sin lactosa', 'Evita la leche y sus derivados'],
            ['Bajo en sodio', 'Reduce el consumo de sal'],
            ['Bajo en carbohidratos', 'Reduce harinas, azucares y almidones'],
            ['Alto en proteinas', 'Prioriza preparaciones con mayor aporte proteico'],
            ['Sin frutos secos', 'Evita mani, nueces y almendras'],
            ['Sin azucar anadida', 'Evita el azucar agregado'],
        ];

        foreach ($preferencias as $i => [$nombre, $descripcion]) {
            DB::table('preferencias_dieta')->insertOrIgnore([
                'cod_preferencia' => $i + 1,
                'nom_pre' => $nombre,
                'des_pre' => $descripcion,
                'act_pre' => true,
            ]);
        }
    }
}
