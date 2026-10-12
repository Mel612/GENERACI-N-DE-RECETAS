@extends('layouts.app')

@section('titulo', 'Usuarios')

@section('contenido')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="titulo-serif text-[clamp(1.9rem,4vw,2.7rem)]">Gestión de usuarios</h1>
        <p class="mt-2 text-sm text-crema-300">{{ $usuarios->total() }} usuario(s) registrado(s).</p>
    </div>

    <a href="{{ route('usuarios.create') }}" class="boton-oro">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
        </svg>
        Nuevo usuario
    </a>
</div>

<form method="GET" class="vidrio mb-6 flex flex-wrap gap-3 rounded-2xl p-4">
    <label class="sr-only" for="buscar">Buscar usuario</label>
    <input id="buscar" type="search" name="buscar" value="{{ request('buscar') }}"
           placeholder="Buscar por nombre o correo" class="campo max-w-xs">

    <label class="sr-only" for="rol">Filtrar por rol</label>
    <select id="rol" name="rol" class="campo max-w-[12rem]">
        <option value="">Todos los roles</option>
        @foreach ($roles as $rol)
            <option value="{{ $rol->cod_rol }}" @selected(request('rol') == $rol->cod_rol)>{{ $rol->nom_rol }}</option>
        @endforeach
    </select>

    <button type="submit" class="boton-vidrio vidrio">Filtrar</button>

    @if (request()->hasAny(['buscar', 'rol']))
        <a href="{{ route('usuarios.index') }}" class="boton-vidrio">Limpiar</a>
    @endif
</form>

<div class="vidrio overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <caption class="sr-only">Listado de usuarios registrados en el sistema</caption>
            <thead>
                <tr class="border-b border-oro-500/12 text-left text-xs uppercase tracking-wide text-crema-500">
                    <th scope="col" class="px-5 py-3.5 font-medium">Nombre</th>
                    <th scope="col" class="px-5 py-3.5 font-medium">Correo</th>
                    <th scope="col" class="px-5 py-3.5 font-medium">Rol</th>
                    <th scope="col" class="px-5 py-3.5 font-medium">Estado</th>
                    <th scope="col" class="px-5 py-3.5 text-right font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
                        <td class="px-5 py-4 font-medium text-crema-100">{{ $usuario->nom_usu }}</td>
                        <td class="px-5 py-4 text-crema-300">{{ $usuario->cor_usu }}</td>
                        <td class="px-5 py-4">
                            <span class="vidrio rounded-full px-3 py-1 text-xs text-crema-300">{{ $usuario->rol->nom_rol }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($usuario->act_usu)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-exito-400/12 px-3 py-1 text-xs text-exito-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-exito-400"></span> Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-crema-100/6 px-3 py-1 text-xs text-crema-500">
                                    <span class="h-1.5 w-1.5 rounded-full bg-crema-500"></span> Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                   class="rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition hover:bg-oro-500/12 hover:text-crema-100">
                                    Editar
                                </a>

                                @if ($usuario->cod_usuario !== auth()->id() && $usuario->act_usu)
                                    <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                                          data-confirmar="Vas a dar de baja a {{ $usuario->nom_usu }}. Ya no podrá iniciar sesión."
                                          data-confirmar-boton="Sí, dar de baja">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-full px-3.5 py-1.5 text-xs text-error-400 transition hover:bg-error-400/12">
                                            Dar de baja
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center text-crema-500">
                            No se encontraron usuarios con esos criterios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $usuarios->links() }}</div>
@endsection
