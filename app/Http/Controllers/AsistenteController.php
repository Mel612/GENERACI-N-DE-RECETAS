<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\CategoriaReceta;
use App\Models\Ingrediente;
use App\Models\Receta;
use App\Models\UnidadMedida;
use App\Services\AsistenteIA;
use App\Services\GeneradorRecetas;
use App\Services\RestriccionesDieta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

/**
 * Asistente de recetas con inteligencia artificial.
 *
 * La propuesta NO se guarda al recibirla: se muestra primero y la persona
 * decide si la conserva. Así el recetario y la bandeja del nutricionista no
 * se llenan de intentos descartados.
 *
 * Lo que sí se guarda entra siempre con origen `ia_generada` y estado
 * `pendiente`: nadie la ve en el recetario hasta que el nutricionista la
 * apruebe.
 */
class AsistenteController extends Controller
{
    /** Dónde vive el lote de propuestas mientras la persona decide. */
    private const EN_SESION = 'propuesta_ia';

    /** Índices del lote ya guardados => código de la receta creada. */
    private const GUARDADAS = 'propuesta_ia_guardadas';

    public function __construct(
        private AsistenteIA $asistente,
        private GeneradorRecetas $generador,
        private RestriccionesDieta $dieta,
    ) {}

    /** Pide un lote de propuestas a partir de la despensa. */
    public function proponer(Request $request): RedirectResponse
    {
        if (! $this->asistente->disponible()) {
            return back()->with('aviso',
                'El asistente de recetas no está configurado en este servidor.');
        }

        $despensa = $this->generador->despensa(Auth::id());

        $preferencias = $this->dieta->para(Auth::user())['aplicables']
            ->pluck('preferencia')->all();

        try {
            $resultado = $this->asistente->proponer(Auth::user(), $despensa, $preferencias);
        } catch (RuntimeException $e) {
            // El servicio lanza mensajes pensados para leerse en pantalla
            return back()->with('aviso', $e->getMessage());
        }

        // Se cuenta aquí y una sola vez: lo que gasta cuota es la llamada a
        // la API, no cuántas recetas traiga ni cuántas se guarden
        Bitacora::registrar('proponer_receta_ia', null, null, [
            'recetas'     => array_column($resultado['recetas'], 'nombre'),
            'descartadas' => $resultado['descartadas'],
        ]);

        $request->session()->put(self::EN_SESION, $resultado['recetas']);
        $request->session()->forget(self::GUARDADAS);

        $redireccion = redirect()->route('asistente.revisar');

        if ($resultado['descartadas'] > 0) {
            $n = $resultado['descartadas'];
            $redireccion->with('aviso', $n === 1
                ? 'El asistente propuso una receta más, pero no pasó la revisión y se descartó.'
                : "El asistente propuso {$n} recetas más, pero no pasaron la revisión y se descartaron.");
        }

        return $redireccion;
    }

    /**
     * El lote que está en la sesión, siempre como lista.
     *
     * Una sesión abierta antes de este cambio puede guardar una sola receta
     * suelta: se envuelve para no romperle la pantalla a quien la tenía.
     */
    private function lote(Request $request): array
    {
        $lote = $request->session()->get(self::EN_SESION);

        if (! is_array($lote) || $lote === []) {
            return [];
        }

        return isset($lote['nombre']) ? [$lote] : array_values($lote);
    }

    /** Muestra el lote de propuestas para que la persona elija. */
    public function revisar(Request $request): View|RedirectResponse
    {
        $lote = $this->lote($request);

        if (! $lote) {
            return redirect()->route('generador.index')
                ->with('aviso', 'No hay ninguna propuesta pendiente.');
        }

        $despensa = $this->generador->despensa(Auth::id())->keyBy('cod_ingrediente_inv');
        $filas    = collect($lote)->flatMap(fn ($p) => $p['ingredientes']);

        return view('asistente.revisar', [
            'lote'       => $lote,
            'guardadas'  => $request->session()->get(self::GUARDADAS, []),
            'categorias' => CategoriaReceta::whereIn('cod_categoria_receta', array_column($lote, 'categoria'))
                              ->get()->keyBy('cod_categoria_receta'),
            'ingredientes' => Ingrediente::whereIn('cod_ingrediente', $filas->pluck('ingrediente')->unique())
                              ->get()->keyBy('cod_ingrediente'),
            'unidades'   => UnidadMedida::whereIn('cod_unidad', $filas->pluck('unidad')->unique())
                              ->get()->keyBy('cod_unidad'),
            'despensa'   => $despensa,
            'niveles'    => ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'],
            'restantes'  => $this->asistente->restantesHoy(Auth::user()),
        ]);
    }

    /**
     * Guarda UNA receta del lote como pendiente de revisión.
     *
     * El resto del lote se queda en la sesión: se vuelve a la lista con la
     * guardada marcada para que la persona pueda guardar otra.
     */
    public function guardar(Request $request): RedirectResponse
    {
        $lote = $this->lote($request);

        if (! $lote) {
            return redirect()->route('generador.index')
                ->with('aviso', 'No hay ninguna propuesta pendiente.');
        }

        $indice = (int) $request->validate([
            'indice' => ['required', 'integer', 'min:0', 'max:' . (count($lote) - 1)],
        ])['indice'];

        $propuesta = $lote[$indice];
        $guardadas = $request->session()->get(self::GUARDADAS, []);

        // Doble clic o recarga: ya está guardada, no se crea otra igual
        if (isset($guardadas[$indice])) {
            return redirect()->route('asistente.revisar')
                ->with('aviso', "«{$propuesta['nombre']}» ya estaba guardada.");
        }

        // Entre que se propuso y se guarda pudo aparecer otra con ese nombre
        if (Receta::activas()->whereRaw('LOWER(nom_rec) = ?', [mb_strtolower($propuesta['nombre'])])->exists()) {
            return redirect()->route('asistente.revisar')
                ->with('aviso', "Ya existe una receta llamada «{$propuesta['nombre']}». Elige otra del lote.");
        }

        $receta = DB::transaction(function () use ($propuesta) {
            $receta = Receta::create([
                'cod_categoria_receta_rec' => $propuesta['categoria'],
                'cod_usuario_rec' => Auth::id(),
                'nom_rec'     => $propuesta['nombre'],
                'des_rec'     => $propuesta['descripcion'],
                'tie_pre_rec' => $propuesta['tiempo_preparacion'],
                'tie_coc_rec' => $propuesta['tiempo_coccion'],
                'por_rec'     => $propuesta['porciones'],
                'niv_rec'     => $propuesta['nivel'],

                // Las tres claves del asunto: se marca de dónde viene, entra a
                // revisión y no lleva el distintivo de verificada.
                'ori_rec'     => 'ia_generada',
                'est_val_rec' => 'pendiente',
                'ver_rec'     => false,

                // La nutrición la calcula el nutricionista, no la IA
                'act_rec'     => true,
            ]);

            foreach ($propuesta['ingredientes'] as $orden => $fila) {
                $receta->ingredientes()->create([
                    'cod_ingrediente_inr' => $fila['ingrediente'],
                    'cod_unidad_inr'      => $fila['unidad'],
                    'can_inr'             => $fila['cantidad'],
                    'opc_inr'             => $fila['opcional'],
                    'not_inr'             => $fila['nota'],
                    'ord_inr'             => $orden + 1,
                ]);
            }

            foreach ($propuesta['pasos'] as $i => $paso) {
                $receta->pasos()->create([
                    'num_pas' => $i + 1,
                    'ins_pas' => $paso['instruccion'],
                    'tie_pas' => $paso['tiempo'],
                    'cri_pas' => $paso['critico'],
                ]);
            }

            return $receta;
        });

        Bitacora::registrar('guardar_receta_ia', 'recetas', $receta->cod_receta, [
            'nombre' => $receta->nom_rec,
        ]);

        $guardadas[$indice] = $receta->cod_receta;
        $request->session()->put(self::GUARDADAS, $guardadas);

        return redirect()->route('asistente.revisar')->with('exito',
            "«{$receta->nom_rec}» se guardó. Quedó pendiente de revisión: hasta "
            . 'que el nutricionista la apruebe no llevará el distintivo de '
            . 'verificada. Puedes guardar otra del lote.');
    }

    /** Tira el lote entero. */
    public function descartar(Request $request): RedirectResponse
    {
        $request->session()->forget([self::EN_SESION, self::GUARDADAS]);

        return redirect()->route('generador.index')->with('exito', 'Propuesta descartada.');
    }
}
