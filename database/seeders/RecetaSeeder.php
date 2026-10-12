<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Recetario boliviano de ejemplo.
 *
 * Cada receta se inserta con sus ingredientes, pasos y etiquetas dentro de
 * una transacción. Si algo falla, no queda una receta a medias.
 *
 * Depende de IngredienteSeeder: sin el catálogo cargado, las recetas no
 * encuentran sus ingredientes. El seeder avisa si eso pasa en lugar de
 * fallar en silencio.
 */
class RecetaSeeder extends Seeder
{
    /** Categorías de receta que estas recetas necesitan y pueden no existir. */
    private const CATEGORIAS_EXTRA = [
        ['Salsas y aderezos', 'Salsas, llajuas y aderezos que acompañan otros platos'],
    ];

    /** Etiquetas que estas recetas usan y pueden no existir. */
    private const ETIQUETAS_EXTRA = [
        ['Reconfortante', '#F97316'],
        ['Dulce', '#F472B6'],
        ['Familiar', '#38BDF8'],
    ];

    private const RECETAS = [
        [
            'nombre'      => 'Silpancho cochabambino',
            'categoria'   => 'Comida boliviana',
            'descripcion' => 'Plato emblemático de Cochabamba: una milanesa delgada de carne sobre arroz y papas fritas, coronada con huevo frito y una ensalada cruda encima.',
            'nivel'       => 'medio',
            'preparacion' => 25,
            'coccion'     => 25,
            'porciones'   => 4,
            'etiquetas'   => ['Tradicional', 'Alto en proteinas'],
            'ingredientes' => [
                ['Carne de res', 600, 'g', false, 'cortada en filetes delgados'],
                ['Pan molido', 200, 'g', false, null],
                ['Huevo de gallina', 6, 'pz', false, '2 para apanar, 4 para freír'],
                ['Arroz blanco', 2, 'tz', false, null],
                ['Papa', 800, 'g', false, 'cortada en bastones'],
                ['Tomate', 2, 'pz', false, 'picado en cubos'],
                ['Cebolla', 1, 'pz', false, 'picada fina'],
                ['Locoto', 1, 'pz', true, 'picado, para quien guste picante'],
                ['Aceite vegetal', 250, 'ml', false, 'para freír'],
                ['Sal', 2, 'cdta', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Cocer el arroz con agua y sal hasta que esté suelto. Reservar caliente.', 20, false],
                ['Golpear los filetes de carne hasta dejarlos muy delgados, casi transparentes.', 10, true],
                ['Sazonar la carne con sal y comino, pasarla por huevo batido y luego por pan molido.', 8, false],
                ['Freír las papas en aceite caliente hasta dorarlas. Escurrir sobre papel.', 12, false],
                ['Freír los filetes apanados hasta que estén dorados de ambos lados.', 8, true],
                ['Freír los huevos dejando la yema blanda.', 4, false],
                ['Picar el tomate, la cebolla y el locoto y mezclarlos con sal para la ensalada.', 5, false],
                ['Servir el arroz de base, encima la carne, las papas al costado, el huevo sobre la carne y la ensalada encima.', 3, false],
            ],
        ],
        [
            'nombre'      => 'Pique macho',
            'categoria'   => 'Comida boliviana',
            'descripcion' => 'Fuente compartida de carne y salchicha salteadas sobre papas fritas, con cebolla, tomate y locoto. Se come al centro de la mesa.',
            'nivel'       => 'medio',
            'preparacion' => 20,
            'coccion'     => 30,
            'porciones'   => 4,
            'etiquetas'   => ['Tradicional', 'Picante', 'Para llevar'],
            'ingredientes' => [
                ['Lomo de res', 700, 'g', false, 'en tiras'],
                ['Papa', 1, 'kg', false, 'en bastones gruesos'],
                ['Cebolla', 2, 'pz', false, 'en pluma'],
                ['Tomate', 3, 'pz', false, 'en gajos'],
                ['Locoto', 2, 'pz', false, 'en rodajas'],
                ['Huevo de gallina', 4, 'pz', false, 'duros, en cuartos'],
                ['Mostaza', 2, 'cda', false, null],
                ['Aceite vegetal', 200, 'ml', false, null],
                ['Sal', 2, 'cdta', false, null],
                ['Pimienta negra', 1, 'cdta', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Cocer los huevos en agua durante diez minutos y reservar.', 10, false],
                ['Freír las papas hasta que estén doradas y crocantes. Escurrir.', 15, false],
                ['Sazonar la carne con sal, pimienta y comino.', 3, false],
                ['Saltear la carne a fuego fuerte hasta sellarla por fuera.', 8, true],
                ['Agregar la cebolla y saltear dos minutos más, que quede firme.', 3, false],
                ['Sumar el tomate, el locoto y la mostaza. Cocinar un minuto.', 2, false],
                ['Montar las papas en la fuente, volcar encima el salteado y decorar con el huevo duro.', 4, false],
            ],
        ],
        [
            'nombre'      => 'Sopa de maní',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Sopa espesa y dorada, hecha con maní molido, carne de res y papas. Se sirve con papas fritas encima.',
            'nivel'       => 'medio',
            'preparacion' => 20,
            'coccion'     => 60,
            'porciones'   => 6,
            'etiquetas'   => ['Tradicional', 'Reconfortante'],
            'ingredientes' => [
                ['Maní', 250, 'g', false, 'pelado y tostado'],
                ['Carne de res', 500, 'g', false, 'en trozos con hueso'],
                ['Papa', 900, 'g', false, '600 g en mitades para la sopa, 300 g en bastones para freír encima'],
                ['Arveja fresca', 150, 'g', false, null],
                ['Zanahoria', 2, 'pz', false, 'en cubos'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Ají amarillo molido', 2, 'cda', false, null],
                ['Arroz blanco', 0.5, 'tz', false, null],
                ['Perejil', 3, 'cda', false, 'picado'],
                ['Sal', 1, 'cda', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Licuar el maní con dos tazas de agua hasta obtener una crema lisa sin grumos.', 8, true],
                ['Cocer la carne en tres litros de agua con sal durante media hora.', 30, false],
                ['Freír la cebolla con el ají amarillo y el comino hasta que suelte aroma.', 6, false],
                ['Incorporar el sofrito y el maní licuado al caldo, revolviendo para que no se corte.', 4, true],
                ['Agregar la zanahoria, la arveja y el arroz. Cocinar veinte minutos a fuego bajo.', 20, false],
                ['Sumar las papas y cocinar hasta que estén tiernas.', 15, false],
                ['Freír aparte las papas en bastones.', 10, false],
                ['Servir la sopa con perejil picado y las papas fritas encima.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Majadito de charque',
            'categoria'   => 'Comida boliviana',
            'descripcion' => 'Arroz cocido junto con charque desmenuzado y urucú, típico del oriente. Se acompaña con plátano frito y huevo.',
            'nivel'       => 'medio',
            'preparacion' => 25,
            'coccion'     => 35,
            'porciones'   => 4,
            'etiquetas'   => ['Tradicional', 'Economico'],
            'ingredientes' => [
                ['Charque', 400, 'g', false, 'remojado y desmenuzado'],
                ['Arroz blanco', 2, 'tz', false, null],
                ['Cebolla', 1, 'pz', false, 'picada fina'],
                ['Tomate', 2, 'pz', false, 'picado'],
                ['Pimiento morrón', 1, 'pz', false, 'picado'],
                ['Ajo', 3, 'dte', false, 'molido'],
                ['Plátano', 2, 'pz', false, 'maduros, en rodajas'],
                ['Huevo de gallina', 4, 'pz', false, null],
                ['Aceite vegetal', 100, 'ml', false, null],
                ['Palillo', 1, 'cdta', false, 'o urucú'],
                ['Sal', 2, 'cdta', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Remojar el charque en agua media hora, escurrirlo y desmenuzarlo con las manos.', 30, true],
                ['Freír el charque en aceite hasta que dore y quede suelto.', 8, false],
                ['Agregar cebolla, tomate, pimiento y ajo. Sofreír hasta que la cebolla esté transparente.', 6, false],
                ['Sumar el palillo, el comino y el arroz. Remover para que el grano tome color.', 3, true],
                ['Añadir cuatro tazas de agua caliente y sal. Cocer tapado a fuego bajo.', 20, false],
                ['Freír aparte los plátanos y los huevos.', 8, false],
                ['Servir el majadito con el plátano al costado y el huevo encima.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Chairo paceño',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Sopa andina de chuño molido, carne y verduras. Contundente, pensada para el frío del altiplano.',
            'nivel'       => 'dificil',
            'preparacion' => 30,
            'coccion'     => 90,
            'porciones'   => 6,
            'etiquetas'   => ['Tradicional', 'Reconfortante'],
            'ingredientes' => [
                ['Chuño', 200, 'g', false, 'remojado y molido'],
                ['Carne de cordero', 500, 'g', false, 'en trozos'],
                ['Carne de res', 300, 'g', false, 'con hueso'],
                ['Papa', 500, 'g', false, 'en cubos'],
                ['Zanahoria', 2, 'pz', false, 'en cubos'],
                ['Haba fresca', 200, 'g', true, 'peladas'],
                ['Trigo pelado', 100, 'g', false, 'remojado desde la víspera'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Ají amarillo molido', 2, 'cda', false, null],
                ['Huacataya', 2, 'cda', false, 'picada'],
                ['Sal', 1, 'cda', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Remojar el trigo y el chuño desde la noche anterior.', 0, true],
                ['Cocer las carnes en tres litros de agua con sal durante cuarenta minutos.', 40, false],
                ['Agregar el trigo remojado y cocinar media hora más.', 30, false],
                ['Freír la cebolla con el ají amarillo y el comino hasta formar un ahogado.', 6, false],
                ['Incorporar el ahogado, la zanahoria y las habas al caldo.', 3, false],
                ['Moler el chuño remojado y agregarlo de a poco, revolviendo para que no se apelmace.', 5, true],
                ['Sumar las papas y cocinar hasta que todo esté tierno.', 20, false],
                ['Terminar con la huacataya picada justo antes de servir.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Saice tarijeño',
            'categoria'   => 'Comida boliviana',
            'descripcion' => 'Guiso de carne molida con ají colorado y arveja, típico de Tarija. Se sirve con arroz y fideo.',
            'nivel'       => 'facil',
            'preparacion' => 15,
            'coccion'     => 35,
            'porciones'   => 4,
            'etiquetas'   => ['Tradicional', 'Facil'],
            'ingredientes' => [
                ['Carne molida de res', 600, 'g', false, null],
                ['Cebolla', 2, 'pz', false, 'picada fina'],
                ['Tomate', 2, 'pz', false, 'picado'],
                ['Arveja fresca', 200, 'g', false, null],
                ['Ají colorado molido', 3, 'cda', false, null],
                ['Papa', 500, 'g', false, 'en bastones para freír'],
                ['Arroz blanco', 1.5, 'tz', false, null],
                ['Aceite vegetal', 80, 'ml', false, null],
                ['Sal', 2, 'cdta', false, null],
                ['Comino', 1, 'cdta', false, null],
                ['Orégano', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Cocer el arroz con agua y sal.', 20, false],
                ['Freír la cebolla en aceite hasta que esté transparente.', 5, false],
                ['Agregar el ají colorado, el comino y el orégano. Freír dos minutos sin quemar.', 3, true],
                ['Incorporar la carne molida y deshacer los grumos con la cuchara.', 6, false],
                ['Sumar el tomate y un vaso de agua. Cocinar a fuego bajo.', 15, false],
                ['Añadir la arveja y cocinar hasta que esté tierna.', 8, false],
                ['Freír las papas aparte y servir el saice con arroz y papas fritas.', 12, false],
            ],
        ],
        [
            'nombre'      => 'Fricasé paceño',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Caldo picante de cerdo con mote de maíz y chuño, amarillo por el ají. Se come los domingos por la mañana.',
            'nivel'       => 'dificil',
            'preparacion' => 25,
            'coccion'     => 120,
            'porciones'   => 6,
            'etiquetas'   => ['Tradicional', 'Picante'],
            'ingredientes' => [
                ['Carne de cerdo', 1, 'kg', false, 'en trozos grandes'],
                ['Maíz pelado', 300, 'g', false, 'remojado desde la víspera'],
                ['Chuño', 200, 'g', false, 'remojado'],
                ['Ají amarillo molido', 4, 'cda', false, null],
                ['Cebolla', 2, 'pz', false, 'picada'],
                ['Ajo', 4, 'dte', false, 'molido'],
                ['Pan molido', 3, 'cda', false, 'para espesar'],
                ['Sal', 1, 'cda', false, null],
                ['Comino', 1, 'cdta', false, null],
                ['Orégano', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Remojar el maíz y el chuño desde la noche anterior.', 0, true],
                ['Cocer el maíz en abundante agua hasta que reviente.', 60, false],
                ['Freír la cebolla y el ajo, agregar el ají amarillo y el comino.', 8, true],
                ['Sellar el cerdo en el sofrito hasta dorarlo por fuera.', 10, false],
                ['Cubrir con tres litros de agua y cocinar a fuego medio.', 60, false],
                ['Agregar el chuño remojado y el orégano.', 15, false],
                ['Espesar con el pan molido disuelto en un poco de caldo.', 5, false],
                ['Servir el caldo con la carne, el mote y el chuño.', 3, false],
            ],
        ],
        [
            'nombre'      => 'Sopa de quinua',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Sopa ligera de quinua con verduras, sencilla de preparar y de alto valor nutritivo.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 30,
            'porciones'   => 4,
            'etiquetas'   => ['Saludable', 'Facil', 'Alto en proteinas'],
            'ingredientes' => [
                ['Quinua', 1, 'tz', false, 'lavada varias veces'],
                ['Zanahoria', 2, 'pz', false, 'en cubos'],
                ['Papa', 300, 'g', false, 'en cubos'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Apio', 2, 'pz', true, 'picado'],
                ['Perejil', 2, 'cda', false, 'picado'],
                ['Aceite vegetal', 2, 'cda', false, null],
                ['Sal', 2, 'cdta', false, null],
            ],
            'pasos' => [
                ['Lavar la quinua frotándola bajo el agua hasta que no salga espuma.', 5, true],
                ['Sofreír la cebolla en aceite hasta que esté transparente.', 4, false],
                ['Agregar dos litros de agua, la quinua y la sal. Llevar a hervor.', 8, false],
                ['Sumar la zanahoria, la papa y el apio.', 2, false],
                ['Cocinar a fuego medio hasta que la quinua abra su germen y las verduras estén tiernas.', 18, false],
                ['Servir con perejil picado por encima.', 1, false],
            ],
        ],
        [
            'nombre'      => 'Api con pastel',
            'categoria'   => 'Desayunos',
            'descripcion' => 'Bebida caliente de maíz morado con canela y clavo, acompañada de un pastel frito espolvoreado con azúcar.',
            'nivel'       => 'medio',
            'preparacion' => 20,
            'coccion'     => 40,
            'porciones'   => 6,
            'etiquetas'   => ['Tradicional', 'Dulce'],
            'ingredientes' => [
                ['Harina de maíz', 300, 'g', false, 'morada, remojada'],
                ['Azúcar', 250, 'g', false, null],
                ['Canela', 3, 'pz', false, 'en rama'],
                ['Clavo de olor', 5, 'pz', false, null],
                ['Limón', 1, 'pz', true, 'la cáscara'],
                ['Harina de trigo', 400, 'g', false, 'para el pastel'],
                ['Queso fresco', 150, 'g', false, 'para el relleno'],
                ['Aceite vegetal', 400, 'ml', false, 'para freír'],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Remojar la harina de maíz en agua durante dos horas.', 0, true],
                ['Hervir dos litros de agua con la canela, el clavo y la cáscara de limón.', 10, false],
                ['Colar el maíz remojado e incorporarlo al agua aromática removiendo sin parar.', 5, true],
                ['Cocinar a fuego bajo hasta que espese, unos treinta minutos, revolviendo seguido.', 30, true],
                ['Endulzar con el azúcar y retirar la canela y el clavo.', 3, false],
                ['Amasar la harina con agua tibia y sal hasta obtener una masa suave.', 12, false],
                ['Estirar la masa, colocar el queso, cerrar y freír en aceite caliente.', 12, false],
                ['Servir el api bien caliente con el pastel espolvoreado de azúcar.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Salteñas de pollo',
            'categoria'   => 'Snacks y aperitivos',
            'descripcion' => 'Empanada jugosa de horno rellena de pollo, papa y arveja en un jugo que cuaja al enfriarse.',
            'nivel'       => 'dificil',
            'preparacion' => 60,
            'coccion'     => 25,
            'porciones'   => 12,
            'etiquetas'   => ['Tradicional', 'Para llevar'],
            'ingredientes' => [
                ['Pechuga de pollo', 600, 'g', false, 'en cubos pequeños'],
                ['Harina de trigo', 1, 'kg', false, null],
                ['Manteca de cerdo', 200, 'g', false, null],
                ['Papa', 400, 'g', false, 'en cubos pequeños'],
                ['Arveja fresca', 200, 'g', false, null],
                ['Cebolla verde', 6, 'pz', false, 'picada'],
                ['Ají colorado molido', 3, 'cda', false, null],
                ['Huevo de gallina', 3, 'pz', false, 'duros, en rodajas'],
                ['Azúcar', 3, 'cda', false, 'para la masa'],
                ['Palillo', 1, 'cdta', false, null],
                ['Sal', 1, 'cda', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Cocer el pollo y reservar el caldo: ese caldo es el jugo de la salteña.', 20, true],
                ['Sofreír la cebolla verde con el ají colorado, el palillo y el comino.', 8, false],
                ['Agregar el pollo picado, la papa, la arveja y dos tazas del caldo.', 5, false],
                ['Cocinar hasta que espese y dejar enfriar por completo en la heladera.', 0, true],
                ['Amasar la harina con la manteca derretida, el azúcar, la sal y agua tibia.', 15, false],
                ['Estirar la masa y cortar discos de unos quince centímetros.', 15, false],
                ['Rellenar cada disco con el jugo frío y una rodaja de huevo.', 20, true],
                ['Repulgar el borde cerrando bien para que no se escape el jugo.', 15, true],
                ['Hornear a horno muy fuerte hasta que doren.', 20, false],
            ],
        ],
        [
            'nombre'      => 'Sopa de arveja',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Sopa casera de arveja verde con papa y un toque de queso, fácil y económica.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 35,
            'porciones'   => 4,
            'etiquetas'   => ['Economico', 'Facil', 'Saludable'],
            'ingredientes' => [
                ['Arveja fresca', 400, 'g', false, null],
                ['Papa', 400, 'g', false, 'en cubos'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Zanahoria', 1, 'pz', false, 'rallada'],
                ['Queso fresco', 100, 'g', true, 'en cubos, para servir'],
                ['Perejil', 2, 'cda', false, 'picado'],
                ['Aceite vegetal', 2, 'cda', false, null],
                ['Sal', 2, 'cdta', false, null],
            ],
            'pasos' => [
                ['Sofreír la cebolla y la zanahoria en aceite.', 5, false],
                ['Agregar dos litros de agua y la arveja. Llevar a hervor.', 8, false],
                ['Cocinar veinte minutos hasta que la arveja esté blanda.', 20, false],
                ['Sumar la papa y cocinar hasta que esté tierna.', 12, false],
                ['Servir con queso fresco en cubos y perejil.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Arroz con queso',
            'categoria'   => 'Guarniciones',
            'descripcion' => 'Arroz cremoso con queso fresco derretido, guarnición clásica del valle.',
            'nivel'       => 'facil',
            'preparacion' => 5,
            'coccion'     => 25,
            'porciones'   => 4,
            'etiquetas'   => ['Facil', 'Economico'],
            'ingredientes' => [
                ['Arroz blanco', 2, 'tz', false, null],
                ['Queso fresco', 250, 'g', false, 'desmenuzado'],
                ['Leche entera', 1, 'tz', false, null],
                ['Cebolla', 1, 'pz', false, 'picada fina'],
                ['Mantequilla', 2, 'cda', false, null],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Sofreír la cebolla en mantequilla hasta que esté transparente.', 4, false],
                ['Agregar el arroz y remover para que se impregne.', 2, false],
                ['Añadir cuatro tazas de agua caliente y sal. Cocer tapado.', 18, false],
                ['Incorporar la leche y el queso, removiendo hasta que se derrita.', 4, true],
                ['Dejar reposar tapado cinco minutos antes de servir.', 5, false],
            ],
        ],
        [
            'nombre'      => 'Ensalada de quinua',
            'categoria'   => 'Ensaladas',
            'descripcion' => 'Ensalada fría de quinua con verduras crudas y limón. Liviana y con buena carga de proteína.',
            'nivel'       => 'facil',
            'preparacion' => 15,
            'coccion'     => 20,
            'porciones'   => 4,
            'etiquetas'   => ['Saludable', 'Vegetariano', 'Alto en proteinas', 'Bajo en calorias'],
            'ingredientes' => [
                ['Quinua', 1, 'tz', false, 'lavada'],
                ['Tomate', 2, 'pz', false, 'en cubos'],
                ['Pepino', 1, 'pz', false, 'en cubos'],
                ['Cebolla', 0.5, 'pz', false, 'en pluma fina'],
                ['Palta', 1, 'pz', false, 'en cubos'],
                ['Limón', 2, 'pz', false, 'el jugo'],
                ['Aceite de oliva', 3, 'cda', false, null],
                ['Perejil', 3, 'cda', false, 'picado'],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Lavar la quinua y cocerla en dos tazas de agua hasta que abra el germen.', 18, false],
                ['Escurrir y dejar enfriar por completo.', 0, true],
                ['Picar todas las verduras en cubos parejos.', 10, false],
                ['Mezclar la quinua fría con las verduras.', 3, false],
                ['Aliñar con el jugo de limón, el aceite, la sal y el perejil.', 2, false],
                ['Agregar la palta al final para que no se deshaga.', 1, false],
            ],
        ],
        [
            'nombre'      => 'Sopa de fideo',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Sopa rápida de fideo con verduras, la que se hace cuando hay poco tiempo.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 25,
            'porciones'   => 4,
            'etiquetas'   => ['Rapido', 'Economico', 'Facil'],
            'ingredientes' => [
                ['Fideo', 200, 'g', false, null],
                ['Zanahoria', 2, 'pz', false, 'en cubos'],
                ['Papa', 300, 'g', false, 'en cubos'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Apio', 1, 'pz', true, 'picado'],
                ['Perejil', 2, 'cda', false, 'picado'],
                ['Aceite vegetal', 2, 'cda', false, null],
                ['Sal', 2, 'cdta', false, null],
            ],
            'pasos' => [
                ['Sofreír la cebolla en aceite.', 4, false],
                ['Agregar dos litros de agua con sal y llevar a hervor.', 8, false],
                ['Sumar la zanahoria, la papa y el apio. Cocinar quince minutos.', 15, false],
                ['Añadir el fideo y cocinar hasta que esté a punto.', 8, true],
                ['Servir con perejil picado.', 1, false],
            ],
        ],
        [
            'nombre'      => 'Pollo al horno con papas',
            'categoria'   => 'Platos principales',
            'descripcion' => 'Pollo entero horneado con papas doradas alrededor. Se prepara solo una vez que entra al horno.',
            'nivel'       => 'facil',
            'preparacion' => 15,
            'coccion'     => 70,
            'porciones'   => 6,
            'etiquetas'   => ['Facil', 'Familiar'],
            'ingredientes' => [
                ['Pollo entero', 1.8, 'kg', false, 'trozado'],
                ['Papa', 1, 'kg', false, 'en cuartos'],
                ['Cebolla', 2, 'pz', false, 'en gajos'],
                ['Ajo', 6, 'dte', false, 'molido'],
                ['Limón', 2, 'pz', false, 'el jugo'],
                ['Aceite vegetal', 60, 'ml', false, null],
                ['Orégano', 1, 'cda', false, null],
                ['Sal', 1, 'cda', false, null],
                ['Pimienta negra', 1, 'cdta', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Mezclar el ajo, el limón, el aceite, el orégano, la sal, la pimienta y el comino.', 5, false],
                ['Untar el pollo con esa mezcla y dejarlo reposar al menos treinta minutos.', 0, true],
                ['Acomodar el pollo en una fuente con las papas y la cebolla alrededor.', 5, false],
                ['Hornear a fuego medio durante cuarenta minutos.', 40, false],
                ['Dar vuelta las papas y subir el fuego para dorar todo.', 25, false],
                ['Dejar reposar cinco minutos antes de trozar.', 5, false],
            ],
        ],
        [
            'nombre'      => 'Picante de pollo',
            'categoria'   => 'Platos principales',
            'descripcion' => 'Pollo guisado en ají colorado, servido con papa y chuño. Plato de fiesta en el altiplano.',
            'nivel'       => 'medio',
            'preparacion' => 20,
            'coccion'     => 45,
            'porciones'   => 5,
            'etiquetas'   => ['Tradicional', 'Picante'],
            'ingredientes' => [
                ['Pollo entero', 1.5, 'kg', false, 'en presas'],
                ['Ají colorado molido', 4, 'cda', false, null],
                ['Cebolla', 2, 'pz', false, 'picada fina'],
                ['Ajo', 4, 'dte', false, 'molido'],
                ['Papa', 700, 'g', false, 'enteras pequeñas'],
                ['Chuño', 200, 'g', false, 'remojado'],
                ['Arveja fresca', 150, 'g', true, null],
                ['Aceite vegetal', 80, 'ml', false, null],
                ['Sal', 1, 'cda', false, null],
                ['Comino', 1, 'cdta', false, null],
                ['Perejil', 3, 'cda', false, 'picado'],
            ],
            'pasos' => [
                ['Remojar el chuño desde la noche anterior y cocerlo aparte.', 25, true],
                ['Sofreír la cebolla y el ajo hasta que estén transparentes.', 6, false],
                ['Agregar el ají colorado y el comino. Freír dos minutos sin quemar.', 3, true],
                ['Sellar las presas de pollo en el ahogado.', 8, false],
                ['Cubrir con caldo o agua y cocinar a fuego medio.', 30, false],
                ['Cocer las papas aparte hasta que estén tiernas.', 20, false],
                ['Servir el pollo con su salsa, la papa y el chuño. Terminar con perejil.', 3, false],
            ],
        ],
        [
            'nombre'      => 'Sopa de verduras',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Caldo de verduras de temporada, sin carne. Liviana y apta para vegetarianos.',
            'nivel'       => 'facil',
            'preparacion' => 15,
            'coccion'     => 30,
            'porciones'   => 4,
            'etiquetas'   => ['Vegetariano', 'Saludable', 'Bajo en calorias'],
            'ingredientes' => [
                ['Zanahoria', 2, 'pz', false, 'en cubos'],
                ['Papa', 300, 'g', false, 'en cubos'],
                ['Zapallo', 300, 'g', false, 'en cubos'],
                ['Vainita', 150, 'g', false, 'en trozos'],
                ['Choclo', 1, 'pz', false, 'en rodajas'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Apio', 2, 'pz', false, 'picado'],
                ['Perejil', 3, 'cda', false, 'picado'],
                ['Aceite vegetal', 2, 'cda', false, null],
                ['Sal', 2, 'cdta', false, null],
            ],
            'pasos' => [
                ['Sofreír la cebolla y el apio en aceite.', 5, false],
                ['Agregar dos litros de agua con sal.', 3, false],
                ['Sumar el choclo y la zanahoria, que tardan más en cocerse.', 12, false],
                ['Añadir el zapallo, la vainita y la papa.', 15, false],
                ['Servir con perejil picado por encima.', 1, false],
            ],
        ],
        [
            'nombre'      => 'Arroz chaufa boliviano',
            'categoria'   => 'Platos principales',
            'descripcion' => 'Arroz salteado con pollo, huevo y cebolla verde. Versión local del plato chino, muy común en almuerzos.',
            'nivel'       => 'facil',
            'preparacion' => 15,
            'coccion'     => 20,
            'porciones'   => 4,
            'etiquetas'   => ['Rapido', 'Familiar'],
            'ingredientes' => [
                ['Arroz blanco', 3, 'tz', false, 'cocido del día anterior'],
                ['Pechuga de pollo', 400, 'g', false, 'en cubos'],
                ['Huevo de gallina', 3, 'pz', false, 'batidos'],
                ['Cebolla verde', 6, 'pz', false, 'picada'],
                ['Pimiento morrón', 1, 'pz', false, 'en tiras'],
                ['Ajo', 3, 'dte', false, 'molido'],
                ['Aceite vegetal', 4, 'cda', false, null],
                ['Sal', 2, 'cdta', false, null],
                ['Pimienta negra', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Usar arroz frío del día anterior: el recién hecho se apelmaza.', 0, true],
                ['Hacer una tortilla delgada con los huevos batidos y cortarla en tiras.', 5, false],
                ['Saltear el pollo a fuego fuerte con el ajo hasta dorarlo.', 7, false],
                ['Agregar el pimiento y la parte blanca de la cebolla verde.', 3, false],
                ['Sumar el arroz y saltear removiendo, sin aplastarlo.', 5, true],
                ['Incorporar la tortilla en tiras y la parte verde de la cebolla.', 2, false],
                ['Sazonar con sal y pimienta y servir enseguida.', 1, false],
            ],
        ],
        [
            'nombre'      => 'Papas a la huancaína',
            'categoria'   => 'Snacks y aperitivos',
            'descripcion' => 'Papas cocidas bañadas en una crema de ají amarillo y queso. Se sirve frío, sobre lechuga.',
            'nivel'       => 'facil',
            'preparacion' => 15,
            'coccion'     => 20,
            'porciones'   => 4,
            'etiquetas'   => ['Vegetariano', 'Facil', 'Picante'],
            'ingredientes' => [
                ['Papa', 800, 'g', false, 'enteras'],
                ['Queso fresco', 250, 'g', false, null],
                ['Ají amarillo molido', 3, 'cda', false, null],
                ['Leche evaporada', 200, 'ml', false, null],
                ['Aceite vegetal', 4, 'cda', false, null],
                ['Huevo de gallina', 2, 'pz', false, 'duros, en rodajas'],
                ['Lechuga', 4, 'pz', false, 'hojas enteras'],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Cocer las papas con cáscara en agua con sal hasta que estén tiernas.', 20, false],
                ['Cocer los huevos diez minutos y reservar.', 10, false],
                ['Licuar el queso, el ají amarillo, la leche, el aceite y la sal hasta obtener una crema lisa.', 5, true],
                ['Pelar las papas y cortarlas en rodajas gruesas.', 5, false],
                ['Acomodar las hojas de lechuga en el plato y sobre ellas las papas.', 3, false],
                ['Bañar con la crema y decorar con el huevo duro.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Ensalada de tomate y cebolla',
            'categoria'   => 'Ensaladas',
            'descripcion' => 'La ensalada que acompaña casi todo en Bolivia: tomate, cebolla, sal y limón.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 0,
            'porciones'   => 4,
            'etiquetas'   => ['Vegano', 'Rapido', 'Economico', 'Bajo en calorias'],
            'ingredientes' => [
                ['Tomate', 4, 'pz', false, 'en gajos'],
                ['Cebolla', 1, 'pz', false, 'en pluma fina'],
                ['Limón', 2, 'pz', false, 'el jugo'],
                ['Locoto', 1, 'pz', true, 'picado'],
                ['Perejil', 2, 'cda', true, 'picado'],
                ['Aceite vegetal', 2, 'cda', false, null],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Cortar la cebolla en pluma fina y dejarla cinco minutos en agua fría con sal para que pierda el filo.', 5, false],
                ['Cortar el tomate en gajos.', 4, false],
                ['Escurrir la cebolla y mezclarla con el tomate.', 1, false],
                ['Aliñar con limón, aceite y sal justo antes de servir.', 2, true],
            ],
        ],
        [
            'nombre'      => 'Jugo de maracuyá',
            'categoria'   => 'Bebidas',
            'descripcion' => 'Refresco frío de maracuyá, ácido y dulce a la vez.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 0,
            'porciones'   => 4,
            'etiquetas'   => ['Rapido', 'Facil', 'Saludable'],
            'ingredientes' => [
                ['Maracuyá', 6, 'pz', false, 'la pulpa'],
                ['Agua', 1.5, 'l', false, 'fría'],
                ['Azúcar', 150, 'g', false, null],
            ],
            'pasos' => [
                ['Abrir los maracuyás y sacar toda la pulpa con una cuchara.', 5, false],
                ['Licuar la pulpa con medio litro de agua unos segundos, sin romper las pepas.', 1, true],
                ['Colar para separar las semillas.', 3, false],
                ['Mezclar con el resto del agua y endulzar al gusto.', 2, false],
                ['Servir bien frío.', 1, false],
            ],
        ],
        [
            'nombre'      => 'Mocochinchi',
            'categoria'   => 'Bebidas',
            'descripcion' => 'Refresco de durazno deshidratado cocido con canela y clavo, se toma helado.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 45,
            'porciones'   => 8,
            'etiquetas'   => ['Tradicional', 'Economico'],
            'ingredientes' => [
                ['Durazno', 300, 'g', false, 'deshidratado'],
                ['Azúcar', 250, 'g', false, null],
                ['Canela', 3, 'pz', false, 'en rama'],
                ['Clavo de olor', 5, 'pz', false, null],
                ['Agua', 3, 'l', false, null],
            ],
            'pasos' => [
                ['Remojar los duraznos deshidratados durante dos horas.', 0, true],
                ['Hervir el agua con la canela y el clavo.', 10, false],
                ['Agregar los duraznos escurridos y cocinar a fuego bajo.', 40, false],
                ['Endulzar con el azúcar y dejar enfriar.', 5, false],
                ['Servir frío con un durazno entero en cada vaso.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Humintas al horno',
            'categoria'   => 'Snacks y aperitivos',
            'descripcion' => 'Pasta de choclo con queso, envuelta en su propia chala y horneada.',
            'nivel'       => 'medio',
            'preparacion' => 40,
            'coccion'     => 50,
            'porciones'   => 8,
            'etiquetas'   => ['Tradicional', 'Vegetariano'],
            'ingredientes' => [
                ['Choclo', 8, 'pz', false, 'con su chala'],
                ['Queso fresco', 300, 'g', false, 'en cubos'],
                ['Mantequilla', 100, 'g', false, 'derretida'],
                ['Azúcar', 4, 'cda', false, null],
                ['Canela', 1, 'cdta', false, 'molida'],
                ['Leche entera', 100, 'ml', true, null],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Deshojar los choclos con cuidado y reservar las chalas enteras.', 15, true],
                ['Desgranar los choclos y molerlos hasta obtener una pasta.', 15, false],
                ['Mezclar la pasta con la mantequilla, el azúcar, la sal y la canela.', 5, false],
                ['Si la masa está muy espesa, aflojar con un poco de leche.', 2, false],
                ['Poner una porción de masa sobre dos chalas cruzadas, colocar el queso y cerrar.', 20, true],
                ['Atar cada huminta con una tira de chala.', 10, false],
                ['Hornear a fuego medio hasta que la masa cuaje.', 50, false],
            ],
        ],
        [
            'nombre'      => 'Sopa de lenteja',
            'categoria'   => 'Sopas y caldos',
            'descripcion' => 'Sopa espesa de lenteja con verduras, barata y muy nutritiva.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 45,
            'porciones'   => 5,
            'etiquetas'   => ['Economico', 'Saludable', 'Vegetariano', 'Alto en proteinas'],
            'ingredientes' => [
                ['Lenteja', 400, 'g', false, 'remojada'],
                ['Zanahoria', 2, 'pz', false, 'en cubos'],
                ['Papa', 400, 'g', false, 'en cubos'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Tomate', 2, 'pz', false, 'picado'],
                ['Ajo', 3, 'dte', false, 'molido'],
                ['Aceite vegetal', 3, 'cda', false, null],
                ['Sal', 2, 'cdta', false, null],
                ['Comino', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Remojar la lenteja al menos dos horas.', 0, true],
                ['Sofreír la cebolla, el ajo y el tomate con el comino.', 6, false],
                ['Agregar dos litros y medio de agua y la lenteja escurrida.', 3, false],
                ['Cocinar a fuego medio treinta minutos.', 30, false],
                ['Sumar la zanahoria y la papa y cocinar hasta que estén tiernas.', 15, false],
            ],
        ],
        [
            'nombre'      => 'Charquekán orureño',
            'categoria'   => 'Comida boliviana',
            'descripcion' => 'Charque de llama desmenuzado y frito, servido con mote, papa y queso. Plato de Oruro.',
            'nivel'       => 'medio',
            'preparacion' => 30,
            'coccion'     => 40,
            'porciones'   => 4,
            'etiquetas'   => ['Tradicional', 'Alto en proteinas'],
            'ingredientes' => [
                ['Charque', 500, 'g', false, 'de llama, remojado'],
                ['Maíz pelado', 300, 'g', false, 'remojado desde la víspera'],
                ['Papa', 600, 'g', false, 'enteras'],
                ['Queso fresco', 200, 'g', false, 'en rodajas'],
                ['Huevo de gallina', 4, 'pz', false, 'duros'],
                ['Locoto', 2, 'pz', true, 'para la llajua'],
                ['Tomate', 2, 'pz', true, 'para la llajua'],
                ['Aceite vegetal', 100, 'ml', false, null],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Remojar el charque en agua durante una hora, cambiando el agua dos veces.', 0, true],
                ['Cocer el maíz pelado hasta que reviente.', 40, false],
                ['Golpear el charque para ablandarlo y desmenuzarlo con las manos.', 15, true],
                ['Freír el charque desmenuzado hasta que quede crocante.', 10, false],
                ['Cocer las papas con cáscara y los huevos.', 20, false],
                ['Moler el tomate con el locoto para hacer la llajua.', 5, false],
                ['Servir el charque con el mote, la papa, el queso y el huevo. La llajua aparte.', 3, false],
            ],
        ],
        [
            'nombre'      => 'Llajua',
            'categoria'   => 'Salsas y aderezos',
            'descripcion' => 'Salsa picante de locoto y tomate que acompaña casi cualquier plato boliviano.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 0,
            'porciones'   => 8,
            'etiquetas'   => ['Tradicional', 'Picante', 'Vegano', 'Rapido'],
            'ingredientes' => [
                ['Locoto', 3, 'pz', false, 'sin venas si se quiere menos picante'],
                ['Tomate', 3, 'pz', false, null],
                ['Quirquiña', 3, 'cda', true, 'picada'],
                ['Huacataya', 2, 'cda', true, 'picada'],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Lavar los locotos y quitarles las venas si se busca un picor suave.', 4, false],
                ['Moler los locotos con sal en batán o licuadora, sin hacerlos puré.', 3, true],
                ['Agregar el tomate y moler apenas, la llajua debe quedar con textura.', 3, false],
                ['Incorporar la quirquiña y la huacataya picadas.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Tortilla de acelga',
            'categoria'   => 'Cenas',
            'descripcion' => 'Tortilla de huevo con acelga salteada y queso, rápida para la noche.',
            'nivel'       => 'facil',
            'preparacion' => 10,
            'coccion'     => 15,
            'porciones'   => 3,
            'etiquetas'   => ['Vegetariano', 'Rapido', 'Saludable'],
            'ingredientes' => [
                ['Acelga', 400, 'g', false, 'en tiras'],
                ['Huevo de gallina', 6, 'pz', false, 'batidos'],
                ['Cebolla', 1, 'pz', false, 'picada'],
                ['Queso fresco', 100, 'g', true, 'desmenuzado'],
                ['Aceite vegetal', 3, 'cda', false, null],
                ['Sal', 1, 'cdta', false, null],
                ['Pimienta negra', 0.5, 'cdta', false, null],
            ],
            'pasos' => [
                ['Lavar la acelga y cortarla en tiras finas.', 5, false],
                ['Sofreír la cebolla y agregar la acelga hasta que reduzca.', 6, false],
                ['Batir los huevos con sal y pimienta.', 2, false],
                ['Mezclar la acelga escurrida con el huevo y el queso.', 2, true],
                ['Cocinar a fuego bajo en sartén tapada hasta que cuaje.', 8, false],
                ['Dar vuelta con ayuda de un plato y dorar del otro lado.', 3, false],
            ],
        ],
        [
            'nombre'      => 'Arroz con leche',
            'categoria'   => 'Postres',
            'descripcion' => 'Postre de arroz cocido en leche con canela, servido tibio o frío.',
            'nivel'       => 'facil',
            'preparacion' => 5,
            'coccion'     => 40,
            'porciones'   => 6,
            'etiquetas'   => ['Dulce', 'Facil', 'Familiar'],
            'ingredientes' => [
                ['Arroz blanco', 1, 'tz', false, null],
                ['Leche entera', 1.5, 'l', false, null],
                ['Azúcar', 200, 'g', false, null],
                ['Canela', 2, 'pz', false, 'en rama'],
                ['Limón', 1, 'pz', true, 'la cáscara'],
                ['Leche condensada', 100, 'ml', true, null],
            ],
            'pasos' => [
                ['Hervir el arroz en dos tazas de agua cinco minutos y escurrir.', 8, false],
                ['Llevar la leche a hervor con la canela y la cáscara de limón.', 8, false],
                ['Agregar el arroz y cocinar a fuego muy bajo, revolviendo seguido.', 30, true],
                ['Endulzar con el azúcar cuando el arroz ya esté blando.', 3, true],
                ['Retirar la canela y la cáscara. Servir tibio o frío.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Buñuelos',
            'categoria'   => 'Postres',
            'descripcion' => 'Masa frita esponjosa bañada en miel de chancaca, típica de fiestas y ferias.',
            'nivel'       => 'medio',
            'preparacion' => 90,
            'coccion'     => 20,
            'porciones'   => 8,
            'etiquetas'   => ['Tradicional', 'Dulce'],
            'ingredientes' => [
                ['Harina de trigo', 500, 'g', false, null],
                ['Levadura', 20, 'g', false, null],
                ['Azúcar', 50, 'g', false, null],
                ['Huevo de gallina', 2, 'pz', false, null],
                ['Aceite vegetal', 500, 'ml', false, 'para freír'],
                ['Chancaca', 250, 'g', false, 'para la miel'],
                ['Canela', 2, 'pz', false, 'en rama'],
                ['Anís', 1, 'cdta', true, null],
                ['Sal', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Disolver la levadura en agua tibia con una cucharada de azúcar y dejar espumar.', 10, true],
                ['Mezclar la harina con la sal, el azúcar, los huevos y la levadura activada.', 10, false],
                ['Amasar hasta obtener una masa blanda y pegajosa.', 12, false],
                ['Tapar y dejar leudar hasta que doble su volumen.', 0, true],
                ['Hervir la chancaca con agua, canela y anís hasta formar una miel.', 15, false],
                ['Estirar porciones de masa con las manos aceitadas y freír en aceite caliente.', 15, true],
                ['Escurrir sobre papel y bañar con la miel de chancaca.', 5, false],
            ],
        ],
        [
            'nombre'      => 'Ensalada de frutas',
            'categoria'   => 'Postres',
            'descripcion' => 'Frutas de temporada picadas con jugo de naranja. Fresca y sin cocción.',
            'nivel'       => 'facil',
            'preparacion' => 15,
            'coccion'     => 0,
            'porciones'   => 4,
            'etiquetas'   => ['Saludable', 'Vegano', 'Rapido', 'Bajo en calorias'],
            'ingredientes' => [
                ['Plátano', 2, 'pz', false, 'en rodajas'],
                ['Manzana', 2, 'pz', false, 'en cubos'],
                ['Papaya', 300, 'g', false, 'en cubos'],
                ['Piña', 300, 'g', false, 'en cubos'],
                ['Naranja', 3, 'pz', false, 'el jugo'],
                ['Frutilla', 200, 'g', true, 'en mitades'],
                ['Miel de abeja', 2, 'cda', true, null],
            ],
            'pasos' => [
                ['Exprimir las naranjas y reservar el jugo.', 5, false],
                ['Picar todas las frutas en cubos de tamaño parejo.', 10, false],
                ['Mezclar las frutas con el jugo de naranja, que evita que se oxiden.', 2, true],
                ['Endulzar con miel si hace falta y servir frío.', 2, false],
            ],
        ],
        [
            'nombre'      => 'Sándwich de chola',
            'categoria'   => 'Snacks y aperitivos',
            'descripcion' => 'Marraqueta rellena de cerdo horneado con su cuero crocante, escabeche de cebolla y llajua.',
            'nivel'       => 'medio',
            'preparacion' => 20,
            'coccion'     => 90,
            'porciones'   => 6,
            'etiquetas'   => ['Tradicional', 'Para llevar'],
            'ingredientes' => [
                ['Carne de cerdo', 1.2, 'kg', false, 'pierna con cuero'],
                ['Marraqueta', 6, 'pz', false, null],
                ['Cebolla', 3, 'pz', false, 'en pluma, para el escabeche'],
                ['Zanahoria', 2, 'pz', false, 'rallada'],
                ['Vinagre', 100, 'ml', false, null],
                ['Locoto', 2, 'pz', true, 'para la llajua'],
                ['Ajo', 5, 'dte', false, 'molido'],
                ['Sal', 1, 'cda', false, null],
                ['Comino', 1, 'cdta', false, null],
                ['Orégano', 1, 'cdta', false, null],
            ],
            'pasos' => [
                ['Untar la pierna con ajo, sal, comino y orégano. Dejar reposar una hora.', 0, true],
                ['Hornear a fuego medio cubierta con papel durante una hora.', 60, false],
                ['Destapar y subir el fuego para que el cuero quede crocante.', 30, true],
                ['Mezclar la cebolla y la zanahoria con vinagre y sal para el escabeche.', 10, false],
                ['Dejar reposar la carne diez minutos y cortarla en láminas.', 10, false],
                ['Abrir las marraquetas, rellenar con la carne, el escabeche y la llajua.', 8, false],
            ],
        ],
    ];

    public function run(): void
    {
        $this->completarCatalogos();

        $categorias   = DB::table('categorias_receta')->pluck('cod_categoria_receta', 'nom_car');
        $ingredientes = DB::table('ingredientes')->pluck('cod_ingrediente', 'nom_ing');
        $unidades     = DB::table('unidades_medida')->pluck('cod_unidad', 'abr_uni');
        $etiquetas    = DB::table('etiquetas')->pluck('cod_etiqueta', 'nom_eti');

        if ($ingredientes->isEmpty()) {
            $this->command->error('El catálogo de ingredientes está vacío. Corre primero IngredienteSeeder.');
            return;
        }

        $cargadas = 0;
        $omitidas = [];

        foreach (self::RECETAS as $receta) {
            // Si ya existe una receta activa con ese nombre, no se duplica.
            $existe = DB::table('recetas')
                ->where('nom_rec', $receta['nombre'])
                ->whereNull('fec_eli_rec')
                ->exists();

            if ($existe) {
                continue;
            }

            if (! isset($categorias[$receta['categoria']])) {
                $omitidas[] = "{$receta['nombre']} (falta la categoría «{$receta['categoria']}»)";
                continue;
            }

            // Se comprueba antes de abrir la transacción: es más claro avisar
            // qué falta que dejar la receta a medio cargar.
            $sinCatalogo = [];
            foreach ($receta['ingredientes'] as [$nombre, , $abrev]) {
                if (! isset($ingredientes[$nombre])) $sinCatalogo[] = $nombre;
                if (! isset($unidades[$abrev]))      $sinCatalogo[] = "unidad {$abrev}";
            }

            if ($sinCatalogo) {
                $omitidas[] = "{$receta['nombre']} (falta: " . implode(', ', array_unique($sinCatalogo)) . ')';
                continue;
            }

            DB::transaction(function () use ($receta, $categorias, $ingredientes, $unidades, $etiquetas) {
                $codigo = DB::table('recetas')->insertGetId([
                    'cod_categoria_receta_rec' => $categorias[$receta['categoria']],
                    'nom_rec'     => $receta['nombre'],
                    'des_rec'     => $receta['descripcion'],
                    'tie_pre_rec' => $receta['preparacion'],
                    'tie_coc_rec' => $receta['coccion'],
                    'por_rec'     => $receta['porciones'],
                    'niv_rec'     => $receta['nivel'],
                    'ori_rec'     => 'sistema',
                    'est_val_rec' => 'pendiente',
                    'ver_rec'     => false,
                    'act_rec'     => true,
                ], 'cod_receta');

                foreach ($receta['ingredientes'] as $orden => [$nombre, $cantidad, $abrev, $opcional, $nota]) {
                    DB::table('ingredientes_receta')->insert([
                        'cod_receta_inr'      => $codigo,
                        'cod_ingrediente_inr' => $ingredientes[$nombre],
                        'cod_unidad_inr'      => $unidades[$abrev],
                        'can_inr'             => $cantidad,
                        'opc_inr'             => $opcional,
                        'not_inr'             => $nota,
                        'ord_inr'             => $orden + 1,
                    ]);
                }

                foreach ($receta['pasos'] as $i => [$instruccion, $minutos, $critico]) {
                    DB::table('pasos_receta')->insert([
                        'cod_receta_pas' => $codigo,
                        'num_pas'        => $i + 1,
                        'ins_pas'        => $instruccion,
                        'tie_pas'        => $minutos ?: null,
                        'cri_pas'        => $critico,
                    ]);
                }

                foreach ($receta['etiquetas'] as $etiqueta) {
                    if (isset($etiquetas[$etiqueta])) {
                        DB::table('receta_etiquetas')->insertOrIgnore([
                            'cod_receta_ret'   => $codigo,
                            'cod_etiqueta_ret' => $etiquetas[$etiqueta],
                        ]);
                    }
                }
            });

            $cargadas++;
        }

        $this->command->info("Recetas cargadas: {$cargadas}");

        foreach ($omitidas as $omitida) {
            $this->command->warn("Omitida: {$omitida}");
        }
    }

    /** Agrega las categorías y etiquetas que estas recetas necesitan. */
    private function completarCatalogos(): void
    {
        foreach (self::CATEGORIAS_EXTRA as [$nombre, $descripcion]) {
            DB::table('categorias_receta')->insertOrIgnore([
                'nom_car' => $nombre, 'des_car' => $descripcion, 'act_car' => true,
            ]);
        }

        foreach (self::ETIQUETAS_EXTRA as [$nombre, $color]) {
            DB::table('etiquetas')->insertOrIgnore([
                'nom_eti' => $nombre, 'col_eti' => $color, 'act_eti' => true,
            ]);
        }
    }
}
