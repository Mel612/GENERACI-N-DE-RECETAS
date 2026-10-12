<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Lo que sale de la despensa y por qué.
 *
 * Antes quitar un ingrediente era un DELETE y se perdía todo: cuánto era,
 * cuándo caducaba y qué pasó con él. Esta tabla lo conserva para medir el
 * aprovechamiento. Las vistas v_salidas_medidas y v_aprovechamiento_usuario
 * hacen las cuentas (gramos, bolivianos y tasas).
 *
 * 'sin_resolver' no es consumido ni desperdiciado: es lo que nadie respondió
 * y se cerró solo. Que la fecha pase no prueba que se botó, así que no se
 * cuenta en contra ni a favor.
 */
class SalidaInventario extends Model
{
    protected $table = 'salidas_inventario';
    protected $primaryKey = 'cod_salida';
    public $timestamps = false;

    public const MOTIVOS = ['consumido', 'desperdiciado', 'otro', 'sin_resolver'];

    public const CAUSAS = ['caduco', 'mal_estado', 'exceso', 'no_gusto'];

    /** Los que puede elegir una persona; 'sin_resolver' lo pone el cierre automático. */
    public const MOTIVOS_ELEGIBLES = ['consumido', 'desperdiciado', 'otro'];

    public const TEXTO_MOTIVO = [
        'consumido'     => 'Consumido',
        'desperdiciado' => 'Desperdiciado',
        'otro'          => 'Otro',
        'sin_resolver'  => 'Sin resolver',
    ];

    public const TEXTO_CAUSA = [
        'caduco'     => 'Caducó',
        'mal_estado' => 'Se echó a perder',
        'exceso'     => 'Compré de más',
        'no_gusto'   => 'No nos gustó',
    ];

    /** Icono de cada motivo: el color nunca va solo. */
    public const ICONO_MOTIVO = [
        'consumido'     => 'M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z',
        'desperdiciado' => 'M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z',
        'otro'          => 'M6 10a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4zm6 0a2 2 0 100 4 2 2 0 000-4z',
        'sin_resolver'  => 'M11 18h2v-2h-2v2zm1-16a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16zm0-14a4 4 0 00-4 4h2a2 2 0 114 0c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5a4 4 0 00-4-4z',
    ];

    public const COLOR_MOTIVO = [
        'consumido'     => 'text-exito-400',
        'desperdiciado' => 'text-error-400',
        'otro'          => 'text-crema-300',
        'sin_resolver'  => 'text-alerta-400',
    ];

    protected $fillable = [
        'cod_usuario_sal', 'cod_ingrediente_sal', 'cod_unidad_sal', 'can_sal',
        'mot_sal', 'cau_sal', 'cod_receta_sal', 'fec_cad_sal', 'not_sal',
    ];

    protected function casts(): array
    {
        return [
            'can_sal'     => 'decimal:3',
            'fec_cad_sal' => 'date',
            'fec_sal'     => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario_sal', 'cod_usuario');
    }

    public function ingrediente(): BelongsTo
    {
        return $this->belongsTo(Ingrediente::class, 'cod_ingrediente_sal', 'cod_ingrediente');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'cod_unidad_sal', 'cod_unidad');
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'cod_receta_sal', 'cod_receta');
    }

    /**
     * Registra la salida de las filas de inventario de la consulta, copiando
     * cantidad, unidad y fecha de caducidad. No borra nada: eso lo hace quien
     * llama, dentro de la misma transacción.
     *
     * Las filas en cero se saltan: la tabla exige can_sal > 0 y una salida de
     * nada no mide nada. Devuelve cuántas salidas se registraron.
     */
    public static function registrarDesde(Builder $filas, string $motivo, ?string $causa = null): int
    {
        // La base exige causa en el desperdicio y la prohíbe en lo demás
        $causa = $motivo === 'desperdiciado' ? $causa : null;

        $origen = $filas->clone()->toBase()
            ->where('can_inv', '>', 0)
            ->select(['cod_usuario_inv', 'cod_ingrediente_inv', 'cod_unidad_inv', 'can_inv', 'fec_cad_inv'])
            ->selectRaw('CAST(? AS varchar), CAST(? AS varchar)', [$motivo, $causa]);

        return DB::table('salidas_inventario')->insertUsing(
            ['cod_usuario_sal', 'cod_ingrediente_sal', 'cod_unidad_sal', 'can_sal', 'fec_cad_sal', 'mot_sal', 'cau_sal'],
            $origen
        );
    }

    /** 350 -> «350 g»; 1250 -> «1,25 kg». */
    public static function peso(float|string|null $gramos): string
    {
        $g = (float) $gramos;

        if ($g >= 1000) {
            return rtrim(rtrim(number_format($g / 1000, 2, ',', '.'), '0'), ',') . ' kg';
        }

        return number_format($g, 0, ',', '.') . ' g';
    }

    /** 82.4 -> «82,4 %». */
    public static function porcentaje(float|string|null $valor): string
    {
        return rtrim(rtrim(number_format((float) $valor, 1, ',', '.'), '0'), ',') . "\u{00A0}%";   // sin espacio partible: el % no se va solo a otra línea
    }
}
