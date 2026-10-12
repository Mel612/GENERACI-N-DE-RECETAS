<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un dictamen del nutricionista sobre una receta (Historias 10 a 13).
 *
 * Cada revision se guarda como una fila nueva: el historial no se pisa.
 * El estado vigente de la receta vive en recetas.est_val_rec; esta tabla
 * conserva quien dijo que y cuando.
 */
class ValidacionReceta extends Model
{
    public const ESTADOS = ['aprobada', 'rechazada', 'observada'];

    protected $table = 'validaciones_receta';
    protected $primaryKey = 'cod_validacion';
    public $timestamps = false;

    protected $fillable = [
        'cod_receta_vre', 'cod_usuario_vre', 'est_vre', 'obs_vre',
        'val_ing_vre', 'val_pas_vre', 'val_nut_vre',
    ];

    protected function casts(): array
    {
        return [
            'val_ing_vre' => 'boolean',
            'val_pas_vre' => 'boolean',
            'val_nut_vre' => 'boolean',
            'fec_rev_vre' => 'datetime',
        ];
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'cod_receta_vre', 'cod_receta');
    }

    public function nutricionista(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_vre', 'cod_usuario');
    }
}
