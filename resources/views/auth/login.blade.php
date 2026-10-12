@extends('layouts.invitado')

@section('titulo', 'Iniciar sesión')
@section('encabezado', 'Bienvenida de vuelta')
@section('subtitulo', 'Ingresa con tu correo y contraseña')

@section('contenido')
<form method="POST" action="{{ route('login.autenticar') }}" class="space-y-5">
    @csrf

    <x-campo nombre="cor_usu"
             etiqueta="Correo electrónico"
             tipo="email"
             requerido
             autofocus
             autocomplete="username"
             placeholder="tu@correo.com"
             icono="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />

    <x-campo nombre="con_usu"
             etiqueta="Contraseña"
             tipo="password"
             requerido
             autocomplete="current-password"
             placeholder="••••••••"
             icono="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zm-6 9a2 2 0 110-4 2 2 0 010 4zM15 8H9V6a3 3 0 016 0v2z" />

    <div class="flex flex-wrap items-center justify-between gap-3">
        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-crema-300">
            <input type="checkbox" name="recordarme" value="1" @checked(old('recordarme'))
                   class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
            Mantener mi sesión iniciada
        </label>

        <a href="{{ route('password.solicitar') }}" class="text-sm text-oro-400 transition hover:text-oro-300">
            ¿Olvidaste tu contraseña?
        </a>
    </div>

    <button type="submit" class="boton-oro w-full">
        Ingresar
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div class="flex items-center gap-3 pt-1">
        <span class="h-px flex-1 bg-oro-500/15"></span>
        <span class="text-xs text-crema-500">o</span>
        <span class="h-px flex-1 bg-oro-500/15"></span>
    </div>

    <a href="{{ route('registro') }}" class="boton-vidrio vidrio w-full">Crear una cuenta nueva</a>
</form>
@endsection

@section('pie')
    Por seguridad, el acceso se bloquea temporalmente después de 3 intentos fallidos.
@endsection
