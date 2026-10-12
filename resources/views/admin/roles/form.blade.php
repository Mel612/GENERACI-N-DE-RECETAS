@extends('layouts.app')

@php
    $editando  = $rol->exists;
    $protegido = $editando && in_array($rol->cod_rol, \App\Http\Controllers\Admin\RolController::PROTEGIDOS, true);
@endphp

@section('titulo', $editando ? 'Editar rol' : 'Nuevo rol')

@section('contenido')
<x-cabecera :titulo="$editando ? 'Editar rol' : 'Nuevo rol'" />

<x-formulario :accion="$editando ? route('admin.roles.update', $rol) : route('admin.roles.store')"
              :metodo="$editando ? 'PUT' : 'POST'"
              :volver="route('admin.roles.index')"
              :textoEnviar="$editando ? 'Guardar cambios' : 'Crear rol'">

    @if ($protegido)
        <div class="flex items-start gap-3 rounded-xl border border-alerta-400/25 bg-alerta-400/10 px-4 py-3 text-sm text-alerta-400">
            <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <span>
                Este es un rol del sistema. Puedes cambiar su descripción, pero no su nombre
                ni su estado: el control de acceso de las rutas lo busca por nombre.
            </span>
        </div>
    @endif

    <x-campo nombre="nom_rol" etiqueta="Nombre del rol" :valor="$rol->nom_rol" requerido
             :readonly="$protegido" placeholder="Nutricionista" />

    <x-campo nombre="des_rol" etiqueta="Descripción" :valor="$rol->des_rol"
             placeholder="Revisa y aprueba las recetas y su información nutricional." />

    @unless ($protegido)
        <x-interruptor nombre="act_rol" etiqueta="Rol activo" :valor="$rol->act_rol ?? true" />

        @unless ($editando)
            <div class="flex items-start gap-3 rounded-xl border border-oro-500/20 bg-oro-500/5 px-4 py-3 text-sm text-crema-300">
                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-oro-400" fill="currentColor" aria-hidden="true">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                </svg>
                <span>
                    Un rol nuevo empieza sin permisos. Para que pueda entrar a alguna pantalla,
                    hay que nombrarlo en el middleware de esa ruta, en <code class="rounded bg-crema-100/6 px-1.5 py-0.5 text-xs">routes/web.php</code>.
                </span>
            </div>
        @endunless
    @endunless
</x-formulario>
@endsection
