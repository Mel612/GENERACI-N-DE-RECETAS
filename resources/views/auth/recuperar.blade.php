@extends('layouts.invitado')

@section('titulo', 'Recuperar contraseña')
@section('encabezado', '¿Olvidaste tu contraseña?')
@section('subtitulo', 'Te enviamos un enlace para que elijas una nueva')

@section('contenido')

{{--
    Atajo de desarrollo. Solo aparece con APP_ENV=local y
    MOSTRAR_ENLACE_RECUPERACION=true en el .env, para poder probar el flujo
    sin tener un servidor de correo montado en Laragon.
--}}
@if (session('enlace_desarrollo'))
    <div class="mb-6 rounded-xl border border-alerta-400/30 bg-alerta-400/10 p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-alerta-400">Solo en desarrollo</p>
        <p class="mt-1.5 text-xs leading-relaxed text-crema-300">
            Este es el enlace que se envió por correo. No aparece cuando el sistema
            está en producción.
        </p>
        <a href="{{ session('enlace_desarrollo') }}"
           class="mt-3 block break-all rounded-lg bg-crema-100/8 px-3 py-2 text-xs text-oro-400 hover:underline">
            {{ session('enlace_desarrollo') }}
        </a>
    </div>
@endif

<form method="POST" action="{{ route('password.enviar') }}" class="space-y-5">
    @csrf

    <p class="text-sm leading-relaxed text-crema-300">
        Escribe el correo con el que te registraste. Si tiene una cuenta activa,
        recibirás un enlace para elegir una contraseña nueva.
    </p>

    <x-campo nombre="cor_usu"
             etiqueta="Correo electrónico"
             tipo="email"
             requerido
             autofocus
             autocomplete="username"
             placeholder="tu@correo.com"
             icono="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />

    <button type="submit" class="boton-oro w-full">
        Enviarme el enlace
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div class="flex items-center gap-3 pt-1">
        <span class="h-px flex-1 bg-oro-500/15"></span>
        <span class="text-xs text-crema-500">o</span>
        <span class="h-px flex-1 bg-oro-500/15"></span>
    </div>

    <a href="{{ route('login') }}" class="boton-vidrio vidrio w-full">Volver a iniciar sesión</a>
</form>
@endsection

@section('pie')
    El enlace vence en una hora y solo se puede usar una vez.
@endsection
