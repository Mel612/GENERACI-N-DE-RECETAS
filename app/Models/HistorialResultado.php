<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialResultado extends Model
{
    protected $table = 'historial_resultado';
    protected $primaryKey = 'cod_resultado';
    public $timestamps = false;

    protected $fillable = [
        'cod_consulta_res', 'cod_receta_res', 'pos_res',
        'sco_res', 'fal_json_res', 'sel_res',
    ];

    protected function casts(): array
    {
        return [
            'sco_res'      => 'decimal:2',
            'fal_json_res' => 'array',
            'sel_res'      => 'boolean',
            'fec_cre_res'  => 'datetime',
        ];
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(HistorialConsulta::class, 'cod_consulta_res', 'cod_consulta');
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'cod_receta_res', 'cod_receta');
    }
}
