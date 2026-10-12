<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Historias de Usuario 14 y 15 - Gestion y eliminacion de usuarios.
 * Requerimiento 3: solo accesible para el rol Administrador.
 */
class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = Usuario::with('rol')
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $texto = mb_strtolower($request->string('buscar')->toString());
                $q->where(function ($sub) use ($texto) {
                    $sub->whereRaw('LOWER(nom_usu) LIKE ?', ["%{$texto}%"])
                        ->orWhereRaw('LOWER(cor_usu) LIKE ?', ["%{$texto}%"]);
                });
            })
            ->when($request->filled('rol'), fn ($q) => $q->where('cod_rol_usu', $request->integer('rol')))
            ->orderBy('nom_usu')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles'    => Rol::where('act_rol', true)->orderBy('cod_rol')->get(),
        ]);
    }

    public function create(): View
    {
        return view('usuarios.form', [
            'usuario' => new Usuario(),
            'roles'   => Rol::where('act_rol', true)->orderBy('cod_rol')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nom_usu'     => ['required', 'string', 'min:3', 'max:100'],
            'cor_usu'     => ['required', 'email', 'max:255', 'unique:usuarios,cor_usu'],
            'con_usu'     => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'cod_rol_usu' => ['required', 'integer', 'exists:roles,cod_rol'],
            'act_usu'     => ['boolean'],
        ]);

        $usuario = Usuario::create([
            ...$datos,
            'act_usu' => $request->boolean('act_usu', true),
        ]);

        Bitacora::registrar('crear_usuario', 'usuarios', $usuario->cod_usuario, [
            'correo' => $usuario->cor_usu,
            'rol'    => $usuario->cod_rol_usu,
        ]);

        return redirect()->route('usuarios.index')
                         ->with('exito', 'Usuario registrado correctamente.');
    }

    public function edit(Usuario $usuario): View
    {
        return view('usuarios.form', [
            'usuario' => $usuario,
            'roles'   => Rol::where('act_rol', true)->orderBy('cod_rol')->get(),
        ]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'nom_usu'     => ['required', 'string', 'min:3', 'max:100'],
            'cor_usu'     => ['required', 'email', 'max:255', 'unique:usuarios,cor_usu,'.$usuario->cod_usuario.',cod_usuario'],
            'con_usu'     => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'cod_rol_usu' => ['required', 'integer', 'exists:roles,cod_rol'],
            'act_usu'     => ['boolean'],
        ]);

        // Un administrador no puede quitarse a si mismo el rol ni desactivarse.
        if ($usuario->cod_usuario === Auth::id()
            && ((int) $datos['cod_rol_usu'] !== Rol::ADMINISTRADOR || ! $request->boolean('act_usu', true))) {
            return back()->withInput()
                         ->withErrors(['cod_rol_usu' => 'No puede modificar su propio rol ni desactivar su cuenta.']);
        }

        if (blank($datos['con_usu'])) {
            unset($datos['con_usu']);
        }

        $usuario->update([
            ...$datos,
            'act_usu'     => $request->boolean('act_usu', true),
            'fec_act_usu' => now(),
        ]);

        Bitacora::registrar('editar_usuario', 'usuarios', $usuario->cod_usuario);

        return redirect()->route('usuarios.index')
                         ->with('exito', 'Usuario actualizado correctamente.');
    }

    /**
     * Historia 15 - Eliminar usuarios.
     * Se aplica borrado logico para no perder su inventario, historial y valoraciones.
     */
    public function destroy(Usuario $usuario): RedirectResponse
    {
        if ($usuario->cod_usuario === Auth::id()) {
            return back()->withErrors(['usuario' => 'No puede eliminar su propia cuenta.']);
        }

        $usuario->update([
            'act_usu'     => false,
            'fec_eli_usu' => now(),
        ]);

        Bitacora::registrar('eliminar_usuario', 'usuarios', $usuario->cod_usuario, [
            'correo' => $usuario->cor_usu,
        ]);

        return redirect()->route('usuarios.index')
                         ->with('exito', 'Usuario dado de baja correctamente.');
    }
}
