<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Catálogo de ingredientes.
 *
 * Los valores nutricionales son por cada 100 g de alimento crudo o tal como
 * se compra, tomados de tablas de composición de alimentos de uso corriente.
 *
 * den_ing es la densidad aparente en gramos por mililitro. Sirve para que el
 * sistema compare una receta que pide «2 tazas de arroz» contra una despensa
 * que guarda «1 kg de arroz». Solo se registra en alimentos que se miden de
 * las dos formas.
 * Para el documento final conviene citarlos contra la Tabla Boliviana de
 * Composición de Alimentos si el tribunal pide la fuente.
 *
 * Se usa insertOrIgnore: correr el seeder dos veces no duplica nada.
 */
class IngredienteSeeder extends Seeder
{
    /** [categoría, nombre, sinónimos, kcal, proteína, carbohidrato, grasa, densidad, alérgeno] */
    private const DATOS = [
        ['Verduras', 'Cebolla', 'cebolla blanca,cebolla cabeza', 40, 1.1, 9.3, 0.1, null, false],
        ['Verduras', 'Cebolla verde', 'cebollín,cebolla de verdeo', 32, 1.8, 7.3, 0.2, null, false],
        ['Verduras', 'Tomate', 'jitomate', 18, 0.9, 3.9, 0.2, null, false],
        ['Verduras', 'Zanahoria', null, 41, 0.9, 9.6, 0.2, null, false],
        ['Verduras', 'Locoto', 'rocoto,ají picante', 40, 1.9, 8.8, 0.4, null, false],
        ['Verduras', 'Ají amarillo', 'ají', 40, 2.0, 8.8, 0.4, null, false],
        ['Verduras', 'Pimiento morrón', 'pimentón,ají dulce', 31, 1.0, 6.0, 0.3, null, false],
        ['Verduras', 'Lechuga', null, 15, 1.4, 2.9, 0.2, null, false],
        ['Verduras', 'Repollo', 'col', 25, 1.3, 5.8, 0.1, null, false],
        ['Verduras', 'Acelga', null, 19, 1.8, 3.7, 0.2, null, false],
        ['Verduras', 'Espinaca', null, 23, 2.9, 3.6, 0.4, null, false],
        ['Verduras', 'Zapallo', 'calabaza', 26, 1.0, 6.5, 0.1, null, false],
        ['Verduras', 'Zapallito italiano', 'zucchini,calabacín', 17, 1.2, 3.1, 0.3, null, false],
        ['Verduras', 'Pepino', null, 15, 0.7, 3.6, 0.1, null, false],
        ['Verduras', 'Choclo', 'maíz tierno,elote', 86, 3.3, 19.0, 1.2, null, false],
        ['Verduras', 'Vainita', 'judía verde,poroto verde', 31, 1.8, 7.0, 0.2, null, false],
        ['Verduras', 'Apio', null, 16, 0.7, 3.0, 0.2, null, false],
        ['Verduras', 'Brócoli', null, 34, 2.8, 6.6, 0.4, null, false],
        ['Verduras', 'Coliflor', null, 25, 1.9, 5.0, 0.3, null, false],
        ['Verduras', 'Remolacha', 'betarraga,beterraga', 43, 1.6, 9.6, 0.2, null, false],
        ['Frutas', 'Limón', 'limón sutil', 29, 1.1, 9.3, 0.3, null, false],
        ['Frutas', 'Naranja', null, 47, 0.9, 11.8, 0.1, null, false],
        ['Frutas', 'Plátano', 'banana,guineo', 89, 1.1, 22.8, 0.3, null, false],
        ['Frutas', 'Manzana', null, 52, 0.3, 13.8, 0.2, null, false],
        ['Frutas', 'Palta', 'aguacate', 160, 2.0, 8.5, 14.7, null, false],
        ['Frutas', 'Piña', 'ananá', 50, 0.5, 13.1, 0.1, null, false],
        ['Frutas', 'Papaya', null, 43, 0.5, 10.8, 0.3, null, false],
        ['Frutas', 'Mango', null, 60, 0.8, 15.0, 0.4, null, false],
        ['Frutas', 'Durazno', 'melocotón', 39, 0.9, 9.5, 0.3, null, false],
        ['Frutas', 'Frutilla', 'fresa', 32, 0.7, 7.7, 0.3, null, false],
        ['Frutas', 'Maracuyá', null, 97, 2.2, 23.4, 0.7, null, false],
        ['Frutas', 'Tumbo', 'curuba', 25, 0.6, 6.3, 0.1, null, false],
        ['Tuberculos', 'Papa', 'patata,papa blanca', 77, 2.0, 17.5, 0.1, null, false],
        ['Tuberculos', 'Papa imilla', 'imilla negra', 77, 2.0, 17.5, 0.1, null, false],
        ['Tuberculos', 'Chuño', 'papa deshidratada', 333, 4.0, 80.0, 0.4, 0.7, false],
        ['Tuberculos', 'Tunta', 'chuño blanco', 323, 2.0, 78.0, 0.3, 0.65, false],
        ['Tuberculos', 'Camote', 'batata,boniato', 86, 1.6, 20.1, 0.1, null, false],
        ['Tuberculos', 'Yuca', 'mandioca', 160, 1.4, 38.1, 0.3, null, false],
        ['Tuberculos', 'Oca', null, 61, 1.0, 13.3, 0.6, null, false],
        ['Tuberculos', 'Racacha', 'arracacha,virraca', 104, 1.0, 24.9, 0.3, null, false],
        ['Carnes rojas', 'Carne de res', 'res,carne de vaca', 250, 26.0, 0.0, 15.0, null, false],
        ['Carnes rojas', 'Lomo de res', 'lomo', 201, 22.0, 0.0, 12.0, null, false],
        ['Carnes rojas', 'Carne molida de res', 'carne picada', 254, 17.2, 0.0, 20.0, null, false],
        ['Carnes rojas', 'Charque', 'charqui,carne seca', 410, 58.0, 1.0, 19.0, null, false],
        ['Carnes rojas', 'Carne de cerdo', 'chancho', 242, 27.3, 0.0, 14.0, null, false],
        ['Carnes rojas', 'Costilla de cerdo', null, 277, 20.0, 0.0, 21.0, null, false],
        ['Carnes rojas', 'Carne de cordero', null, 294, 25.0, 0.0, 21.0, null, false],
        ['Carnes rojas', 'Carne de llama', null, 110, 24.8, 0.0, 1.0, null, false],
        ['Carnes rojas', 'Hígado de res', null, 135, 20.4, 3.9, 3.6, null, false],
        ['Aves', 'Pollo entero', null, 239, 27.0, 0.0, 14.0, null, false],
        ['Aves', 'Pechuga de pollo', null, 165, 31.0, 0.0, 3.6, null, false],
        ['Aves', 'Pierna de pollo', 'muslo de pollo', 209, 26.0, 0.0, 11.0, null, false],
        ['Aves', 'Menudencia de pollo', null, 130, 18.0, 1.0, 5.0, null, false],
        ['Pescados y mariscos', 'Trucha', null, 148, 20.8, 0.0, 6.6, null, true],
        ['Pescados y mariscos', 'Pejerrey', null, 105, 19.0, 0.0, 2.5, null, true],
        ['Pescados y mariscos', 'Surubí', null, 97, 19.5, 0.0, 1.8, null, true],
        ['Pescados y mariscos', 'Atún en lata', null, 128, 23.6, 0.0, 3.0, null, true],
        ['Pescados y mariscos', 'Camarón', null, 99, 24.0, 0.2, 0.3, null, true],
        ['Lacteos', 'Leche entera', 'leche', 61, 3.2, 4.8, 3.3, 1.03, true],
        ['Lacteos', 'Leche evaporada', null, 134, 6.8, 10.0, 7.6, 1.07, true],
        ['Lacteos', 'Yogur natural', null, 59, 3.5, 4.7, 3.3, 1.03, true],
        ['Lacteos', 'Queso fresco', 'quesillo', 264, 17.0, 3.0, 20.0, 0.55, true],
        ['Lacteos', 'Queso criollo', 'queso de Chiquitos', 330, 22.0, 2.5, 26.0, null, true],
        ['Lacteos', 'Mantequilla', 'manteca', 717, 0.9, 0.1, 81.1, 0.95, true],
        ['Lacteos', 'Crema de leche', 'nata', 340, 2.1, 2.8, 36.0, 1.0, true],
        ['Huevos', 'Huevo de gallina', 'huevo', 143, 12.6, 0.7, 9.5, null, true],
        ['Huevos', 'Huevo de codorniz', null, 158, 13.1, 0.4, 11.1, null, true],
        ['Cereales y granos', 'Arroz blanco', 'arroz', 365, 7.1, 80.0, 0.7, 0.85, false],
        ['Cereales y granos', 'Quinua', 'quinoa', 368, 14.1, 64.2, 6.1, 0.75, false],
        ['Cereales y granos', 'Cañahua', 'cañihua', 344, 14.0, 63.4, 4.3, 0.7, false],
        ['Cereales y granos', 'Amaranto', 'kiwicha', 371, 13.6, 65.2, 7.0, 0.75, false],
        ['Cereales y granos', 'Avena', null, 389, 16.9, 66.3, 6.9, 0.4, true],
        ['Cereales y granos', 'Maíz pelado', 'mote de maíz', 365, 9.4, 74.3, 4.7, 0.8, false],
        ['Cereales y granos', 'Trigo pelado', null, 339, 13.7, 71.0, 2.5, 0.8, true],
        ['Cereales y granos', 'Fideo', 'pasta,tallarín', 371, 13.0, 74.7, 1.5, null, true],
        ['Legumbres', 'Haba fresca', null, 88, 7.9, 14.0, 0.7, null, false],
        ['Legumbres', 'Haba seca', null, 341, 26.1, 58.3, 1.5, 0.78, false],
        ['Legumbres', 'Arveja fresca', 'guisante', 81, 5.4, 14.5, 0.4, null, false],
        ['Legumbres', 'Frejol', 'poroto,frijol', 333, 23.6, 60.0, 0.8, 0.8, false],
        ['Legumbres', 'Lenteja', null, 353, 25.8, 60.1, 1.1, 0.85, false],
        ['Legumbres', 'Garbanzo', null, 364, 19.3, 60.7, 6.0, 0.8, false],
        ['Legumbres', 'Tarwi', 'chocho,lupino', 277, 17.3, 17.6, 17.5, null, false],
        ['Frutos secos y semillas', 'Maní', 'cacahuate', 567, 25.8, 16.1, 49.2, 0.6, true],
        ['Frutos secos y semillas', 'Nuez', null, 654, 15.2, 13.7, 65.2, null, true],
        ['Frutos secos y semillas', 'Almendra', null, 579, 21.2, 21.6, 49.9, null, true],
        ['Frutos secos y semillas', 'Semilla de girasol', 'pipa', 584, 20.8, 20.0, 51.5, 0.55, true],
        ['Frutos secos y semillas', 'Sésamo', 'ajonjolí', 573, 17.7, 23.4, 49.7, 0.6, true],
        ['Aceites y grasas', 'Aceite vegetal', 'aceite de girasol,aceite', 884, 0.0, 0.0, 100.0, 0.92, false],
        ['Aceites y grasas', 'Aceite de oliva', null, 884, 0.0, 0.0, 100.0, 0.918, false],
        ['Aceites y grasas', 'Manteca de cerdo', null, 902, 0.0, 0.0, 100.0, 0.92, false],
        ['Condimentos y especias', 'Sal', 'sal de mesa', 0, 0.0, 0.0, 0.0, 1.2, false],
        ['Condimentos y especias', 'Pimienta negra', 'pimienta', 251, 10.4, 64.0, 3.3, 0.45, false],
        ['Condimentos y especias', 'Comino', null, 375, 17.8, 44.2, 22.3, 0.45, false],
        ['Condimentos y especias', 'Ají amarillo molido', 'ají molido', 282, 10.9, 55.0, 8.0, 0.5, false],
        ['Condimentos y especias', 'Ají colorado molido', 'ají panca', 282, 10.9, 55.0, 8.0, 0.5, false],
        ['Condimentos y especias', 'Ajo', null, 149, 6.4, 33.1, 0.5, 0.6, false],
        ['Condimentos y especias', 'Canela', null, 247, 4.0, 80.6, 1.2, 0.45, false],
        ['Condimentos y especias', 'Clavo de olor', null, 274, 6.0, 65.5, 13.0, null, false],
        ['Condimentos y especias', 'Anís', null, 337, 17.6, 50.0, 15.9, 0.4, false],
        ['Condimentos y especias', 'Palillo', 'cúrcuma', 312, 9.7, 67.1, 3.2, 0.55, false],
        ['Hierbas', 'Perejil', null, 36, 3.0, 6.3, 0.8, 0.25, false],
        ['Hierbas', 'Huacataya', 'huacatay', 44, 3.5, 7.0, 1.0, 0.25, false],
        ['Hierbas', 'Quirquiña', 'killkiña', 40, 3.0, 6.5, 0.9, 0.25, false],
        ['Hierbas', 'Orégano', null, 265, 9.0, 68.9, 4.3, 0.15, false],
        ['Hierbas', 'Laurel', null, 313, 7.6, 75.0, 8.4, 0.1, false],
        ['Endulzantes', 'Azúcar', 'azúcar blanca', 387, 0.0, 100.0, 0.0, 0.85, false],
        ['Endulzantes', 'Chancaca', 'panela,empanizao', 380, 0.5, 98.0, 0.1, 0.9, false],
        ['Endulzantes', 'Miel de abeja', 'miel', 304, 0.3, 82.4, 0.0, 1.4, false],
        ['Harinas y panaderia', 'Harina de trigo', 'harina', 364, 10.3, 76.3, 1.0, 0.55, true],
        ['Harinas y panaderia', 'Harina de maíz', null, 361, 6.9, 76.8, 3.9, 0.6, false],
        ['Harinas y panaderia', 'Pan molido', 'pan rallado,apanadura', 395, 13.4, 71.8, 5.3, 0.4, true],
        ['Harinas y panaderia', 'Marraqueta', 'pan', 277, 9.0, 53.0, 3.0, null, true],
        ['Harinas y panaderia', 'Levadura', null, 325, 40.4, 41.2, 7.6, 0.5, false],
        ['Harinas y panaderia', 'Almidón de maíz', 'maicena,chuño de maíz', 381, 0.3, 91.3, 0.1, 0.6, false],
        ['Salsas y aderezos', 'Vinagre', null, 21, 0.0, 0.9, 0.0, 1.01, false],
        ['Salsas y aderezos', 'Mayonesa', null, 680, 1.0, 0.6, 75.0, 0.95, true],
        ['Salsas y aderezos', 'Mostaza', null, 66, 4.4, 5.8, 3.3, 1.05, false],
        ['Enlatados y conservas', 'Arveja en lata', null, 60, 4.0, 10.0, 0.4, null, false],
        ['Enlatados y conservas', 'Durazno en almíbar', null, 74, 0.4, 19.9, 0.1, null, false],
        ['Enlatados y conservas', 'Leche condensada', null, 321, 7.9, 54.4, 8.7, 1.28, true],
        ['Bebidas', 'Agua', null, 0, 0.0, 0.0, 0.0, 1.0, false],
        ['Bebidas', 'Café', 'café molido', 1, 0.1, 0.0, 0.0, 0.4, false],
        ['Bebidas', 'Vino tinto', null, 85, 0.1, 2.6, 0.0, 0.99, false],
        ['Bebidas', 'Cerveza', null, 43, 0.5, 3.6, 0.0, 1.01, false],
    ];

    public function run(): void
    {
        $categorias = DB::table('categorias_ingrediente')
            ->pluck('cod_categoria_ingrediente', 'nom_cat');

        $faltantes = [];

        foreach (self::DATOS as [$categoria, $nombre, $sinonimos, $cal, $pro, $car, $gra, $densidad, $alergeno]) {
            if (! isset($categorias[$categoria])) {
                $faltantes[$categoria] = true;
                continue;
            }

            DB::table('ingredientes')->insertOrIgnore([
                'cod_categoria_ingrediente_ing' => $categorias[$categoria],
                'nom_ing' => $nombre,
                'sin_ing' => $sinonimos,
                'cal_ing' => $cal,
                'pro_ing' => $pro,
                'car_ing' => $car,
                'gra_ing' => $gra,
                'den_ing' => $densidad,
                'ale_ing' => $alergeno,
                'act_ing' => true,
            ]);
        }

        if ($faltantes) {
            $this->command->warn(
                'Estas categorías de ingrediente no existen, así que sus ingredientes no se cargaron: '
                . implode(', ', array_keys($faltantes))
            );
        }

        $this->command->info('Ingredientes en el catálogo: ' . DB::table('ingredientes')->count());
    }
}
