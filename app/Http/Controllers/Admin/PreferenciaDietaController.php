<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GestionaCatalogo;
use App\Http\Controllers\Concerns\ValidaNombres;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\PreferenciaDieta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Catalogo de preferencias alimentarias que el usuario elige en su perfil. */
class PreferenciaDietaController extends Controller
{
    use GestionaCatalogo;

    use ValidaNombres;

    public function index(Request $request): View
    {
        $consulta = PreferenciaDieta::withCount('usuarios');
        $this->buscarEn($consulta, $request->string('buscar')->toString(), ['nom_pre', 'des_pre']);

        if ($request->filled('estado')) {
            $consulta->where('act_pre', $request->input('estado') === 'activos');
        }

        return view('admin.preferencias.index', [
            'preferencias' => $consulta->orderBy('nom_pre')->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.preferencias.form', ['preferencia' => new PreferenciaDieta()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $preferencia = PreferenciaDieta::create(
            $this->validar($request) + ['act_pre' => $request->boolean('act_pre', true)]
        );

        Bitacora::registrar('crear', 'preferencias_dieta', $preferencia->cod_preferencia, [
            'nombre' => $preferencia->nom_pre,
        ]);

        return redirect()->route('admin.preferencias.index')->with('exito', 'Preferencia registrada correctamente.');
    }

    public function edit(PreferenciaDieta $preferencia): View
    {
        return view('admin.preferencias.form', compact('preferencia'));
    }

    public function update(Request $request, PreferenciaDieta $preferencia): RedirectResponse
    {
        $preferencia->update(
            $this->validar($request, $preferencia->cod_preferencia) + ['act_pre' => $request->boolean('act_pre', true)]
        );

        Bitacora::registrar('editar', 'preferencias_dieta', $preferencia->cod_preferencia);

        return redirect()->route('admin.preferencias.index')->with('exito', 'Preferencia actualizada correctamente.');
    }

    public function destroy(PreferenciaDieta $preferencia): RedirectResponse
    {
        [$tipo, $mensaje] = $this->eliminarODesactivar($preferencia, ['usuarios'], 'act_pre');

        return redirect()->route('admin.preferencias.index')->with($tipo, $mensaje);
    }

    /**
     * Valida y limpia los datos de la preferencia (reglas comunes en ValidaNombres).
     *
     * @return array{nom_pre: string, des_pre: ?string}
     */
    private function validar(Request $request, ?int $id = null): array
    {
        $this->limpiarTextos($request, ['nom_pre', 'des_pre']);

        $datos = $request->validate([
            'nom_pre' => $this->reglasNombre(PreferenciaDieta::class, 'nom_pre', $id, 'la preferencia'),
            'des_pre' => $this->reglasDescripcion($request, 'nom_pre'),
        ], $this->mensajesNombre('nom_pre', 'la preferencia', 'des_pre'));

        $this->comprobarConIa('nom_pre', $datos['nom_pre'], PreferenciaDieta::class, $id,
            'una preferencia o restricción alimentaria (por ejemplo: vegano, sin gluten)');

        return $datos;
    }
}
