<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredienteReceta extends Model
{
    protected $table = 'ingredientes_receta';
    protected $primaryKey = 'cod_ingrediente_receta';
    public $timestamps = false;

    protected $fillable = [
        'cod_receta_inr', 'cod_ingrediente_inr', 'cod_unidad_inr',
        'can_inr', 'opc_inr', 'ord_inr', 'not_inr',
    ];

    protected function casts(): array
    {
        return ['can_inr' => 'decimal:3', 'opc_inr' => 'boolean'];
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'cod_receta_inr', 'cod_receta');
    }

    public function ingrediente(): BelongsTo
    {
        return $this->belongsTo(Ingrediente::class, 'cod_ingrediente_inr', 'cod_ingrediente');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'cod_unidad_inr', 'cod_unidad');
    }

    /** Cantidad llevada a la unidad base de su tipo, para poder compararla. */
    public function cantidadBase(): float
    {
        return (float) $this->can_inr * (float) ($this->unidad->fac_uni ?? 1);
    }
}
