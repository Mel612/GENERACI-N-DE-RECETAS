@extends('layouts.app')

@section('titulo', 'Mi perfil')

@section('contenido')
<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <h1 class="titulo-serif text-[clamp(1.9rem,4vw,2.7rem)]">Mi perfil</h1>
    </div>

    <form method="POST" action="{{ route('perfil.update') }}" class="vidrio space-y-5 rounded-2xl p-7">
        @csrf
        @method('PUT')

        <h2 class="titulo-serif text-xl">Datos personales</h2>

        <x-campo nombre="nom_usu" etiqueta="Nombre completo" :valor="$usuario->nom_usu" requerido />
        <x-campo nombre="cor_usu" etiqueta="Correo electrónico" tipo="email" :valor="$usuario->cor_usu" requerido />

        <fieldset>
            <legend class="etiqueta-campo">Preferencias alimentarias</legend>
            @php $elegidas = old('preferencias', $usuario->preferencias->pluck('cod_preferencia')->all()); @endphp

            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($preferencias as $preferencia)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="preferencias[]" value="{{ $preferencia->cod_preferencia }}"
                               @checked(in_array($preferencia->cod_preferencia, $elegidas))
                               class="peer sr-only">
                        <span class="vidrio inline-flex rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition
                                     peer-checked:bg-oro-500 peer-checked:text-masa-900
                                     peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-oro-400">
                            {{ $preferencia->nom_pre }}
                        </span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <button type="submit" class="boton-oro">Guardar cambios</button>
    </form>

    <form method="POST" action="{{ route('perfil.password') }}" class="vidrio space-y-5 rounded-2xl p-7">
        @csrf
        @method('PUT')

        <h2 class="titulo-serif text-xl">Cambiar contraseña</h2>

        <x-campo nombre="con_actual" etiqueta="Contraseña actual" tipo="password" requerido autocomplete="current-password" />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-campo nombre="con_usu" etiqueta="Nueva contraseña" tipo="password" requerido autocomplete="new-password"
                     ayuda="Mínimo 8 caracteres, con letras y números." />
            <x-campo nombre="con_usu_confirmation" etiqueta="Repetir nueva contraseña" tipo="password" requerido autocomplete="new-password" />
        </div>

        <button type="submit" class="boton-vidrio vidrio">Actualizar contraseña</button>
    </form>
</div>
@endsection
