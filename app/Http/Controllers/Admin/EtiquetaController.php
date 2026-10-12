<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GestionaCatalogo;
use App\Http\Controllers\Concerns\ValidaNombres;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Etiqueta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Catalogo de etiquetas de receta (saludable, rapido, economico...). */
class EtiquetaController extends Controller
{
    use GestionaCatalogo;

    use ValidaNombres;

    public function index(Request $request): View
    {
        $consulta = Etiqueta::withCount('recetas');
        $this->buscarEn($consulta, $request->string('buscar')->toString(), ['nom_eti']);

        if ($request->filled('estado')) {
            $consulta->where('act_eti', $request->input('estado') === 'activos');
        }

        return view('admin.etiquetas.index', [
            'etiquetas' => $consulta->orderBy('nom_eti')->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.etiquetas.form', ['etiqueta' => new Etiqueta()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $etiqueta = Etiqueta::create(
            $this->validar($request) + ['act_eti' => $request->boolean('act_eti', true)]
        );

        Bitacora::registrar('crear', 'etiquetas', $etiqueta->cod_etiqueta, ['nombre' => $etiqueta->nom_eti]);

        return redirect()->route('admin.etiquetas.index')->with('exito', 'Etiqueta registrada correctamente.');
    }

    public function edit(Etiqueta $etiqueta): View
    {
        return view('admin.etiquetas.form', compact('etiqueta'));
    }

    public function update(Request $request, Etiqueta $etiqueta): RedirectResponse
    {
        $etiqueta->update(
            $this->validar($request, $etiqueta->cod_etiqueta) + ['act_eti' => $request->boolean('act_eti', true)]
        );

        Bitacora::registrar('editar', 'etiquetas', $etiqueta->cod_etiqueta);

        return redirect()->route('admin.etiquetas.index')->with('exito', 'Etiqueta actualizada correctamente.');
    }

    public function destroy(Etiqueta $etiqueta): RedirectResponse
    {
        [$tipo, $mensaje] = $this->eliminarODesactivar($etiqueta, ['recetas'], 'act_eti');

        return redirect()->route('admin.etiquetas.index')->with($tipo, $mensaje);
    }

    /**
     * Valida y limpia los datos de la etiqueta (reglas comunes en ValidaNombres).
     *
     * @return array{nom_eti: string, col_eti: string}
     */
    private function validar(Request $request, ?int $id = null): array
    {
        $this->limpiarTextos($request, ['nom_eti']);

        $datos = $request->validate([
            'nom_eti' => $this->reglasNombre(Etiqueta::class, 'nom_eti', $id, 'la etiqueta', maximo: 40),
            'col_eti' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], $this->mensajesNombre('nom_eti', 'la etiqueta') + [
            'col_eti.regex' => 'El color debe estar en formato hexadecimal, por ejemplo #16A34A.',
        ]);

        $this->comprobarConIa('nom_eti', $datos['nom_eti'], Etiqueta::class, $id,
            'una etiqueta que describe recetas de cocina (por ejemplo: rápido, picante, económico)');

        return $datos;
    }
}
