<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    public const ADMINISTRADOR = 1;
    public const NUTRICIONISTA = 2;
    public const USUARIO       = 3;

    protected $table = 'roles';
    protected $primaryKey = 'cod_rol';
    public $timestamps = false;

    protected $fillable = ['nom_rol', 'des_rol', 'act_rol'];

    protected function casts(): array
    {
        return ['act_rol' => 'boolean', 'fec_cre_rol' => 'datetime'];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'cod_rol_usu', 'cod_rol');
    }
}
