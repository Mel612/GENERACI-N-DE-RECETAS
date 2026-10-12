<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaIngrediente extends Model
{
    protected $table = 'categorias_ingrediente';
    protected $primaryKey = 'cod_categoria_ingrediente';
    public $timestamps = false;

    protected $fillable = ['nom_cat', 'des_cat', 'ico_cat', 'act_cat'];

    protected function casts(): array
    {
        return ['act_cat' => 'boolean', 'fec_cre_cat' => 'datetime'];
    }

    public function ingredientes(): HasMany
    {
        return $this->hasMany(Ingrediente::class, 'cod_categoria_ingrediente_ing', 'cod_categoria_ingrediente');
    }
}
