<?php

namespace App\Models;

use App\Models\Concerns\TieneImagen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receta extends Model
{
    use TieneImagen;

    /** Columna donde vive la imagen (la lee el trait TieneImagen). */
    public const COLUMNA_IMAGEN = 'img_rec';

    public const NIVELES = ['facil', 'medio', 'dificil'];
    public const ORIGENES = ['sistema', 'ia_generada', 'usuario'];
    public const ESTADOS_VALIDACION = ['pendiente', 'aprobada', 'rechazada', 'observada'];

    protected $table = 'recetas';
    protected $primaryKey = 'cod_receta';
    public $timestamps = false;

    protected $fillable = [
        'cod_categoria_receta_rec', 'cod_usuario_rec', 'nom_rec', 'des_rec',
        'tie_pre_rec', 'tie_coc_rec', 'por_rec', 'niv_rec', 'ori_rec', 'img_rec',
        // Informacion nutricional por porcion (Historia 11)
        'cal_por_rec', 'pro_por_rec', 'car_por_rec', 'gra_por_rec', 'obs_nut_rec',
        'est_val_rec', 'ver_rec', 'act_rec', 'fec_act_rec', 'fec_eli_rec',
    ];

    // tie_tot_rec es una columna calculada por PostgreSQL: no se asigna nunca.

    protected function casts(): array
    {
        return [
            'cal_por_rec' => 'decimal:2',
            'pro_por_rec' => 'decimal:2',
            'car_por_rec' => 'decimal:2',
            'gra_por_rec' => 'decimal:2',
            'ver_rec'     => 'boolean',
            'act_rec'     => 'boolean',
            'fec_cre_rec' => 'datetime',
            'fec_act_rec' => 'datetime',
            'fec_eli_rec' => 'datetime',
        ];
    }

    // ---------------------------------------------------------------- Relaciones

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaReceta::class, 'cod_categoria_receta_rec', 'cod_categoria_receta');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_rec', 'cod_usuario');
    }

    public function ingredientes(): HasMany
    {
        return $this->hasMany(IngredienteReceta::class, 'cod_receta_inr', 'cod_receta');
    }

    public function pasos(): HasMany
    {
        return $this->hasMany(PasoReceta::class, 'cod_receta_pas', 'cod_receta');
    }

    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(Etiqueta::class, 'receta_etiquetas', 'cod_receta_ret', 'cod_etiqueta_ret');
    }

    public function valoraciones(): HasMany
    {
        return $this->hasMany(Valoracion::class, 'cod_receta_val', 'cod_receta');
    }

    public function validaciones(): HasMany
    {
        return $this->hasMany(ValidacionReceta::class, 'cod_receta_vre', 'cod_receta');
    }

    public function usuariosQueLaGuardaron(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'favoritos', 'cod_receta_fav', 'cod_usuario_fav');
    }

    // ---------------------------------------------------------------- Scopes

    public function scopeActivas($query)
    {
        return $query->where('act_rec', true)->whereNull('fec_eli_rec');
    }

    public function scopeVerificadas($query)
    {
        return $query->where('ver_rec', true);
    }

    /**
     * Lo que puede ver quien cocina.
     *
     * Una receta que el nutricionista rechazó no debe seguir apareciendo en
     * el recetario ni salir sugerida por el generador: si se rechazó, fue
     * por algo. Las pendientes sí se ven, pero sin el distintivo de
     * verificada.
     */
    public function scopePublicas($query)
    {
        return $query->activas()->where('est_val_rec', '!=', 'rechazada');
    }

    public function scopePendientesDeRevision($query)
    {
        return $query->whereIn('est_val_rec', ['pendiente', 'observada']);
    }

    // ---------------------------------------------------------------- Accesores

    public function getPromedioValoracionAttribute(): float
    {
        return round((float) $this->valoraciones()->avg('pun_val'), 2);
    }

    public function getTiempoTotalAttribute(): int
    {
        // La base ya lo calcula, pero el accesor sirve cuando el modelo
        // todavia no se guardo.
        return (int) ($this->tie_tot_rec ?? ((int) $this->tie_pre_rec + (int) $this->tie_coc_rec));
    }

    /** Solo una receta aprobada puede marcarse como verificada. */
    public function puedeVerificarse(): bool
    {
        return $this->est_val_rec === 'aprobada';
    }
}
