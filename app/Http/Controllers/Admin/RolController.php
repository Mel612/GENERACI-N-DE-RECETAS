<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion de roles y visualizacion de permisos.
 *
 * Los tres roles base (Administrador, Nutricionista y Usuario) estan
 * referenciados por nombre en el middleware VerificarRol, asi que no se
 * pueden renombrar, desactivar ni eliminar: hacerlo dejaria rutas
 * inaccesibles para todos. El sistema lo impide y lo explica.
 */
class RolController extends Controller
{
    /** Roles que el sistema necesita para funcionar. */
    public const PROTEGIDOS = [Rol::ADMINISTRADOR, Rol::NUTRICIONISTA, Rol::USUARIO];

    /**
     * Matriz de permisos, derivada de lo que realmente hacen las rutas.
     * Es informativa: la restriccion real vive en el middleware.
     */
    public const PERMISOS = [
        'Acceder al sistema'              => ['Administrador', 'Nutricionista', 'Usuario'],
        'Editar su propio perfil'         => ['Administrador', 'Nutricionista', 'Usuario'],
        'Administrar usuarios'            => ['Administrador'],
        'Administrar roles'               => ['Administrador'],
        'Administrar el catálogo de ingredientes' => ['Administrador', 'Nutricionista'],
        'Administrar unidades de medida'  => ['Administrador'],
        'Administrar categorías y etiquetas' => ['Administrador'],
        'Administrar preferencias alimentarias' => ['Administrador'],
        'Ver reportes del sistema'        => ['Administrador'],
        'Consultar la bitácora'           => ['Administrador'],
        'Moderar valoraciones'            => ['Administrador'],
        'Validar y aprobar recetas'       => ['Nutricionista'],
        'Registrar su inventario'         => ['Usuario'],
        'Generar recetas con sus ingredientes' => ['Usuario'],
    ];

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles'    => Rol::withCount('usuarios')->orderBy('cod_rol')->get(),
            'permisos' => self::PERMISOS,
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', ['rol' => new Rol()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $rol = Rol::create($this->validar($request) + ['act_rol' => $request->boolean('act_rol', true)]);

        Bitacora::registrar('crear', 'roles', $rol->cod_rol, ['nombre' => $rol->nom_rol]);

        return redirect()->route('admin.roles.index')
                         ->with('aviso', "Rol «{$rol->nom_rol}» creado. Para que tenga permisos reales, "
                                       . 'hay que agregarlo al middleware de las rutas correspondientes.');
    }

    public function edit(Rol $rol): View
    {
        return view('admin.roles.form', compact('rol'));
    }

    public function update(Request $request, Rol $rol): RedirectResponse
    {
        $protegido = in_array($rol->cod_rol, self::PROTEGIDOS, true);
        $datos     = $this->validar($request, $rol->cod_rol);

        if ($protegido) {
            // El nombre y el estado quedan fijos: el middleware depende de ellos.
            if ($datos['nom_rol'] !== $rol->nom_rol) {
                return back()->withInput()->withErrors([
                    'nom_rol' => "«{$rol->nom_rol}» es un rol del sistema. Cambiarle el nombre dejaría "
                               . 'sin acceso a todas las pantallas que lo exigen.',
                ]);
            }

            $datos['act_rol'] = true;
        } else {
            $datos['act_rol'] = $request->boolean('act_rol', true);
        }

        $rol->update($datos);

        Bitacora::registrar('editar', 'roles', $rol->cod_rol);

        return redirect()->route('admin.roles.index')->with('exito', 'Rol actualizado correctamente.');
    }

    public function destroy(Rol $rol): RedirectResponse
    {
        if (in_array($rol->cod_rol, self::PROTEGIDOS, true)) {
            return back()->withErrors([
                'rol' => "«{$rol->nom_rol}» es un rol del sistema y no se puede eliminar.",
            ]);
        }

        if ($rol->usuarios()->exists()) {
            return back()->withErrors([
                'rol' => "«{$rol->nom_rol}» tiene usuarios asignados. Cámbialos de rol antes de eliminarlo.",
            ]);
        }

        $nombre = $rol->nom_rol;
        $rol->delete();

        Bitacora::registrar('eliminar', 'roles', null, ['nombre' => $nombre]);

        return redirect()->route('admin.roles.index')->with('exito', 'Rol eliminado correctamente.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nom_rol' => ['required', 'string', 'max:50',
                          'unique:roles,nom_rol' . ($id ? ",{$id},cod_rol" : '')],
            'des_rol' => ['nullable', 'string', 'max:255'],
        ], [
            'nom_rol.required' => 'El nombre del rol es obligatorio.',
            'nom_rol.unique'   => 'Ya existe un rol con ese nombre.',
        ]);
    }
}
