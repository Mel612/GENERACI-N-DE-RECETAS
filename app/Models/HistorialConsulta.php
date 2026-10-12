<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistorialConsulta extends Model
{
    protected $table = 'historial_consulta';
    protected $primaryKey = 'cod_consulta';
    public $timestamps = false;

    protected $fillable = [
        'cod_usuario_con', 'ing_json_con', 'fil_json_con',
        'tex_lib_con', 'tot_res_con', 'tie_res_con',
    ];

    protected function casts(): array
    {
        return [
            'ing_json_con' => 'array',
            'fil_json_con' => 'array',
            'fec_cre_con'  => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_con', 'cod_usuario');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(HistorialResultado::class, 'cod_consulta_res', 'cod_consulta');
    }
}
