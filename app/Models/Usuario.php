<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'cod_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nom_usu', 'cor_usu', 'con_usu', 'fot_usu', 'cod_rol_usu', 'act_usu',
    ];

    protected $hidden = [
        'con_usu', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'act_usu'     => 'boolean',
            'con_usu'     => 'hashed',
            'ult_acc_usu' => 'datetime',
            'fec_cre_usu' => 'datetime',
            'fec_act_usu' => 'datetime',
            'fec_eli_usu' => 'datetime',
        ];
    }

    /** Laravel busca 'password' por defecto; aqui la contrasena vive en con_usu. */
    public function getAuthPassword(): string
    {
        return $this->con_usu;
    }

    /** Campo que se usa para las notificaciones de recuperacion de contrasena. */
    public function getEmailForPasswordReset(): string
    {
        return $this->cor_usu;
    }

    public function routeNotificationForMail($notification = null): string
    {
        return $this->cor_usu;
    }

    // ---------------------------------------------------------------- Relaciones

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'cod_rol_usu', 'cod_rol');
    }

    public function preferencias(): BelongsToMany
    {
        return $this->belongsToMany(
            PreferenciaDieta::class,
            'usuario_preferencias',
            'cod_usuario_upr',
            'cod_preferencia_upr'
        );
    }

    public function inventario(): HasMany
    {
        return $this->hasMany(Inventario::class, 'cod_usuario_inv', 'cod_usuario');
    }

    public function recetas(): HasMany
    {
        return $this->hasMany(Receta::class, 'cod_usuario_rec', 'cod_usuario');
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(HistorialConsulta::class, 'cod_usuario_con', 'cod_usuario');
    }

    public function valoraciones(): HasMany
    {
        return $this->hasMany(Valoracion::class, 'cod_usuario_val', 'cod_usuario');
    }

    public function favoritos(): BelongsToMany
    {
        return $this->belongsToMany(
            Receta::class,
            'favoritos',
            'cod_usuario_fav',
            'cod_receta_fav'
        )->withPivot('fec_cre_fav');
    }

    public function validaciones(): HasMany
    {
        return $this->hasMany(ValidacionReceta::class, 'cod_usuario_vre', 'cod_usuario');
    }

    // ---------------------------------------------------------------- Helpers de rol

    public function esAdministrador(): bool
    {
        return $this->cod_rol_usu === Rol::ADMINISTRADOR;
    }

    public function esNutricionista(): bool
    {
        return $this->cod_rol_usu === Rol::NUTRICIONISTA;
    }

    public function esUsuario(): bool
    {
        return $this->cod_rol_usu === Rol::USUARIO;
    }

    public function tieneRol(string ...$roles): bool
    {
        return in_array($this->rol?->nom_rol, $roles, true);
    }

    // ---------------------------------------------------------------- Scopes

    public function scopeActivos($query)
    {
        return $query->where('act_usu', true)->whereNull('fec_eli_usu');
    }
}
