<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valoracion extends Model
{
    protected $table = 'valoraciones';
    protected $primaryKey = 'cod_valoracion';
    public $timestamps = false;

    protected $fillable = ['cod_usuario_val', 'cod_receta_val', 'pun_val', 'com_val'];

    protected function casts(): array
    {
        return ['fec_cre_val' => 'datetime', 'fec_act_val' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_val', 'cod_usuario');
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'cod_receta_val', 'cod_receta');
    }
}
