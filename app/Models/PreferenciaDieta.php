<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PreferenciaDieta extends Model
{
    protected $table = 'preferencias_dieta';
    protected $primaryKey = 'cod_preferencia';
    public $timestamps = false;

    protected $fillable = ['nom_pre', 'des_pre', 'act_pre'];

    protected function casts(): array
    {
        return ['act_pre' => 'boolean', 'fec_cre_pre' => 'datetime'];
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuario_preferencias',
            'cod_preferencia_upr',
            'cod_usuario_upr'
        );
    }
}
