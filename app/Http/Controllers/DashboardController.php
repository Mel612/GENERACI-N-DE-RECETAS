<?php

namespace App\Http\Controllers;

use App\Models\Ingrediente;
use App\Models\Inventario;
use App\Models\Receta;
use App\Models\Usuario;
use App\Models\Valoracion;
use App\Services\GeneradorRecetas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private GeneradorRecetas $generador) {}

    public function index(): View
    {
        $usuario = Auth::user();

        // Fecha y hora de la cabecera, de PostgreSQL: la base corre en hora
        // de Bolivia y Laravel en UTC, así que con now() de PHP el saludo
        // decía «Buenos días» a las once de la noche.
        $reloj = DB::selectOne('SELECT CURRENT_DATE AS fecha, EXTRACT(HOUR FROM LOCALTIMESTAMP)::int AS hora');

        // El administrador entra a ver el estado del sistema, no su despensa:
        // tiene su propio panel con sus propias consultas.
        if ($usuario->esAdministrador()) {
            return view('dashboard', ['rol' => 'admin', 'reloj' => $reloj, ...$this->panelAdmin($usuario)]);
        }

        $mios = Inventario::where('cod_usuario_inv', $usuario->cod_usuario);

        $resumen = [
            'ingredientes'  => (clone $mios)->disponibles()->count(),
            'por_caducar'   => (clone $mios)->disponibles()->porCaducar()->count(),
            // Vencidos con cantidad: esperan que se diga qué pasó con ellos
            'por_resolver'  => (clone $mios)->vencidos()->where('can_inv', '>', 0)->count(),
            'favoritos'     => $usuario->favoritos()->count(),
            'consultas'     => $usuario->consultas()->count(),
        ];

        if ($usuario->esNutricionista()) {
            $resumen['por_revisar'] = Receta::activas()->pendientesDeRevision()->count();
        }

        // Estado del catalogo de ingredientes: tambien lo administra el nutricionista.
        if ($usuario->esNutricionista()) {
            $resumen['ingredientes_catalogo'] = Ingrediente::activos()->count();
            $resumen['sin_nutricion']         = Ingrediente::whereNull('cal_ing')->where('act_ing', true)->count();
        }

        // La despensa se lee una vez: sirve para la lista del panel y para
        // decidir si hay receta que destacar
        $despensa = $this->generador->despensa($usuario->cod_usuario);

        return view('dashboard', [
            'rol'         => $usuario->esNutricionista() ? 'nutricionista' : 'usuario',
            'reloj'       => $reloj,
            'resumen'     => $resumen,
            'destacada'   => $this->destacada($usuario->cod_usuario, $despensa),

            // Tasa de aprovechamiento; null si todavía no sacó nada de la despensa
            'aprovechamiento' => DB::table('v_aprovechamiento_usuario')
                ->where('cod_usuario_sal', $usuario->cod_usuario)->first(),

            // Lo que vence primero arriba, como se anotaría en el cuaderno
            'despensa'    => $despensa
                                ->sortBy(fn ($d) => [$d->urgente ? 0 : 1, $d->fec_cad_inv ?? '9999-12-31', $d->nom_ing])
                                ->take(8)->values(),

            'verificadas' => Receta::activas()->verificadas()->count(),
            'totalRecetas' => Receta::activas()->count(),
        ]);
    }

    /**
     * Datos del panel del administrador.
     *
     * Una consulta agregada por tabla, con COUNT(*) FILTER de PostgreSQL, en
     * lugar de un count() por cifra. Las fechas se comparan con CURRENT_DATE:
     * la base corre en hora de Bolivia y las columnas fec_cre_* las llena su
     * DEFAULT, mientras que Laravel corre en UTC. Con now() de PHP, de noche
     * el "hoy" del panel sería el día siguiente.
     */
    private function panelAdmin(Usuario $usuario): array
    {
        // ---- Recetas activas: estado, verificación, incompletas y altas
        $recetas = Receta::activas()->toBase()->selectRaw("
            COUNT(*)                                          AS total,
            COUNT(*) FILTER (WHERE ver_rec)                   AS verificadas,
            COUNT(*) FILTER (WHERE est_val_rec = 'pendiente') AS pendiente,
            COUNT(*) FILTER (WHERE est_val_rec = 'observada') AS observada,
            COUNT(*) FILTER (WHERE est_val_rec = 'aprobada')  AS aprobada,
            COUNT(*) FILTER (WHERE est_val_rec = 'rechazada') AS rechazada,
            COUNT(*) FILTER (WHERE fec_cre_rec >= CURRENT_DATE - 29) AS nuevas,
            COUNT(*) FILTER (WHERE
                NOT EXISTS (SELECT 1 FROM ingredientes_receta i WHERE i.cod_receta_inr = recetas.cod_receta)
                OR NOT EXISTS (SELECT 1 FROM pasos_receta p WHERE p.cod_receta_pas = recetas.cod_receta)
            ) AS incompletas
        ")->first();

        // ---- Ingredientes activos y sus datos faltantes
        $ingredientes = Ingrediente::activos()->toBase()->selectRaw('
            COUNT(*)                                  AS total,
            COUNT(*) FILTER (WHERE cal_ing IS NULL)   AS sin_nutricion,
            COUNT(*) FILTER (WHERE den_ing IS NULL)   AS sin_densidad,
            COUNT(*) FILTER (WHERE fec_cre_ing >= CURRENT_DATE - 29) AS nuevos
        ')->first();

        // Un comentario moderado queda en NULL: lo que sigue con texto se ve
        $resenasMalas = Valoracion::where('pun_val', 1)
            ->whereNotNull('com_val')->where('com_val', '<>', '')
            ->count();

        // ---- Búsquedas por día de los últimos 28: 14 para la gráfica y 14
        //      anteriores para tener contra qué comparar. Los días sin
        //      búsquedas salen en cero gracias a generate_series.
        $dias = collect(DB::select("
            SELECT d::date AS dia,
                   COUNT(h.cod_consulta) AS busquedas,
                   COALESCE(SUM(h.tie_res_con), 0) AS suma_ms,
                   COUNT(h.tie_res_con) AS con_ms
            FROM generate_series(CURRENT_DATE - 27, CURRENT_DATE, INTERVAL '1 day') AS d
            LEFT JOIN historial_consulta h
                   ON h.fec_cre_con >= d AND h.fec_cre_con < d + INTERVAL '1 day'
            GROUP BY d
            ORDER BY d
        "));

        [$anteriores, $recientes] = $dias->chunk(14)->map->values()->all();

        $promedioMs = fn ($tramo) => $tramo->sum('con_ms') > 0
            ? (int) round($tramo->sum('suma_ms') / $tramo->sum('con_ms'))
            : null;

        // ---- Personas con cualquier actividad registrada. No solo quien
        //      inició sesión: con la sesión recordada se puede usar el
        //      sistema toda la semana sin volver a entrar.
        $activos = DB::selectOne('
            SELECT COUNT(DISTINCT cod_usuario_bit) FILTER (WHERE fec_cre_bit >= CURRENT_DATE - 6) AS semana,
                   COUNT(DISTINCT cod_usuario_bit) FILTER (WHERE fec_cre_bit <  CURRENT_DATE - 6) AS anterior
            FROM bitacora
            WHERE fec_cre_bit >= CURRENT_DATE - 13
        ');

        // ---- Lo personal, en una línea
        $despensa = Inventario::where('cod_usuario_inv', $usuario->cod_usuario)
            ->disponibles()->toBase()->selectRaw('
                COUNT(*) AS total,
                COUNT(*) FILTER (WHERE fec_cad_inv BETWEEN CURRENT_DATE AND CURRENT_DATE + 7) AS por_caducar
            ')->first();

        $porRevisar = (int) $recetas->pendiente + (int) $recetas->observada;

        // Solo lo accionable, y nada en cero: un panel lleno de ceros
        // enseña a no mirarlo
        $atencion = collect([
            ['cantidad' => $porRevisar,
             'texto'    => $porRevisar === 1 ? 'receta espera dictamen del nutricionista' : 'recetas esperan dictamen del nutricionista',
             'enlace'   => route('nutricionista.index')],
            ['cantidad' => (int) $ingredientes->sin_nutricion,
             'texto'    => (int) $ingredientes->sin_nutricion === 1 ? 'ingrediente sin datos nutricionales' : 'ingredientes sin datos nutricionales',
             'detalle'  => 'Sin calorías no se puede calcular la nutrición de las recetas que los usan.',
             'enlace'   => route('admin.ingredientes.index', ['sin_nutricion' => 1, 'estado' => 'activos'])],
            ['cantidad' => (int) $ingredientes->sin_densidad,
             'texto'    => (int) $ingredientes->sin_densidad === 1 ? 'ingrediente sin densidad' : 'ingredientes sin densidad',
             'detalle'  => 'Sin densidad el generador no puede comparar "2 tazas" de una receta con "1 kg" de una despensa.',
             'enlace'   => route('admin.ingredientes.index', ['sin_densidad' => 1, 'estado' => 'activos'])],
            ['cantidad' => (int) $recetas->incompletas,
             'texto'    => (int) $recetas->incompletas === 1 ? 'receta activa sin ingredientes o sin pasos' : 'recetas activas sin ingredientes o sin pasos',
             'enlace'   => route('admin.recetas.index', ['incompletas' => 1])],
            ['cantidad' => $resenasMalas,
             'texto'    => $resenasMalas === 1 ? 'valoración de 1 estrella con comentario visible' : 'valoraciones de 1 estrella con comentario visible',
             'enlace'   => route('admin.valoraciones.index', ['puntaje' => 1, 'con_comentario' => 1])],
        ])->filter(fn ($fila) => $fila['cantidad'] > 0)->values();

        return [
            'atencion' => $atencion,

            'actividad' => [
                'grafica' => $recientes->map(fn ($d) => [
                    'etiqueta' => $this->dia($d->dia),
                    'valor'    => (int) $d->busquedas,
                ]),
                'busquedas'          => (int) $recientes->sum('busquedas'),
                'busquedasAntes'     => (int) $anteriores->sum('busquedas'),
                'promedioMs'         => $promedioMs($recientes),
                'promedioMsAntes'    => $promedioMs($anteriores),
                'activos'            => (int) $activos->semana,
                'activosAntes'       => (int) $activos->anterior,
            ],

            'catalogo' => [
                'recetas'      => (int) $recetas->total,
                'verificadas'  => (int) $recetas->verificadas,
                'recetasNuevas' => (int) $recetas->nuevas,
                'ingredientes' => (int) $ingredientes->total,
                'ingredientesNuevos' => (int) $ingredientes->nuevos,
                'estados'      => [
                    'pendiente' => (int) $recetas->pendiente,
                    'observada' => (int) $recetas->observada,
                    'aprobada'  => (int) $recetas->aprobada,
                    'rechazada' => (int) $recetas->rechazada,
                ],
            ],

            'personal' => [
                'despensa'   => (int) $despensa->total,
                'porCaducar' => (int) $despensa->por_caducar,
                'favoritos'  => $usuario->favoritos()->count(),
                'consultas'  => $usuario->consultas()->count(),
            ],

            'nota' => match ($atencion->count()) {
                0       => 'Nada pendiente en el sistema.',
                1       => 'Hay 1 asunto que requiere tu atención.',
                default => "Hay {$atencion->count()} asuntos que requieren tu atención.",
            },
        ];
    }

    /** 2026-10-07 -> mié 7 oct */
    private function dia(string $fecha): string
    {
        $dias  = ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb'];
        $meses = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $f = \Carbon\Carbon::parse($fecha);

        return $dias[$f->dayOfWeek] . ' ' . $f->day . ' ' . $meses[$f->month];
    }

    /**
     * La receta que encabeza el panel.
     *
     * Es la mejor del generador para la despensa de quien entra: así el
     * panel abre con algo que de verdad puede cocinar hoy, en vez de una
     * receta al azar. Si la despensa está vacía, no hay nada que destacar.
     */
    private function destacada(int $codUsuario, \Illuminate\Support\Collection $despensa): ?object
    {
        if ($despensa->isEmpty()) {
            return null;
        }

        return $this->generador->generar($codUsuario, [], 1)->first();
    }
}
