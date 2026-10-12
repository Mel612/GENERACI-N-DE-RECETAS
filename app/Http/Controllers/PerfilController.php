<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\PreferenciaDieta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/** Historia de Usuario 18 - Editar perfil. */
class PerfilController extends Controller
{
    public function edit(): View
    {
        return view('perfil.edit', [
            'usuario'      => Auth::user()->load('preferencias'),
            'preferencias' => PreferenciaDieta::where('act_pre', true)->orderBy('nom_pre')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        $datos = $request->validate([
            'nom_usu'        => ['required', 'string', 'min:3', 'max:100'],
            'cor_usu'        => ['required', 'email', 'max:255', 'unique:usuarios,cor_usu,'.$usuario->cod_usuario.',cod_usuario'],
            'preferencias'   => ['nullable', 'array'],
            'preferencias.*' => ['integer', 'exists:preferencias_dieta,cod_preferencia'],
        ]);

        $usuario->update([
            'nom_usu'     => $datos['nom_usu'],
            'cor_usu'     => $datos['cor_usu'],
            'fec_act_usu' => now(),
        ]);

        $usuario->preferencias()->sync($datos['preferencias'] ?? []);

        Bitacora::registrar('editar_perfil', 'usuarios', $usuario->cod_usuario);

        return back()->with('exito', 'Perfil actualizado correctamente.');
    }

    public function cambiarPassword(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        $datos = $request->validate([
            'con_actual' => ['required', 'string'],
            'con_usu'    => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'con_actual.required' => 'Debe ingresar su contrasena actual.',
            'con_usu.confirmed'   => 'La confirmacion de la nueva contrasena no coincide.',
        ]);

        if (! Hash::check($datos['con_actual'], $usuario->con_usu)) {
            return back()->withErrors(['con_actual' => 'La contrasena actual no es correcta.']);
        }

        $usuario->update([
            'con_usu'     => $datos['con_usu'],
            'fec_act_usu' => now(),
        ]);

        Bitacora::registrar('cambio_password', 'usuarios', $usuario->cod_usuario);

        return back()->with('exito', 'Contrasena actualizada correctamente.');
    }
}
