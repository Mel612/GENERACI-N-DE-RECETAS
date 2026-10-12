<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorito extends Model
{
    protected $table = 'favoritos';
    protected $primaryKey = 'cod_favorito';
    public $timestamps = false;

    protected $fillable = ['cod_usuario_fav', 'cod_receta_fav'];

    protected function casts(): array
    {
        return ['fec_cre_fav' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_fav', 'cod_usuario');
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'cod_receta_fav', 'cod_receta');
    }
}
