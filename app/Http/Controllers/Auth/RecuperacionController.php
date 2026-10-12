<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EnlaceRecuperacion;
use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Recuperación de contraseña.
 *
 * Complementa la Historia 2 (inicio de sesión): sin esto, quien olvida su
 * contraseña depende de que el administrador se la cambie a mano.
 *
 * ── El flujo ─────────────────────────────────────────────────────────────
 *
 *   1. La persona pide un enlace con su correo.
 *   2. Se genera un token al azar, se guarda su hash y se envía el original
 *      por correo dentro de un enlace.
 *   3. La persona abre el enlace y escribe su nueva contraseña.
 *   4. El token se borra: sirve una sola vez.
 *
 * ── Decisiones de seguridad ──────────────────────────────────────────────
 *
 * **El token se guarda hasheado**, igual que una contraseña. Si alguien
 * llegara a leer la tabla, no podría usar lo que ve para entrar a ninguna
 * cuenta. Por eso la validación recorre los candidatos y compara con
 * Hash::check en lugar de buscar el token directo.
 *
 * **La respuesta es siempre la misma**, exista o no el correo. Si dijera
 * «ese correo no está registrado», cualquiera podría averiguar quién tiene
 * cuenta probando correos uno por uno.
 *
 * **El token vence a la hora** y solo se puede pedir uno cada minuto, para
 * que nadie llene el buzón de otra persona pidiendo enlaces sin parar.
 *
 * ── Un solo reloj ────────────────────────────────────────────────────────
 *
 * Las fechas se escriben y se comparan con CURRENT_TIMESTAMP, o sea con el
 * reloj de PostgreSQL, nunca con el de PHP.
 *
 * No es un capricho. PostgreSQL en Laragon corre en America/La_Paz y Laravel
 * sin configurar corre en UTC: cuatro horas de diferencia. Si se escribiera
 * con un reloj y se comparara con el otro, todos los enlaces nacerían
 * vencidos. Con un solo reloj, la caducidad funciona sin importar cómo esté
 * configurada la aplicación.
 *
 * (Aun así conviene poner APP_TIMEZONE=America/La_Paz en el .env: el resto
 * del sistema sí compara fechas de PostgreSQL contra el now() de PHP.)
 */
class RecuperacionController extends Controller
{
    /** Minutos que vale un enlace. */
    private const VIGENCIA = 60;

    /** Segundos que hay que esperar entre una solicitud y la siguiente. */
    private const ESPERA = 60;

    // ------------------------------------------------------ pedir el enlace

    public function solicitar(): View
    {
        return view('auth.recuperar');
    }

    public function enviar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cor_usu' => ['required', 'string', 'email', 'max:255'],
        ], [
            'cor_usu.required' => 'Escribe tu correo electrónico.',
            'cor_usu.email'    => 'Ese correo no tiene un formato válido.',
        ]);

        $correo = mb_strtolower(trim($datos['cor_usu']));

        // Mismo mensaje pase lo que pase: no se revela quién tiene cuenta.
        $respuesta = redirect()->route('password.solicitar')->with('exito',
            'Si ese correo tiene una cuenta, te enviamos un enlace para '
            . 'restablecer tu contraseña. Revisa tu bandeja de entrada y la '
            . 'carpeta de correo no deseado. El enlace vence en una hora.');

        $usuario = Usuario::whereRaw('LOWER(cor_usu) = ?', [$correo])
            ->where('act_usu', true)
            ->whereNull('fec_eli_usu')
            ->first();

        if (! $usuario) {
            return $respuesta;
        }

        // Ya pidió uno hace muy poco: no se manda otro, pero tampoco se avisa
        $reciente = DB::table('password_reset_tokens')
            ->where('email', $usuario->cor_usu)
            ->whereRaw("created_at > CURRENT_TIMESTAMP - INTERVAL '" . self::ESPERA . " seconds'")
            ->exists();

        if ($reciente) {
            return $respuesta;
        }

        $token = Str::random(64);

        // Un correo, un token vigente: el anterior deja de servir
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $usuario->cor_usu],
            ['token' => Hash::make($token), 'created_at' => DB::raw('CURRENT_TIMESTAMP')]
        );

        $enlace = route('password.restablecer', ['token' => $token])
                . '?correo=' . urlencode($usuario->cor_usu);

        try {
            Mail::to($usuario->cor_usu)->send(new EnlaceRecuperacion($usuario, $enlace, self::VIGENCIA));
        } catch (\Throwable $e) {
            // Si el correo no sale, la persona no debe quedarse esperando algo
            // que nunca llegará ni ver el detalle del error.
            Log::error('No se pudo enviar el enlace de recuperación', [
                'usuario' => $usuario->cod_usuario,
                'error'   => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'cor_usu' => 'No pudimos enviar el correo en este momento. '
                           . 'Intenta de nuevo en unos minutos o pide ayuda al administrador.',
            ]);
        }

        Bitacora::registrar('solicitar_recuperacion', 'usuarios', $usuario->cod_usuario);

        // Atajo para desarrollo: ver el enlace sin servidor de correo montado.
        //
        // Dos candados, y el primero es el que importa: aunque alguien active
        // la variable por error en el servidor, mientras APP_ENV no sea local
        // el enlace no se muestra. Enseñarlo en producción sería regalarle el
        // acceso a cualquiera que escriba el correo de otra persona.
        //
        // El env() de respaldo es para que funcione aunque no se agregue la
        // clave a config/app.php. Si alguna vez se usa `php artisan
        // config:cache`, hay que agregarla ahí: env() deja de leerse.
        $mostrar = config('app.mostrar_enlace_recuperacion', env('MOSTRAR_ENLACE_RECUPERACION', false));

        if (app()->environment('local') && $mostrar) {
            return $respuesta->with('enlace_desarrollo', $enlace);
        }

        return $respuesta;
    }

    // --------------------------------------------------- usar el enlace

    public function restablecer(Request $request, string $token): View|RedirectResponse
    {
        $correo = (string) $request->query('correo', '');

        if (! $this->filaValida($correo, $token)) {
            return redirect()->route('password.solicitar')->withErrors([
                'cor_usu' => 'Ese enlace ya no sirve: o venció, o ya lo usaste. Pide uno nuevo.',
            ]);
        }

        return view('auth.restablecer', ['token' => $token, 'correo' => $correo]);
    }

    public function guardar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'token'   => ['required', 'string'],
            'cor_usu' => ['required', 'string', 'email'],
            'con_usu' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'con_usu.required'  => 'Escribe tu nueva contraseña.',
            'con_usu.confirmed' => 'La confirmación no coincide con la contraseña.',
        ]);

        if (! $this->filaValida($datos['cor_usu'], $datos['token'])) {
            return redirect()->route('password.solicitar')->withErrors([
                'cor_usu' => 'Ese enlace ya no sirve: o venció, o ya lo usaste. Pide uno nuevo.',
            ]);
        }

        $usuario = Usuario::whereRaw('LOWER(cor_usu) = ?', [mb_strtolower($datos['cor_usu'])])
            ->where('act_usu', true)
            ->whereNull('fec_eli_usu')
            ->first();

        // La cuenta pudo darse de baja entre que se pidió el enlace y se usó
        if (! $usuario) {
            return redirect()->route('password.solicitar')->withErrors([
                'cor_usu' => 'Esa cuenta ya no está disponible. Comunícate con el administrador.',
            ]);
        }

        DB::transaction(function () use ($usuario, $datos) {
            $usuario->update([
                'con_usu'     => $datos['con_usu'], // el cast 'hashed' la encripta
                'fec_act_usu' => now(),
            ]);

            // Un token sirve una sola vez
            DB::table('password_reset_tokens')->where('email', $usuario->cor_usu)->delete();
        });

        Bitacora::registrar('restablecer_password', 'usuarios', $usuario->cod_usuario);

        return redirect()->route('login')->with('exito',
            'Tu contraseña se cambió correctamente. Ya puedes iniciar sesión con ella.');
    }

    // ------------------------------------------------------------------ apoyo

    /**
     * ¿Hay un token vigente para ese correo que coincida con el recibido?
     *
     * La caducidad se resuelve en la consulta, con el reloj de PostgreSQL.
     * Una fila sin fecha no pasa el filtro: un dato roto no debería abrir
     * la puerta.
     *
     * La coincidencia del token sí se compara en PHP, porque en la tabla
     * está el hash y no el token: no se puede buscar por token.
     */
    private function filaValida(string $correo, string $token): bool
    {
        if ($correo === '' || $token === '') {
            return false;
        }

        $fila = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($correo))])
            ->whereNotNull('created_at')
            ->whereRaw("created_at > CURRENT_TIMESTAMP - INTERVAL '" . self::VIGENCIA . " minutes'")
            ->first();

        return $fila !== null && Hash::check($token, $fila->token);
    }
}
