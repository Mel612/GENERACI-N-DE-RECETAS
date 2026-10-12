<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
use App\Models\Inventario;
use App\Models\SalidaInventario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Cierra lo vencido que nadie resolvió.
 *
 * Lo que lleva más de DIAS días vencido sin que la persona diga qué pasó
 * sale de la despensa con motivo 'sin_resolver'. Nunca como desperdiciado:
 * que la fecha haya pasado no prueba que se botó, y la tasa tiene que salir
 * de lo que la gente respondió, no de lo que el sistema supone.
 */
class CerrarSalidasPendientes extends Command
{
    /** Días de gracia después de la caducidad para responder. */
    public const DIAS = 7;

    protected $signature = 'app:cerrar-salidas-pendientes';

    protected $description = 'Registra como «sin resolver» y quita de la despensa lo vencido hace más de ' . self::DIAS . ' días';

    public function handle(): int
    {
        // CURRENT_DATE de la base, que corre en hora de Bolivia
        $pendientes = Inventario::whereRaw('fec_cad_inv < CURRENT_DATE - ?::int', [self::DIAS]);

        [$registradas, $quitadas] = DB::transaction(fn () => [
            SalidaInventario::registrarDesde($pendientes, 'sin_resolver'),
            (clone $pendientes)->delete(),
        ]);

        if ($quitadas > 0) {
            Bitacora::registrar('cerrar_salidas_pendientes', 'inventario', null, [
                'registradas' => $registradas,
                'quitadas'    => $quitadas,
            ]);
        }

        $this->info("Se cerraron {$registradas} salida(s) como «sin resolver»; {$quitadas} fila(s) salieron de las despensas.");

        return self::SUCCESS;
    }
}
