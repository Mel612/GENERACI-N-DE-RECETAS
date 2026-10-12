<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Historia de Usuario 2 - Inicio de sesion.
 *
 * Requerimiento 1: el acceso exige correo y contrasena validos y se bloquea
 * temporalmente al usuario despues de 3 intentos fallidos.
 */
class LoginController extends Controller
{
    private const MAX_INTENTOS = 3;
    private const BLOQUEO_SEGUNDOS = 60;

    public function mostrar(): View
    {
        return view('auth.login');
    }

    public function autenticar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cor_usu' => ['required', 'string', 'email', 'max:255'],
            'con_usu' => ['required', 'string'],
        ], [
            'cor_usu.required' => 'El correo electronico es obligatorio.',
            'cor_usu.email'    => 'El correo electronico no tiene un formato valido.',
            'con_usu.required' => 'La contrasena es obligatoria.',
        ]);

        $llave = $this->llaveDeIntentos($request);

        // Si ya agoto los intentos, no se evalua la contrasena.
        if (RateLimiter::tooManyAttempts($llave, self::MAX_INTENTOS)) {
            $segundos = RateLimiter::availableIn($llave);

            throw ValidationException::withMessages([
                'cor_usu' => "Demasiados intentos fallidos. Intente nuevamente en {$segundos} segundos.",
            ]);
        }

        $credenciales = [
            'cor_usu'  => $datos['cor_usu'],
            'password' => $datos['con_usu'],
            'act_usu'  => true,
        ];

        if (! Auth::attempt($credenciales, $request->boolean('recordarme'))) {
            RateLimiter::hit($llave, self::BLOQUEO_SEGUNDOS);

            $restantes = self::MAX_INTENTOS - RateLimiter::attempts($llave);

            throw ValidationException::withMessages([
                'cor_usu' => $restantes > 0
                    ? "Las credenciales no coinciden. Le quedan {$restantes} intento(s)."
                    : 'Las credenciales no coinciden. Su acceso quedo bloqueado temporalmente.',
            ]);
        }

        RateLimiter::clear($llave);
        $request->session()->regenerate();

        $usuario = Auth::user();
        $usuario->forceFill(['ult_acc_usu' => now()])->save();

        Bitacora::registrar('inicio_sesion', 'usuarios', $usuario->cod_usuario);

        return redirect()->intended(route('dashboard'))
                         ->with('exito', "Bienvenida/o, {$usuario->nom_usu}.");
    }

    public function salir(Request $request): RedirectResponse
    {
        Bitacora::registrar('cierre_sesion', 'usuarios', Auth::id());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('exito', 'Sesion cerrada correctamente.');
    }

    private function llaveDeIntentos(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('cor_usu')).'|'.$request->ip());
    }
}
