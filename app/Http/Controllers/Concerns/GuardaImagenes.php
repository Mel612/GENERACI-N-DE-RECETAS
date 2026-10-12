<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Subida de imágenes para recetas e ingredientes.
 *
 * Las imágenes van al disco `public` de Laravel (storage/app/public) y se
 * sirven por el enlace simbólico que crea `php artisan storage:link`. En la
 * base solo se guarda la ruta relativa, por ejemplo `recetas/a7f3...jpg`.
 *
 * ── Por qué no se guarda el nombre original ──────────────────────────────
 *
 * El nombre que trae un archivo subido lo escribe quien lo sube. Puede traer
 * rutas («../../algo»), caracteres que rompen el sistema de archivos, o
 * simplemente repetirse y pisar la imagen de otra receta. Se genera un
 * nombre nuevo y se conserva solo la extensión, que además se toma del tipo
 * real del archivo y no de lo que diga el nombre.
 *
 * ── Qué valida ───────────────────────────────────────────────────────────
 *
 * La regla `image` de Laravel no se fía de la extensión ni del tipo que
 * declara el navegador: abre el archivo y comprueba que de verdad sea una
 * imagen. Un .php renombrado a .jpg no pasa.
 */
trait GuardaImagenes
{
    /** Tipos aceptados y tamaño máximo (en kilobytes). */
    protected const IMAGEN_REGLAS = ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:3072'];

    protected const IMAGEN_MENSAJES = [
        'image' => 'El archivo debe ser una imagen.',
        'mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
        'max'   => 'La imagen no puede pesar más de 3 MB.',
    ];

    /**
     * Resuelve qué ruta de imagen debe quedar guardada.
     *
     * Contempla los tres casos: subieron una nueva, pidieron quitar la que
     * había, o no tocaron nada.
     *
     * @param  string  $campo    Nombre del input, p. ej. `img_rec`
     * @param  string  $carpeta  Subcarpeta del disco, p. ej. `recetas`
     * @param  ?string $actual   Lo que hay guardado hoy
     */
    protected function resolverImagen(Request $peticion, string $campo,
                                      string $carpeta, ?string $actual = null): ?string
    {
        // Pidieron quitarla
        if ($peticion->boolean("quitar_{$campo}")) {
            $this->borrarImagen($actual);

            return null;
        }

        // Subieron una nueva
        if ($peticion->hasFile($campo)) {
            /** @var UploadedFile $archivo */
            $archivo = $peticion->file($campo);

            // La extensión se deduce del contenido real, no del nombre
            $nombre = bin2hex(random_bytes(16)) . '.' . ($archivo->extension() ?: 'jpg');

            $ruta = $archivo->storeAs($carpeta, $nombre, 'public');

            // La anterior ya no la referencia nadie
            $this->borrarImagen($actual);

            return $ruta;
        }

        // No tocaron nada
        return $actual;
    }

    /**
     * Borra una imagen subida.
     *
     * Solo toca las que están dentro del disco público y en una de nuestras
     * carpetas. Las rutas escritas a mano de antes (`img/recetas/algo.jpg`)
     * apuntan a archivos que vinieron con el proyecto: borrarlas sería
     * destruir material que no subió nadie por el formulario.
     */
    protected function borrarImagen(?string $ruta): void
    {
        if (blank($ruta)) {
            return;
        }

        if (! preg_match('#^(recetas|ingredientes)/[a-f0-9]{32}\.[a-z]{3,4}$#', $ruta)) {
            return;
        }

        Storage::disk('public')->delete($ruta);
    }
}
