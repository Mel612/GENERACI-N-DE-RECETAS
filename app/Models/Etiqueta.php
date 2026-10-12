<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';
    protected $primaryKey = 'cod_etiqueta';
    public $timestamps = false;

    protected $fillable = ['nom_eti', 'col_eti', 'act_eti'];

    protected function casts(): array
    {
        return ['act_eti' => 'boolean'];
    }

    public function recetas(): BelongsToMany
    {
        return $this->belongsToMany(Receta::class, 'receta_etiquetas', 'cod_etiqueta_ret', 'cod_receta_ret');
    }
}
