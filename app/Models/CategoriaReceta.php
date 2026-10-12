<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaReceta extends Model
{
    protected $table = 'categorias_receta';
    protected $primaryKey = 'cod_categoria_receta';
    public $timestamps = false;

    protected $fillable = ['nom_car', 'des_car', 'ico_car', 'act_car'];

    protected function casts(): array
    {
        return ['act_car' => 'boolean', 'fec_cre_car' => 'datetime'];
    }

    public function recetas(): HasMany
    {
        return $this->hasMany(Receta::class, 'cod_categoria_receta_rec', 'cod_categoria_receta');
    }
}
