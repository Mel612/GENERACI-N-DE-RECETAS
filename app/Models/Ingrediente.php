<?php

namespace App\Models;

use App\Models\Concerns\TieneImagen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingrediente extends Model
{
    use TieneImagen;

    /** Columna donde vive la imagen (la lee el trait TieneImagen). */
    public const COLUMNA_IMAGEN = 'img_ing';

    protected $table = 'ingredientes';
    protected $primaryKey = 'cod_ingrediente';
    public $timestamps = false;

    protected $fillable = [
        'cod_categoria_ingrediente_ing', 'nom_ing', 'sin_ing',
        'cal_ing', 'pro_ing', 'car_ing', 'gra_ing', 'den_ing', 'pre_ing', 'ale_ing', 'img_ing', 'act_ing',
    ];

    protected function casts(): array
    {
        return [
            'cal_ing' => 'decimal:2', 'pro_ing' => 'decimal:2',
            'car_ing' => 'decimal:2', 'gra_ing' => 'decimal:2',
            'den_ing' => 'decimal:3',
            'pre_ing' => 'decimal:2',   // Bs por kilo, de referencia
            'ale_ing' => 'boolean',   'act_ing' => 'boolean',
            'fec_cre_ing' => 'datetime', 'fec_act_ing' => 'datetime',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaIngrediente::class, 'cod_categoria_ingrediente_ing', 'cod_categoria_ingrediente');
    }

    public function recetas(): HasMany
    {
        return $this->hasMany(IngredienteReceta::class, 'cod_ingrediente_inr', 'cod_ingrediente');
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class, 'cod_ingrediente_inv', 'cod_ingrediente');
    }

    public function scopeActivos($query)
    {
        return $query->where('act_ing', true);
    }

    /**
     * Convierte una cantidad de este ingrediente a gramos.
     *
     * Devuelve null cuando no se puede: un volumen sin densidad registrada,
     * o una medida por unidad (una cebolla no tiene peso fijo).
     */
    public function aGramos(float $cantidad, UnidadMedida $unidad): ?float
    {
        if ($unidad->tip_uni === 'masa') {
            return $cantidad * (float) $unidad->fac_uni;
        }

        if ($unidad->tip_uni === 'volumen' && $this->den_ing !== null) {
            return $cantidad * (float) $unidad->fac_uni * (float) $this->den_ing;
        }

        return null;
    }

    /**
     * Calorías por 100 g a partir de los macronutrientes (factores de Atwater).
     *
     * Proteínas y carbohidratos aportan 4 kcal por gramo y las grasas 9. Así
     * las calorías siempre cuadran con los macros en lugar de escribirse a mano.
     */
    public static function calcularCalorias(float $proteinas, float $carbohidratos, float $grasas): float
    {
        return round(4 * $proteinas + 4 * $carbohidratos + 9 * $grasas, 2);
    }

    /** Busca por nombre o por cualquiera de sus sinonimos. */
    public function scopeBuscar($query, ?string $texto)
    {
        if (blank($texto)) {
            return $query;
        }

        $texto = mb_strtolower(trim($texto));

        return $query->where(function ($q) use ($texto) {
            $q->whereRaw('LOWER(nom_ing) LIKE ?', ["%{$texto}%"])
              ->orWhereRaw('LOWER(COALESCE(sin_ing, \'\')) LIKE ?', ["%{$texto}%"]);
        });
    }
}
