<?php

namespace App\Services;

use App\Models\Etiqueta;
use App\Models\Usuario;
use Illuminate\Support\Collection;

/**
 * Traduce las preferencias alimentarias de una persona a filtros de receta.
 *
 * El registro y el perfil ya guardan las preferencias en usuario_preferencias,
 * pero hasta ahora nadie las usaba: una persona vegetariana recibía recetas
 * con carne igual que cualquier otra.
 *
 * ── Cómo se traduce ──────────────────────────────────────────────────────
 *
 * Una preferencia se puede aplicar cuando existe una etiqueta de receta con
 * el mismo nombre. «Vegetariano» como preferencia se vuelve «solo recetas
 * etiquetadas Vegetariano». La comparación ignora mayúsculas y tildes,
 * porque el catálogo tiene «Sin azucar anadida» sin tildes y las etiquetas
 * podrían tenerlas.
 *
 * ── Lo que no se puede aplicar ───────────────────────────────────────────
 *
 * Cuatro de las nueve preferencias del catálogo no tienen etiqueta que les
 * corresponda: Bajo en carbohidratos, Bajo en sodio, Sin azúcar añadida y
 * Sin frutos secos. No se inventa un criterio para ellas.
 *
 * Calcularlas desde la nutrición tampoco es posible todavía: las recetas
 * recién ahora empiezan a tener información nutricional cargada por el
 * nutricionista, y «Sin frutos secos» necesitaría saber qué alérgeno tiene
 * cada ingrediente, no solo que tiene alguno (hoy `ale_ing` es un booleano).
 *
 * Por eso esta clase devuelve también las que NO pudo aplicar, para que la
 * pantalla lo diga en lugar de callarlo. Una preferencia que se ignora en
 * silencio es peor que una que no existe.
 */
class RestriccionesDieta
{
    /**
     * Resuelve las preferencias de un usuario.
     *
     * @return array{aplicables: Collection, sin_soporte: Collection, tiene: bool}
     */
    public function para(Usuario $usuario): array
    {
        $preferencias = $usuario->preferencias()->where('act_pre', true)->get();

        if ($preferencias->isEmpty()) {
            return ['aplicables' => collect(), 'sin_soporte' => collect(), 'tiene' => false];
        }

        // Etiquetas activas indexadas por su nombre normalizado
        $etiquetas = Etiqueta::where('act_eti', true)->get()
            ->keyBy(fn ($e) => $this->normalizar($e->nom_eti));

        $aplicables  = collect();
        $sinSoporte  = collect();

        foreach ($preferencias as $preferencia) {
            $etiqueta = $etiquetas->get($this->normalizar($preferencia->nom_pre));

            if ($etiqueta) {
                $aplicables->push([
                    'preferencia'  => $preferencia->nom_pre,
                    'cod_etiqueta' => $etiqueta->cod_etiqueta,
                    'color'        => $etiqueta->col_eti,
                ]);
            } else {
                $sinSoporte->push($preferencia->nom_pre);
            }
        }

        return [
            'aplicables'  => $aplicables,
            'sin_soporte' => $sinSoporte,
            'tiene'       => true,
        ];
    }

    /** Códigos de etiqueta que la receta debe tener todas. */
    public function etiquetasExigidas(Usuario $usuario): array
    {
        return $this->para($usuario)['aplicables']->pluck('cod_etiqueta')->all();
    }

    /**
     * Minúsculas y sin tildes, para que «Sin azúcar añadida» y
     * «Sin azucar anadida» se reconozcan como lo mismo.
     */
    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');

        $texto = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'ñ' => 'n',
        ]);

        // Un espacio de más no debería romper la coincidencia
        return preg_replace('/\s+/', ' ', $texto);
    }
}
