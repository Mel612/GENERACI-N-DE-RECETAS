<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\PreferenciaDieta;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Historia de Usuario 1 - Registro de usuario.
 *
 * Requerimiento 2: datos personales obligatorios, validacion de campos y formato.
 * Todo registro publico entra con el rol Usuario; los demas roles los asigna
 * el administrador desde la gestion de usuarios.
 */
class RegistroController extends Controller
{
    public function mostrar(): View
    {
        return view('auth.registro', [
            'preferencias' => PreferenciaDieta::where('act_pre', true)->orderBy('nom_pre')->get(),
        ]);
    }

    public function registrar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nom_usu'         => ['required', 'string', 'min:3', 'max:100'],
            'cor_usu'         => ['required', 'string', 'email', 'max:255', 'unique:usuarios,cor_usu'],
            'con_usu'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'preferencias'    => ['nullable', 'array'],
            'preferencias.*'  => ['integer', 'exists:preferencias_dieta,cod_preferencia'],
        ], [
            'nom_usu.required'  => 'El nombre completo es obligatorio.',
            'nom_usu.min'       => 'El nombre debe tener al menos 3 caracteres.',
            'cor_usu.required'  => 'El correo electronico es obligatorio.',
            'cor_usu.email'     => 'El correo electronico no tiene un formato valido.',
            'cor_usu.unique'    => 'Ese correo electronico ya esta registrado.',
            'con_usu.required'  => 'La contrasena es obligatoria.',
            'con_usu.confirmed' => 'La confirmacion de la contrasena no coincide.',
        ]);

        $usuario = DB::transaction(function () use ($datos) {
            $usuario = Usuario::create([
                'nom_usu'     => $datos['nom_usu'],
                'cor_usu'     => $datos['cor_usu'],
                'con_usu'     => $datos['con_usu'], // el cast 'hashed' la encripta
                'cod_rol_usu' => Rol::USUARIO,
                'act_usu'     => true,
            ]);

            $usuario->preferencias()->sync($datos['preferencias'] ?? []);

            return $usuario;
        });

        Bitacora::registrar('registro_usuario', 'usuarios', $usuario->cod_usuario, [
            'correo' => $usuario->cor_usu,
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
                         ->with('exito', 'Su cuenta fue creada correctamente.');
    }
}
