<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasoReceta extends Model
{
    protected $table = 'pasos_receta';
    protected $primaryKey = 'cod_paso';
    public $timestamps = false;

    protected $fillable = ['cod_receta_pas', 'num_pas', 'ins_pas', 'tie_pas', 'img_pas', 'cri_pas'];

    protected function casts(): array
    {
        return ['cri_pas' => 'boolean'];
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'cod_receta_pas', 'cod_receta');
    }
}
