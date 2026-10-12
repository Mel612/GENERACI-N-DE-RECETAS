<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GestionaCatalogo;
use App\Http\Controllers\Concerns\ValidaNombres;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\CategoriaReceta;
use App\Services\AsistenteIA;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Catalogo de categorias de receta (desayunos, almuerzos, comida boliviana...). */
class CategoriaRecetaController extends Controller
{
    use GestionaCatalogo;

    use ValidaNombres;

    public function index(Request $request): View
    {
        $consulta = CategoriaReceta::withCount('recetas');
        $this->buscarEn($consulta, $request->string('buscar')->toString(), ['nom_car', 'des_car']);

        if ($request->filled('estado')) {
            $consulta->where('act_car', $request->input('estado') === 'activos');
        }

        return view('admin.categorias-receta.index', [
            'categorias' => $consulta->orderBy('nom_car')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categorias-receta.form', ['categoria' => new CategoriaReceta()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $categoria = CategoriaReceta::create(
            $this->validar($request) + ['act_car' => $request->boolean('act_car', true)]
        );

        Bitacora::registrar('crear', 'categorias_receta', $categoria->cod_categoria_receta, [
            'nombre' => $categoria->nom_car,
        ]);

        return redirect()->route('admin.categorias-receta.index')
                         ->with('exito', 'Categoría registrada correctamente.');
    }

    public function edit(CategoriaReceta $categoria): View
    {
        return view('admin.categorias-receta.form', [
            'categoria'      => $categoria,
            'recetasActivas' => $categoria->recetas()->activas()->count(),
        ]);
    }

    public function update(Request $request, CategoriaReceta $categoria): RedirectResponse
    {
        $categoria->update(
            $this->validar($request, $categoria->cod_categoria_receta)
            + ['act_car' => $request->boolean('act_car', true)]
        );

        Bitacora::registrar('editar', 'categorias_receta', $categoria->cod_categoria_receta);

        return redirect()->route('admin.categorias-receta.index')
                         ->with('exito', 'Categoría actualizada correctamente.');
    }

    public function destroy(CategoriaReceta $categoria): RedirectResponse
    {
        // Desactivarla con recetas activas dentro las dejaría en una categoría oculta
        $activas = $categoria->recetas()->activas()->count();

        if ($activas > 0) {
            return redirect()->route('admin.categorias-receta.index')->with('aviso',
                "No se puede eliminar «{$categoria->nom_car}»: tiene {$activas} receta(s) activa(s). "
                . 'Cámbialas de categoría o dalas de baja primero.');
        }

        if ($categoria->act_car && $this->esLaUltimaActiva($categoria->cod_categoria_receta)) {
            return redirect()->route('admin.categorias-receta.index')->with('aviso',
                "No se puede eliminar «{$categoria->nom_car}»: es la única categoría activa y las recetas necesitan al menos una.");
        }

        [$tipo, $mensaje] = $this->eliminarODesactivar($categoria, ['recetas'], 'act_car');

        return redirect()->route('admin.categorias-receta.index')->with($tipo, $mensaje);
    }

    /**
     * Valida y limpia los datos de la categoría.
     *
     * Además de las reglas comunes (ValidaNombres: sin números, sin
     * duplicados aunque cambien tildes o singular/plural):
     * 1. El nombre no lleva palabras sobrantes como «receta» o «categoría».
     * 2. No se desactiva si tiene recetas activas ni si es la última activa.
     * 3. La IA revisa que sirva para agrupar recetas y que no sea la misma
     *    idea que otra categoría con otras palabras («Dulces» y «Postres»);
     *    esto último se puede confirmar si de verdad es distinta.
     *
     * @return array{nom_car: string, des_car: ?string}
     *
     * @throws ValidationException
     */
    private function validar(Request $request, ?int $id = null): array
    {
        $this->limpiarTextos($request, ['nom_car', 'des_car']);

        $datos = $request->validate([
            'nom_car' => [
                ...$this->reglasNombre(CategoriaReceta::class, 'nom_car', $id, 'la categoría'),
                function (string $atributo, mixed $valor, \Closure $fallar) {
                    if (preg_match('/\b(recetas?|categor[ií]as?)\b/iu', (string) $valor, $palabra)) {
                        $fallar("No hace falta poner «{$palabra[1]}» en el nombre: ya se sabe que es una categoría de recetas. "
                              . 'Escribe solo el grupo, por ejemplo «Postres».');
                    }
                },
            ],
            'des_car' => $this->reglasDescripcion($request, 'nom_car'),
        ], $this->mensajesNombre('nom_car', 'la categoría', 'des_car'));

        if (! $request->boolean('act_car', true)) {
            $activas = $id ? CategoriaReceta::find($id)?->recetas()->activas()->count() ?? 0 : 0;

            if ($activas > 0) {
                throw ValidationException::withMessages([
                    'act_car' => "No puedes desactivar esta categoría: tiene {$activas} receta(s) activa(s). "
                               . 'Cámbialas de categoría o dalas de baja primero.',
                ]);
            }

            if ($id && CategoriaReceta::find($id)?->act_car && $this->esLaUltimaActiva($id)) {
                throw ValidationException::withMessages([
                    'act_car' => 'No puedes desactivarla: es la única categoría activa y las recetas necesitan al menos una.',
                ]);
            }
        }

        $this->revisarConIa($datos['nom_car'], $id, $request->boolean('confirmar_distinta'));

        return $datos;
    }

    /**
     * Pide a la IA que confirme que el nombre sirve como categoría de recetas
     * y que no repite con otras palabras una que ya existe.
     *
     * Solo consulta si el nombre es nuevo o cambió. Si la IA no responde,
     * se deja pasar.
     *
     * @throws ValidationException
     */
    private function revisarConIa(string $nombre, ?int $id, bool $confirmada): void
    {
        $anterior = $id ? CategoriaReceta::find($id)?->nom_car : null;

        if ($anterior !== null && $this->normalizar($anterior) === $this->normalizar($nombre)) {
            return;
        }

        $existentes = CategoriaReceta::query()
            ->when($id, fn ($q) => $q->where('cod_categoria_receta', '!=', $id))
            ->orderBy('nom_car')->pluck('nom_car')->all();

        $revision = app(AsistenteIA::class)->revisarCategoriaReceta(Auth::user(), $nombre, $existentes);

        if ($revision === null) {
            return;
        }

        if (! $revision['valida']) {
            throw ValidationException::withMessages([
                'nom_car' => "«{$nombre}» no sirve como categoría de recetas. {$revision['motivo']}",
            ]);
        }

        if ($revision['igual_a'] !== null && ! $confirmada) {
            throw ValidationException::withMessages([
                'nom_car' => "«{$nombre}» parece la misma categoría que «{$revision['igual_a']}». {$revision['motivo']} "
                           . 'Si de verdad es distinta, marca «Es una categoría distinta» y guarda de nuevo.',
            ]);
        }
    }

    /** ¿Es la única categoría activa que queda (sin contar las demás inactivas)? */
    private function esLaUltimaActiva(int $id): bool
    {
        return ! CategoriaReceta::where('act_car', true)->where('cod_categoria_receta', '!=', $id)->exists();
    }
}
