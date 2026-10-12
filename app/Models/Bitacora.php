<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'cod_bitacora';
    public $timestamps = false;

    protected $fillable = ['cod_usuario_bit', 'acc_bit', 'tab_bit', 'reg_bit', 'det_bit', 'ip_bit'];

    protected function casts(): array
    {
        return ['det_bit' => 'array', 'fec_cre_bit' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_bit', 'cod_usuario');
    }

    /** Atajo para registrar una accion desde cualquier controlador. */
    public static function registrar(string $accion, ?string $tabla = null, ?int $registro = null, array $detalle = []): self
    {
        return static::create([
            'cod_usuario_bit' => Auth::id(),
            'acc_bit'         => $accion,
            'tab_bit'         => $tabla,
            'reg_bit'         => $registro,
            'det_bit'         => $detalle ?: null,
            'ip_bit'          => request()->ip(),
        ]);
    }
}
