<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GestionaCatalogo;
use App\Http\Controllers\Concerns\ValidaNombres;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\CategoriaIngrediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Catalogo de categorias de ingrediente (verduras, carnes, lacteos...). */
class CategoriaIngredienteController extends Controller
{
    use GestionaCatalogo;

    use ValidaNombres;

    public function index(Request $request): View
    {
        $consulta = CategoriaIngrediente::withCount('ingredientes');
        $this->buscarEn($consulta, $request->string('buscar')->toString(), ['nom_cat', 'des_cat']);

        if ($request->filled('estado')) {
            $consulta->where('act_cat', $request->input('estado') === 'activos');
        }

        return view('admin.categorias-ingrediente.index', [
            'categorias' => $consulta->orderBy('nom_cat')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categorias-ingrediente.form', ['categoria' => new CategoriaIngrediente()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        $categoria = CategoriaIngrediente::create($datos + ['act_cat' => $request->boolean('act_cat', true)]);

        Bitacora::registrar('crear', 'categorias_ingrediente', $categoria->cod_categoria_ingrediente, [
            'nombre' => $categoria->nom_cat,
        ]);

        return redirect()->route('admin.categorias-ingrediente.index')
                         ->with('exito', 'Categoría registrada correctamente.');
    }

    public function edit(CategoriaIngrediente $categoria): View
    {
        return view('admin.categorias-ingrediente.form', ['categoria' => $categoria]);
    }

    public function update(Request $request, CategoriaIngrediente $categoria): RedirectResponse
    {
        $datos = $this->validar($request, $categoria->cod_categoria_ingrediente);

        $categoria->update($datos + ['act_cat' => $request->boolean('act_cat', true)]);

        Bitacora::registrar('editar', 'categorias_ingrediente', $categoria->cod_categoria_ingrediente);

        return redirect()->route('admin.categorias-ingrediente.index')
                         ->with('exito', 'Categoría actualizada correctamente.');
    }

    public function destroy(CategoriaIngrediente $categoria): RedirectResponse
    {
        // Desactivarla con ingredientes activos dentro los dejaría en una
        // categoría oculta: primero hay que moverlos o desactivarlos.
        $activos = $categoria->ingredientes()->activos()->count();

        if ($activos > 0) {
            return redirect()->route('admin.categorias-ingrediente.index')->with('aviso',
                "No se puede eliminar «{$categoria->nom_cat}»: tiene {$activos} ingrediente(s) activo(s). "
                . 'Muévelos a otra categoría o desactívalos primero.');
        }

        [$tipo, $mensaje] = $this->eliminarODesactivar($categoria, ['ingredientes'], 'act_cat');

        return redirect()->route('admin.categorias-ingrediente.index')->with($tipo, $mensaje);
    }

    /**
     * Valida y limpia los datos de la categoría.
     *
     * Además de las reglas comunes de nombres (ValidaNombres), la IA revisa
     * que sea una categoría de alimentos, y no se puede desactivar una
     * categoría que todavía tiene ingredientes activos.
     *
     * @return array{nom_cat: string, des_cat: ?string}
     *
     * @throws ValidationException
     */
    private function validar(Request $request, ?int $id = null): array
    {
        $this->limpiarTextos($request, ['nom_cat', 'des_cat']);

        $datos = $request->validate([
            'nom_cat' => $this->reglasNombre(CategoriaIngrediente::class, 'nom_cat', $id, 'la categoría'),
            'des_cat' => $this->reglasDescripcion($request, 'nom_cat'),
        ], $this->mensajesNombre('nom_cat', 'la categoría', 'des_cat'));

        if ($id && ! $request->boolean('act_cat', true)) {
            $activos = CategoriaIngrediente::find($id)?->ingredientes()->activos()->count() ?? 0;

            if ($activos > 0) {
                throw ValidationException::withMessages([
                    'act_cat' => "No puedes desactivar esta categoría: tiene {$activos} ingrediente(s) activo(s). "
                               . 'Muévelos a otra categoría o desactívalos primero.',
                ]);
            }
        }

        $this->comprobarConIa('nom_cat', $datos['nom_cat'], CategoriaIngrediente::class, $id,
            'una categoría de ingredientes de cocina');

        return $datos;
    }
}


