<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model
{
    protected $table = 'inventario';
    protected $primaryKey = 'cod_inventario';
    public $timestamps = false;

    protected $fillable = [
        'cod_usuario_inv', 'cod_ingrediente_inv', 'cod_unidad_inv',
        'can_inv', 'fec_cad_inv', 'not_inv', 'act_inv',
    ];

    protected function casts(): array
    {
        return [
            'can_inv'     => 'decimal:3',
            'fec_cad_inv' => 'date',
            'act_inv'     => 'boolean',
            'fec_cre_inv' => 'datetime',
            'fec_act_inv' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_inv', 'cod_usuario');
    }

    public function ingrediente(): BelongsTo
    {
        return $this->belongsTo(Ingrediente::class, 'cod_ingrediente_inv', 'cod_ingrediente');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'cod_unidad_inv', 'cod_unidad');
    }

    public function cantidadBase(): float
    {
        return (float) $this->can_inv * (float) ($this->unidad->fac_uni ?? 1);
    }

    /**
     * Lo que se puede usar para cocinar. Lo vencido no: seguía alimentando
     * al generador días después de caducar.
     */
    public function scopeDisponibles($query)
    {
        return $query->where('act_inv', true)->where('can_inv', '>', 0)->vigentes();
    }

    /*
     * Las fechas se comparan con CURRENT_DATE de PostgreSQL y no con now()
     * de PHP: la base corre en hora de Bolivia y Laravel en UTC, así que de
     * noche el "hoy" de PHP ya es mañana.
     */

    /** Caducó antes de hoy. Lo que vence hoy todavía sirve. */
    public function scopeVencidos($query)
    {
        return $query->whereRaw('fec_cad_inv < CURRENT_DATE');
    }

    /** Sin fecha de caducidad, o que vence hoy o después. */
    public function scopeVigentes($query)
    {
        return $query->where(fn ($q) => $q
            ->whereNull('fec_cad_inv')
            ->orWhereRaw('fec_cad_inv >= CURRENT_DATE'));
    }

    /** Ingredientes que caducan dentro de los proximos N dias. */
    public function scopePorCaducar($query, int $dias = 7)
    {
        return $query->whereNotNull('fec_cad_inv')
                     ->whereRaw('fec_cad_inv BETWEEN CURRENT_DATE AND CURRENT_DATE + ?::int', [$dias]);
    }

    public function estaVencido(): bool
    {
        return $this->fec_cad_inv !== null && $this->fec_cad_inv->isPast();
    }
}
