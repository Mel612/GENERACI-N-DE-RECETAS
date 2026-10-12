<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Bitacora;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

/**
 * Comportamiento comun a los catalogos del sistema.
 *
 * Los catalogos estan referenciados por otras tablas con ON DELETE RESTRICT,
 * asi que no siempre se pueden borrar. La regla es:
 *
 *   - Si nadie lo usa  -> se elimina de verdad.
 *   - Si esta en uso   -> se desactiva y se avisa al usuario.
 *
 * Asi nunca se pierde informacion historica ni se rompe una receta existente.
 */
trait GestionaCatalogo
{
    /**
     * Elimina el registro si no esta en uso; si lo esta, lo desactiva.
     *
     * @param  array<string>  $relaciones  Relaciones que impiden el borrado.
     * @param  string  $campoActivo        Columna booleana de estado.
     * @return array{0: string, 1: string}  [tipo de mensaje, texto]
     */
    protected function eliminarODesactivar(Model $modelo, array $relaciones, string $campoActivo): array
    {
        $enUso = collect($relaciones)->first(fn ($rel) => $modelo->{$rel}()->exists());

        if ($enUso) {
            $modelo->update([$campoActivo => false]);

            Bitacora::registrar('desactivar', $modelo->getTable(), $modelo->getKey(), [
                'motivo' => "en uso por {$enUso}",
            ]);

            return ['aviso', 'El registro está en uso, así que se desactivó en lugar de eliminarse. '
                          . 'Dejará de aparecer en los formularios, pero la información existente se conserva.'];
        }

        try {
            $modelo->delete();
        } catch (QueryException $e) {
            // Red de seguridad por si aparece una relacion no contemplada.
            $modelo->update([$campoActivo => false]);

            return ['aviso', 'El registro no se pudo eliminar porque otra tabla lo referencia; se desactivó.'];
        }

        Bitacora::registrar('eliminar', $modelo->getTable(), $modelo->getKey());

        return ['exito', 'Registro eliminado correctamente.'];
    }

    /** Aplica una busqueda sin distinguir mayusculas sobre varias columnas. */
    protected function buscarEn($consulta, ?string $texto, array $columnas)
    {
        if (blank($texto)) {
            return $consulta;
        }

        $texto = '%' . mb_strtolower(trim($texto)) . '%';

        return $consulta->where(function ($q) use ($texto, $columnas) {
            foreach ($columnas as $columna) {
                $q->orWhereRaw("LOWER(COALESCE({$columna}, '')) LIKE ?", [$texto]);
            }
        });
    }
}
