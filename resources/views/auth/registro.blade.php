@extends('layouts.invitado')

@section('titulo', 'Crear cuenta')
@section('encabezado', 'Crea tu cuenta')
@section('subtitulo', 'Un minuto y empiezas a cocinar con lo que tienes')
@section('ancho', 'max-w-lg')

@section('contenido')
<form method="POST" action="{{ route('registro.guardar') }}" class="space-y-5">
    @csrf

    <x-campo nombre="nom_usu"
             etiqueta="Nombre completo"
             requerido
             autofocus
             autocomplete="name"
             placeholder="Milenka Melody Chuquimia"
             icono="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5z" />

    <x-campo nombre="cor_usu"
             etiqueta="Correo electrónico"
             tipo="email"
             requerido
             autocomplete="username"
             placeholder="tu@correo.com"
             icono="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />

    <div class="grid gap-5 sm:grid-cols-2">
        <x-campo nombre="con_usu"
                 etiqueta="Contraseña"
                 tipo="password"
                 requerido
                 autocomplete="new-password"
                 placeholder="••••••••"
                 ayuda="Mínimo 8 caracteres, con letras y números." />

        <x-campo nombre="con_usu_confirmation"
                 etiqueta="Repetir contraseña"
                 tipo="password"
                 requerido
                 autocomplete="new-password"
                 placeholder="••••••••" />
    </div>

    <fieldset>
        <legend class="etiqueta-campo">
            Preferencias alimentarias
            <span class="font-normal text-crema-500">(opcional)</span>
        </legend>

        <p class="mb-3 text-xs text-crema-500">
            Sirven para ajustar las recetas que te sugerimos. Puedes cambiarlas después.
        </p>

        @php $elegidas = old('preferencias', []); @endphp

        <div class="flex flex-wrap gap-2">
            @foreach ($preferencias as $preferencia)
                <label class="cursor-pointer">
                    <input type="checkbox"
                           name="preferencias[]"
                           value="{{ $preferencia->cod_preferencia }}"
                           @checked(in_array($preferencia->cod_preferencia, $elegidas))
                           class="peer sr-only">
                    <span class="vidrio inline-flex rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition
                                 peer-checked:bg-oro-500 peer-checked:text-masa-900 peer-checked:shadow-none
                                 peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-oro-400">
                        {{ $preferencia->nom_pre }}
                    </span>
                </label>
            @endforeach
        </div>

        @error('preferencias.*')
            <p class="mt-2 text-xs text-error-400">{{ $message }}</p>
        @enderror
    </fieldset>

    <button type="submit" class="boton-oro w-full">
        Crear mi cuenta
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <p class="text-center text-sm text-crema-300">
        ¿Ya tienes una cuenta?
        <a href="{{ route('login') }}" class="font-medium text-oro-400 transition hover:text-oro-300">Inicia sesión</a>
    </p>
</form>
@endsection
