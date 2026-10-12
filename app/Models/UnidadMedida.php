<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';
    protected $primaryKey = 'cod_unidad';
    public $timestamps = false;

    protected $fillable = ['nom_uni', 'abr_uni', 'tip_uni', 'fac_uni', 'act_uni'];

    protected function casts(): array
    {
        return ['fac_uni' => 'decimal:6', 'act_uni' => 'boolean'];
    }

    /**
     * Unidades base de cada tipo (factor 1): todas las demás se convierten a
     * ellas, así que no se pueden cambiar, desactivar ni eliminar.
     */
    public const BASES = ['masa' => 'g', 'volumen' => 'ml', 'unidad' => 'pz'];

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class, 'cod_unidad_inv', 'cod_unidad');
    }

    public function ingredientesReceta(): HasMany
    {
        return $this->hasMany(IngredienteReceta::class, 'cod_unidad_inr', 'cod_unidad');
    }

    /** ¿Es la unidad base de su tipo (gramo, mililitro o pieza)? */
    public function esBase(): bool
    {
        return (self::BASES[$this->getOriginal('tip_uni')] ?? null) === $this->getOriginal('abr_uni');
    }

    /** Convierte una cantidad de esta unidad a la unidad base de su tipo. */
    public function aBase(float $cantidad): float
    {
        return $cantidad * (float) $this->fac_uni;
    }

    /** Convierte una cantidad expresada en la unidad base hacia esta unidad. */
    public function desdeBase(float $cantidadBase): float
    {
        $factor = (float) $this->fac_uni;

        return $factor > 0 ? $cantidadBase / $factor : 0.0;
    }

    public function esCompatibleCon(self $otra): bool
    {
        return $this->tip_uni === $otra->tip_uni;
    }
}
