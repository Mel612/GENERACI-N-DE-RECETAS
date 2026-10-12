<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Favorito;
use App\Models\Receta;
use App\Models\Valoracion;
use App\Services\GeneradorRecetas;
use App\Services\RestriccionesDieta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Las recetas vistas desde el lado de quien cocina.
 *
 * Historias 6 (ver la receta), 8 (favoritas) y las valoraciones.
 * A diferencia del panel de administración, aquí la receta se muestra
 * comparada contra la despensa de quien la mira: qué tiene y qué le falta.
 */
class RecetaUsuarioController extends Controller
{
    public function __construct(
        private GeneradorRecetas $generador,
        private RestriccionesDieta $dieta,
    ) {}

    /** Buscador general del recetario (Historias 5 y 16). */
    public function index(Request $request): View
    {
        $consulta = Receta::with(['categoria', 'etiquetas'])
            ->withCount('valoraciones')
            ->withAvg('valoraciones', 'pun_val')
            ->publicas();

        if ($request->filled('buscar')) {
            $texto = '%' . mb_strtolower($request->string('buscar')->toString()) . '%';
            $consulta->where(function ($q) use ($texto) {
                $q->whereRaw('LOWER(nom_rec) LIKE ?', [$texto])
                  ->orWhereRaw('LOWER(COALESCE(des_rec, \'\')) LIKE ?', [$texto])
                  // También busca por ingrediente: «qué hago con quinua»
                  ->orWhereHas('ingredientes.ingrediente',
                      fn ($i) => $i->whereRaw('LOWER(nom_ing) LIKE ?', [$texto]));
            });
        }

        $consulta
            ->when($request->filled('categoria'), fn ($q) => $q->where('cod_categoria_receta_rec', $request->integer('categoria')))
            ->when($request->filled('nivel'),     fn ($q) => $q->where('niv_rec', $request->input('nivel')))
            ->when($request->filled('tiempo_max'), fn ($q) => $q->where('tie_tot_rec', '<=', $request->integer('tiempo_max')))
            ->when($request->boolean('solo_verificadas'), fn ($q) => $q->where('ver_rec', true));

        if ($request->filled('etiquetas')) {
            $consulta->whereHas('etiquetas', fn ($q) =>
                $q->whereIn('receta_etiquetas.cod_etiqueta_ret', (array) $request->input('etiquetas')));
        }

        // El recetario se explora entero por defecto: aquí la persona está
        // mirando qué existe, no decidiendo qué cenar. El filtro por sus
        // preferencias está a un clic, pero no se impone.
        $dieta = $this->dieta->para(Auth::user());

        $segunDieta = $request->boolean('segun_mi_dieta') && $dieta['aplicables']->isNotEmpty();

        if ($segunDieta) {
            // Todas, no cualquiera: vegetariano Y sin gluten es más estricto
            foreach ($dieta['aplicables'] as $p) {
                $consulta->whereHas('etiquetas', fn ($q) =>
                    $q->where('receta_etiquetas.cod_etiqueta_ret', $p['cod_etiqueta']));
            }
        }

        return view('recetas.index', [
            'recetas'    => $consulta->orderBy('nom_rec')->paginate(12)->withQueryString(),
            'categorias' => \App\Models\CategoriaReceta::where('act_car', true)->orderBy('nom_car')->get(),
            'etiquetas'  => \App\Models\Etiqueta::where('act_eti', true)->orderBy('nom_eti')->get(),
            'niveles'    => ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'],
            'favoritas'  => $this->favoritasDelUsuario(),
            'dieta'      => $dieta,
            'segunDieta' => $segunDieta,
        ]);
    }

    /** Ficha completa, comparada contra la despensa de quien mira (Historia 6). */
    public function show(Receta $receta): View
    {
        // Una receta rechazada por el nutricionista deja de ser pública
        abort_if($receta->fec_eli_rec !== null
              || ! $receta->act_rec
              || $receta->est_val_rec === 'rechazada', 404);

        $receta->load([
            'categoria', 'etiquetas',
            'ingredientes.ingrediente', 'ingredientes.unidad',
            'pasos' => fn ($q) => $q->orderBy('num_pas'),
            'valoraciones.usuario' => fn ($q) => $q->orderByDesc('fec_cre_val'),
        ]);

        return view('recetas.show', [
            'receta'      => $receta,
            'comparacion' => $this->compararConDespensa($receta),
            'esFavorita'  => $this->favoritasDelUsuario()->contains($receta->cod_receta),
            'miValoracion' => Valoracion::where('cod_usuario_val', Auth::id())
                ->where('cod_receta_val', $receta->cod_receta)->first(),
            'niveles'     => ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'],
        ]);
    }

    /** Guarda o quita de favoritas (Historia 8). */
    public function alternarFavorita(Receta $receta): RedirectResponse
    {
        $favorito = Favorito::where('cod_usuario_fav', Auth::id())
            ->where('cod_receta_fav', $receta->cod_receta)
            ->first();

        if ($favorito) {
            $favorito->delete();
            $mensaje = "«{$receta->nom_rec}» se quitó de tus favoritas.";
        } else {
            Favorito::create([
                'cod_usuario_fav' => Auth::id(),
                'cod_receta_fav'  => $receta->cod_receta,
            ]);
            $mensaje = "«{$receta->nom_rec}» se guardó en tus favoritas.";
        }

        return back()->with('exito', $mensaje);
    }

    /** Listado de favoritas. */
    public function favoritas(): View
    {
        return view('recetas.favoritas', [
            'recetas' => Receta::with(['categoria', 'etiquetas'])
                ->withCount('valoraciones')
                ->withAvg('valoraciones', 'pun_val')
                ->whereIn('cod_receta', $this->favoritasDelUsuario())
                ->publicas()
                ->orderBy('nom_rec')
                ->get(),
            'niveles' => ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'],
        ]);
    }

    /** Deja o actualiza una valoración. */
    public function valorar(Request $request, Receta $receta): RedirectResponse
    {
        $datos = $request->validate([
            'pun_val' => ['required', 'integer', 'between:1,5'],
            'com_val' => ['nullable', 'string', 'max:1000'],
        ], [
            'pun_val.required' => 'Elige cuántas estrellas le das.',
            'pun_val.between'  => 'La puntuación va de 1 a 5.',
        ]);

        // La base tiene UNIQUE(usuario, receta): se actualiza en lugar de duplicar
        Valoracion::updateOrCreate(
            ['cod_usuario_val' => Auth::id(), 'cod_receta_val' => $receta->cod_receta],
            $datos + ['fec_act_val' => now()]
        );

        Bitacora::registrar('valorar', 'valoraciones', $receta->cod_receta, [
            'receta'  => $receta->nom_rec,
            'puntaje' => $datos['pun_val'],
        ]);

        return back()->with('exito', 'Gracias por tu valoración.');
    }

    // ------------------------------------------------------------------ apoyo

    private function favoritasDelUsuario()
    {
        return Favorito::where('cod_usuario_fav', Auth::id())->pluck('cod_receta_fav');
    }

    /**
     * Para cada ingrediente de la receta, dice si el usuario lo tiene.
     *
     * Usa la misma regla de conversión que el generador: unidades del mismo
     * tipo se comparan directo, y si son de tipos distintos se pasan a gramos
     * usando la densidad del ingrediente.
     */
    private function compararConDespensa(Receta $receta): array
    {
        $filas = DB::table('ingredientes_receta as ir')
            ->join('ingredientes as i', 'i.cod_ingrediente', '=', 'ir.cod_ingrediente_inr')
            ->join('unidades_medida as ur', 'ur.cod_unidad', '=', 'ir.cod_unidad_inr')
            ->leftJoin('inventario as inv', function ($j) {
                $j->on('inv.cod_ingrediente_inv', '=', 'ir.cod_ingrediente_inr')
                  ->where('inv.cod_usuario_inv', '=', Auth::id())
                  ->where('inv.act_inv', '=', true)
                  ->where('inv.can_inv', '>', 0);
            })
            ->leftJoin('unidades_medida as uv', 'uv.cod_unidad', '=', 'inv.cod_unidad_inv')
            ->where('ir.cod_receta_inr', $receta->cod_receta)
            ->select(
                'ir.cod_ingrediente_inr',
                DB::raw("
                    CASE
                        WHEN inv.cod_inventario IS NULL THEN false
                        WHEN ur.tip_uni = uv.tip_uni
                            THEN inv.can_inv * uv.fac_uni >= ir.can_inr * ur.fac_uni
                        WHEN i.den_ing IS NOT NULL
                         AND ur.tip_uni IN ('masa','volumen')
                         AND uv.tip_uni IN ('masa','volumen')
                            THEN (CASE WHEN uv.tip_uni = 'masa'
                                       THEN inv.can_inv * uv.fac_uni
                                       ELSE inv.can_inv * uv.fac_uni * i.den_ing END)
                              >= (CASE WHEN ur.tip_uni = 'masa'
                                       THEN ir.can_inr * ur.fac_uni
                                       ELSE ir.can_inr * ur.fac_uni * i.den_ing END)
                        ELSE false
                    END AS cubierto
                "),
                DB::raw('inv.cod_inventario IS NOT NULL AS en_despensa')
            )
            ->get();

        return $filas->mapWithKeys(fn ($f) => [
            $f->cod_ingrediente_inr => [
                'cubierto'    => (bool) $f->cubierto,
                'en_despensa' => (bool) $f->en_despensa,
            ],
        ])->all();
    }
}
