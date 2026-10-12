<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\CategoriaReceta;
use App\Models\Etiqueta;
use App\Models\HistorialConsulta;
use App\Services\GeneradorRecetas;
use App\Services\AsistenteIA;
use App\Services\RestriccionesDieta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Generación de recetas a partir de la despensa.
 *
 * Historias 4, 5, 6, 16 y 17. Requerimientos 5, 6, 7, 13 y 14.
 *
 * El controlador solo recoge los filtros, llama al servicio y muestra lo que
 * devuelve. La comparación vive en App\Services\GeneradorRecetas.
 */
class GeneradorController extends Controller
{
    public function __construct(
        private GeneradorRecetas $generador,
        private RestriccionesDieta $dieta,
        private AsistenteIA $asistente,
    ) {}

    /** Formulario: qué tengo y qué quiero cocinar. */
    public function index(): View
    {
        $despensa = $this->generador->despensa(Auth::id());

        return view('generador.index', [
            'despensa'   => $despensa,
            'categorias' => CategoriaReceta::where('act_car', true)->orderBy('nom_car')->get(),
            'etiquetas'  => Etiqueta::where('act_eti', true)->orderBy('nom_eti')->get(),
            'niveles'    => ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'],
            'dieta'      => $this->dieta->para(Auth::user()),
        ]);
    }

    /** Ejecuta la generación y muestra los resultados. */
    public function generar(Request $request): View|RedirectResponse
    {
        $filtros = $request->validate([
            'ingredientes'   => ['nullable', 'array'],
            'ingredientes.*' => ['integer', 'exists:ingredientes,cod_ingrediente'],
            'categoria'      => ['nullable', 'integer', 'exists:categorias_receta,cod_categoria_receta'],
            'nivel'          => ['nullable', 'in:facil,medio,dificil'],
            'tiempo_max'     => ['nullable', 'integer', 'min:5', 'max:600'],
            'etiquetas'      => ['nullable', 'array'],
            'etiquetas.*'    => ['integer', 'exists:etiquetas,cod_etiqueta'],
            'solo_completas'   => ['nullable', 'boolean'],
            'solo_verificadas' => ['nullable', 'boolean'],
            'respetar_dieta'   => ['nullable', 'boolean'],
        ]);

        $despensa = $this->generador->despensa(Auth::id(), $filtros['ingredientes'] ?? null);

        if ($despensa->isEmpty()) {
            return redirect()->route('inventario.index')
                ->with('aviso', 'Tu despensa está vacía. Agrega al menos un ingrediente para generar recetas.');
        }

        // Las preferencias vienen marcadas en el formulario: quien es
        // vegetariano no debería tener que pedirlo en cada búsqueda. Se
        // pueden desmarcar para una búsqueda puntual, por ejemplo cuando se
        // cocina para otra persona.
        $dieta    = $this->dieta->para(Auth::user());
        $respetar = $request->boolean('respetar_dieta');

        if ($respetar && $dieta['aplicables']->isNotEmpty()) {
            $filtros['preferencias'] = $dieta['aplicables']->pluck('cod_etiqueta')->all();
        }

        // Se mide el tiempo real de la generación: alimenta el reporte de
        // eficiencia y el objetivo específico de evaluar el desempeño.
        $inicio     = hrtime(true);
        $resultados = $this->generador->generar(Auth::id(), $filtros);
        $milisegundos = (int) round((hrtime(true) - $inicio) / 1e6);

        $consulta = $this->generador->registrar(
            Auth::id(), $despensa, $filtros, $resultados, $milisegundos
        );

        Bitacora::registrar('generar_recetas', 'historial_consulta', $consulta->cod_consulta, [
            'ingredientes' => $despensa->count(),
            'resultados'   => $resultados->count(),
            'ms'           => $milisegundos,
        ]);

        return view('generador.resultados', [
            'resultados'   => $resultados,
            'despensa'     => $despensa,
            'filtros'      => $filtros,
            'milisegundos' => $milisegundos,
            'consulta'     => $consulta,
            'categorias'   => CategoriaReceta::where('act_car', true)->orderBy('nom_car')->get(),
            'etiquetas'    => Etiqueta::where('act_eti', true)->orderBy('nom_eti')->get(),
            'niveles'      => ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'],
            'dieta'        => $dieta,
            'dietaAplicada' => $respetar && $dieta['aplicables']->isNotEmpty(),
            'asistente'    => $this->asistente->disponible(),
            'restantesIa'  => $this->asistente->disponible()
                                ? $this->asistente->restantesHoy(Auth::user()) : 0,
        ]);
    }

    /** Historial de consultas (Historia 7, Requerimiento 15). */
    public function historial(): View
    {
        return view('generador.historial', [
            'consultas' => HistorialConsulta::with(['resultados.receta'])
                ->where('cod_usuario_con', Auth::id())
                ->orderByDesc('fec_cre_con')
                ->paginate(15),
        ]);
    }
}
