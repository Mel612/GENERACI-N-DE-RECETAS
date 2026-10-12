<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Valoracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Moderación de valoraciones.
 *
 * El administrador puede borrar un comentario inapropiado sin perder la
 * puntuación, o eliminar la valoración entera. Borrar solo el comentario es
 * lo habitual: el puntaje sigue siendo un dato legítimo del usuario.
 */
class ValoracionController extends Controller
{
    public function index(Request $request): View
    {
        $consulta = Valoracion::with(['usuario', 'receta']);

        if ($request->filled('puntaje')) {
            $consulta->where('pun_val', $request->integer('puntaje'));
        }

        if ($request->boolean('con_comentario')) {
            $consulta->whereNotNull('com_val')->where('com_val', '<>', '');
        }

        if ($request->filled('buscar')) {
            $texto = '%' . mb_strtolower($request->string('buscar')->toString()) . '%';
            $consulta->where(function ($q) use ($texto) {
                $q->whereRaw('LOWER(COALESCE(com_val, \'\')) LIKE ?', [$texto])
                  ->orWhereHas('receta', fn ($r) => $r->whereRaw('LOWER(nom_rec) LIKE ?', [$texto]))
                  ->orWhereHas('usuario', fn ($u) => $u->whereRaw('LOWER(nom_usu) LIKE ?', [$texto]));
            });
        }

        $distribucion = Valoracion::select('pun_val', DB::raw('COUNT(*) as valor'))
            ->groupBy('pun_val')->pluck('valor', 'pun_val');

        return view('admin.valoraciones.index', [
            'valoraciones' => $consulta->orderByDesc('fec_cre_val')->paginate(20)->withQueryString(),

            'resumen' => [
                'total'          => Valoracion::count(),
                'promedio'       => round((float) Valoracion::avg('pun_val'), 2),
                'conComentario'  => Valoracion::whereNotNull('com_val')->where('com_val', '<>', '')->count(),
                'negativas'      => Valoracion::where('pun_val', '<=', 2)->count(),
            ],

            'distribucion' => collect(range(5, 1))->map(fn ($p) => (object) [
                'etiqueta' => $p . ($p === 1 ? ' estrella' : ' estrellas'),
                'valor'    => (int) ($distribucion[$p] ?? 0),
            ]),
        ]);
    }

    /** Borra solo el comentario y conserva la puntuación. */
    public function ocultarComentario(Valoracion $valoracion): RedirectResponse
    {
        $valoracion->update(['com_val' => null, 'fec_act_val' => now()]);

        Bitacora::registrar('moderar_comentario', 'valoraciones', $valoracion->cod_valoracion, [
            'receta' => $valoracion->receta?->nom_rec,
        ]);

        return back()->with('exito', 'Comentario eliminado. La puntuación se conservó.');
    }

    /** Elimina la valoración completa. */
    public function destroy(Valoracion $valoracion): RedirectResponse
    {
        $receta = $valoracion->receta?->nom_rec;
        $valoracion->delete();

        Bitacora::registrar('eliminar', 'valoraciones', null, ['receta' => $receta]);

        return back()->with('exito', 'Valoración eliminada correctamente.');
    }
}
