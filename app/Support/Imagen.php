<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Traduce lo guardado en una columna de imagen a una URL que se pueda mostrar.
 *
 * Vive aparte del modelo porque el generador de recetas no devuelve modelos:
 * devuelve filas sueltas de una consulta, y esas también necesitan mostrar su
 * foto. Si la lógica viviera solo en el trait, habría que duplicarla.
 *
 * Conviven tres clases de valor y las tres deben seguir funcionando:
 *
 *   recetas/a7f3...jpg      subida por el formulario (disco public)
 *   img/recetas/algo.jpg    ruta escrita a mano antes de que existiera la subida
 *   https://.../foto.jpg    una dirección externa
 */
class Imagen
{
    /** Carpetas donde el formulario guarda lo que se sube. */
    private const CARPETAS = ['recetas/', 'ingredientes/'];

    public static function url(?string $valor): ?string
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        if (str_starts_with($valor, 'http://') || str_starts_with($valor, 'https://')) {
            return $valor;
        }

        foreach (self::CARPETAS as $carpeta) {
            if (str_starts_with($valor, $carpeta)) {
                // Si el archivo se borró del disco, mejor no mostrar una rota
                return Storage::disk('public')->exists($valor)
                    ? Storage::url($valor)
                    : null;
            }
        }

        // Ruta escrita a mano, relativa a public/
        return asset(ltrim($valor, '/'));
    }
}
