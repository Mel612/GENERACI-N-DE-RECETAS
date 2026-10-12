<?php

use App\Http\Controllers\Admin\CategoriaIngredienteController;
use App\Http\Controllers\Admin\CategoriaRecetaController;
use App\Http\Controllers\Admin\EtiquetaController;
use App\Http\Controllers\Admin\IngredienteController;
use App\Http\Controllers\Admin\PreferenciaDietaController;
use App\Http\Controllers\Admin\RecetaController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\UnidadMedidaController;
use App\Http\Controllers\Admin\ValoracionController;
use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RecuperacionController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Nutricionista\ValidacionController;
use App\Http\Controllers\AprovechamientoController;
use App\Http\Controllers\AsistenteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneradorController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\RecetaUsuarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::view('/', 'bienvenida')->name('inicio');

Route::middleware('guest')->group(function () {
    // Historia 2 - Inicio de sesión
    Route::get('/login', [LoginController::class, 'mostrar'])->name('login');
    Route::post('/login', [LoginController::class, 'autenticar'])->name('login.autenticar');

    // Historia 1 - Registro de usuario
    Route::get('/registro', [RegistroController::class, 'mostrar'])->name('registro');
    Route::post('/registro', [RegistroController::class, 'registrar'])->name('registro.guardar');

    // Recuperación de contraseña (complementa la Historia 2)
    Route::get('/olvide-password',  [RecuperacionController::class, 'solicitar'])->name('password.solicitar');
    Route::post('/olvide-password', [RecuperacionController::class, 'enviar'])->name('password.enviar');

    // El token va en la URL; el correo viaja como parámetro de consulta
    Route::get('/restablecer/{token}', [RecuperacionController::class, 'restablecer'])->name('password.restablecer');
    Route::post('/restablecer',        [RecuperacionController::class, 'guardar'])->name('password.guardar');
});

/*
|--------------------------------------------------------------------------
| Rutas autenticadas, para cualquier rol
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'salir'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Historia 18 - Editar perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'cambiarPassword'])->name('perfil.password');

    /*
    |----------------------------------------------------------------------
    | Mi cocina: lo que usa quien cocina
    |----------------------------------------------------------------------
    |
    | Disponible para cualquier cuenta: el administrador y el nutricionista
    | también tienen su propia despensa.
    |
    */

    // Historia 3 - Inventario (Requerimientos 4, 11 y 12)
    Route::get('/despensa',  [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/despensa', [InventarioController::class, 'store'])->name('inventario.store');
    Route::put('/despensa/{inventario}',    [InventarioController::class, 'update'])->name('inventario.update');
    Route::delete('/despensa/{inventario}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
    Route::post('/despensa/vaciar',         [InventarioController::class, 'vaciar'])->name('inventario.vaciar');
    Route::post('/despensa/ingrediente-ia', [InventarioController::class, 'identificarConIa'])
         ->middleware('throttle:10,1')->name('inventario.ia');

    // Aprovechamiento: qué se consumió y qué se desperdició de la despensa
    Route::get('/aprovechamiento', [AprovechamientoController::class, 'index'])->name('aprovechamiento.index');

    // Historias 4, 5, 16 y 17 - Generacion de recetas (Requerimientos 5, 6, 7, 13 y 14)
    Route::get('/generar',  [GeneradorController::class, 'index'])->name('generador.index');
    Route::post('/generar', [GeneradorController::class, 'generar'])->name('generador.generar');

    // Historia 7 - Historial de consultas (Requerimiento 15)
    Route::get('/mis-busquedas', [GeneradorController::class, 'historial'])->name('generador.historial');

    // Asistente con inteligencia artificial (Gemini)
    Route::post('/asistente',           [AsistenteController::class, 'proponer'])->name('asistente.proponer');
    Route::get('/asistente/propuesta',  [AsistenteController::class, 'revisar'])->name('asistente.revisar');
    Route::post('/asistente/guardar',   [AsistenteController::class, 'guardar'])->name('asistente.guardar');
    Route::post('/asistente/descartar', [AsistenteController::class, 'descartar'])->name('asistente.descartar');

    // Historias 6 y 8 - Ver recetas, favoritas y valoraciones (Requerimiento 19)
    Route::get('/recetas',            [RecetaUsuarioController::class, 'index'])->name('recetas.index');
    Route::get('/recetas/favoritas',  [RecetaUsuarioController::class, 'favoritas'])->name('recetas.favoritas');
    Route::get('/recetas/{receta}',   [RecetaUsuarioController::class, 'show'])->name('recetas.show');
    Route::post('/recetas/{receta}/favorita', [RecetaUsuarioController::class, 'alternarFavorita'])->name('recetas.favorita');
    Route::post('/recetas/{receta}/valorar',  [RecetaUsuarioController::class, 'valorar'])->name('recetas.valorar');
});

/*
|--------------------------------------------------------------------------
| Administración
|--------------------------------------------------------------------------
|
| Todo lo de aquí vive bajo /admin y exige rol Administrador, salvo el
| catálogo de ingredientes, que el Nutricionista también administra.
|
*/

Route::middleware(['auth', 'rol:Administrador'])->group(function () {

    // Historias 14 y 15 - Gestión y baja de usuarios
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    Route::name('admin.')->prefix('admin')->group(function () {

        Route::resource('categorias-ingrediente', CategoriaIngredienteController::class)
             ->parameters(['categorias-ingrediente' => 'categoria'])
             ->except(['show']);

        Route::resource('categorias-receta', CategoriaRecetaController::class)
             ->parameters(['categorias-receta' => 'categoria'])
             ->except(['show']);

        Route::post('unidades/sugerencia-ia', [UnidadMedidaController::class, 'sugerenciaIa'])
             ->middleware('throttle:20,1')->name('unidades.sugerencia-ia');
        Route::resource('unidades', UnidadMedidaController::class)
             ->parameters(['unidades' => 'unidad'])
             ->except(['show']);

        Route::resource('etiquetas', EtiquetaController::class)->except(['show']);

        Route::resource('preferencias', PreferenciaDietaController::class)
             ->parameters(['preferencias' => 'preferencia'])
             ->except(['show']);

        // Roles y permisos
        Route::resource('roles', RolController::class)
             ->parameters(['roles' => 'rol'])
             ->except(['show']);

        // Reportes (Historia 19, Requerimiento 18)
        Route::get('reportes',              [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/usuarios',     [ReporteController::class, 'usuarios'])->name('reportes.usuarios');
        Route::get('reportes/ingredientes', [ReporteController::class, 'ingredientes'])->name('reportes.ingredientes');
        Route::get('reportes/recetas',      [ReporteController::class, 'recetas'])->name('reportes.recetas');
        Route::get('reportes/consultas',    [ReporteController::class, 'consultas'])->name('reportes.consultas');

        // Monitoreo (Requerimiento 20). Solo lectura: no hay rutas de escritura.
        Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');

        // Moderación de valoraciones
        Route::get('valoraciones', [ValoracionController::class, 'index'])->name('valoraciones.index');
        Route::patch('valoraciones/{valoracion}/ocultar', [ValoracionController::class, 'ocultarComentario'])
             ->name('valoraciones.ocultar');
        Route::delete('valoraciones/{valoracion}', [ValoracionController::class, 'destroy'])
             ->name('valoraciones.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Compartido entre Administrador y Nutricionista
|--------------------------------------------------------------------------
|
| El catálogo de ingredientes lo administran ambos, porque el nutricionista
| es quien conoce los valores nutricionales. Las recetas también: el usuario
| final no las crea.
|
*/

Route::middleware(['auth', 'rol:Administrador,Nutricionista'])
     ->name('admin.')->prefix('admin')->group(function () {

         Route::post('ingredientes/nutricion-ia', [IngredienteController::class, 'nutricionIa'])
              ->middleware('throttle:20,1')->name('ingredientes.nutricion-ia');
         Route::resource('ingredientes', IngredienteController::class)->except(['show']);

         // Gestión de recetas (Requerimientos 11 y 12)
         Route::resource('recetas', RecetaController::class);
     });

/*
|--------------------------------------------------------------------------
| Panel del nutricionista
|--------------------------------------------------------------------------
|
| Historias 9 a 13 (Requerimientos 8, 9, 10, 16 y 17). El administrador
| entra también: si el nutricionista se ausenta, alguien tiene que poder
| desatorar la bandeja, y sin eso ninguna receta llegaría a verificarse.
|
*/

Route::middleware(['auth', 'rol:Nutricionista,Administrador'])
     ->name('nutricionista.')->prefix('validacion')->group(function () {

         // Historia 9 - Bandeja de recetas por revisar
         Route::get('/', [ValidacionController::class, 'index'])->name('index');

         // Historias 10 y 11 - Revisar la receta y su información nutricional
         Route::get('/{receta}', [ValidacionController::class, 'revisar'])->name('revisar');

         // Historias 12 y 13 - Emitir el dictamen y marcar verificada
         Route::post('/{receta}', [ValidacionController::class, 'dictaminar'])->name('dictaminar');
     });
