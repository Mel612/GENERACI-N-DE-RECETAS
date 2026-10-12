@extends('layouts.invitado')

@section('titulo', 'Nueva contraseña')
@section('encabezado', 'Elige tu nueva contraseña')
@section('subtitulo', 'Para la cuenta ' . $correo)

@section('contenido')
<form method="POST" action="{{ route('password.guardar') }}" class="space-y-5">
    @csrf

    {{-- El token y el correo viajan con el formulario: identifican la solicitud --}}
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="cor_usu" value="{{ $correo }}">

    <x-campo nombre="con_usu"
             etiqueta="Nueva contraseña"
             tipo="password"
             requerido
             autofocus
             autocomplete="new-password"
             placeholder="••••••••"
             ayuda="Al menos 8 caracteres, con letras y números."
             icono="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zm-6 9a2 2 0 110-4 2 2 0 010 4zM15 8H9V6a3 3 0 016 0v2z" />

    <x-campo nombre="con_usu_confirmation"
             etiqueta="Repite la nueva contraseña"
             tipo="password"
             requerido
             autocomplete="new-password"
             placeholder="••••••••"
             icono="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zm-6 9a2 2 0 110-4 2 2 0 010 4zM15 8H9V6a3 3 0 016 0v2z" />

    <button type="submit" class="boton-oro w-full">
        Guardar la nueva contraseña
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</form>
@endsection

@section('pie')
    Después de guardarla, este enlace deja de funcionar.
@endsection
