@extends('layouts.app')

@php $editando = $usuario->exists; @endphp

@section('titulo', $editando ? 'Editar usuario' : 'Nuevo usuario')

@section('contenido')
<div class="mx-auto max-w-2xl">

    <div class="mb-7">
        <h1 class="titulo-serif text-[clamp(1.9rem,4vw,2.7rem)]">
            {{ $editando ? 'Editar usuario' : 'Registrar usuario' }}
        </h1>
    </div>

    <form method="POST"
          action="{{ $editando ? route('usuarios.update', $usuario) : route('usuarios.store') }}"
          class="vidrio space-y-5 rounded-2xl p-7">
        @csrf
        @if ($editando) @method('PUT') @endif

        <x-campo nombre="nom_usu" etiqueta="Nombre completo" :valor="$usuario->nom_usu" requerido />
        <x-campo nombre="cor_usu" etiqueta="Correo electrónico" tipo="email" :valor="$usuario->cor_usu" requerido />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-campo nombre="con_usu"
                     :etiqueta="$editando ? 'Nueva contraseña' : 'Contraseña'"
                     tipo="password"
                     :requerido="! $editando"
                     autocomplete="new-password"
                     :ayuda="$editando ? 'Déjala vacía para no cambiarla.' : 'Mínimo 8 caracteres, con letras y números.'" />

            <x-campo nombre="con_usu_confirmation"
                     etiqueta="Repetir contraseña"
                     tipo="password"
                     :requerido="! $editando"
                     autocomplete="new-password" />
        </div>

        <div>
            <label for="cod_rol_usu" class="etiqueta-campo">
                Rol <span class="text-oro-400" aria-hidden="true">*</span>
            </label>
            <select id="cod_rol_usu" name="cod_rol_usu" required
                    @class(['campo', 'campo-error' => $errors->has('cod_rol_usu')])>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->cod_rol }}" @selected(old('cod_rol_usu', $usuario->cod_rol_usu) == $rol->cod_rol)>
                        {{ $rol->nom_rol }}
                    </option>
                @endforeach
            </select>
            @error('cod_rol_usu')
                <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-crema-300">
            <input type="checkbox" name="act_usu" value="1"
                   @checked(old('act_usu', $usuario->act_usu ?? true))
                   class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
            Cuenta activa
        </label>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="boton-oro">
                {{ $editando ? 'Guardar cambios' : 'Registrar usuario' }}
            </button>
            <a href="{{ route('usuarios.index') }}" class="boton-vidrio vidrio">Cancelar</a>
        </div>
    </form>
</div>
@endsection
