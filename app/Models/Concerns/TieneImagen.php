<?php

namespace App\Models\Concerns;

use App\Support\Imagen;

/**
 * Acceso cómodo a la imagen de un modelo.
 *
 * La traducción de ruta guardada a URL vive en App\Support\Imagen, porque el
 * generador de recetas trabaja con filas sueltas y no con modelos: ahí hace
 * falta la misma lógica sin tener un modelo delante.
 *
 * El modelo que use este trait declara la constante COLUMNA_IMAGEN con el
 * nombre de su columna.
 */
trait TieneImagen
{
    public function urlImagen(): ?string
    {
        return Imagen::url($this->{static::COLUMNA_IMAGEN} ?? null);
    }

    public function tieneImagen(): bool
    {
        return $this->urlImagen() !== null;
    }
}
