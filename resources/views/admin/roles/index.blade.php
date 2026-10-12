@extends('layouts.app')

@section('titulo', 'Roles y permisos')

@section('contenido')
<x-cabecera titulo="Roles y permisos"
            subtitulo="Qué puede hacer cada tipo de usuario dentro del sistema."
            :accion="route('admin.roles.create')"
            textoAccion="Nuevo rol" />

{{-- Los roles, como fichas --}}
<div class="mb-8 grid gap-4 md:grid-cols-3">
    @foreach ($roles as $rol)
        @php $protegido = in_array($rol->cod_rol, \App\Http\Controllers\Admin\RolController::PROTEGIDOS, true); @endphp

        <div class="vidrio flex flex-col rounded-2xl p-6">
            <div class="flex items-start justify-between gap-3">
                <h2 class="titulo-serif text-2xl">{{ $rol->nom_rol }}</h2>
                @if ($protegido)
                    <span class="vidrio shrink-0 rounded-full px-2.5 py-1 text-[0.62rem] text-crema-300"
                          title="Los roles del sistema no se pueden renombrar ni eliminar">Del sistema</span>
                @endif
            </div>

            <p class="mt-2 flex-1 text-sm leading-relaxed text-crema-300">{{ $rol->des_rol }}</p>

            <div class="mt-5 flex items-end justify-between border-t border-oro-500/10 pt-4">
                <div>
                    <p class="titulo-serif text-3xl text-oro-400">{{ $rol->usuarios_count }}</p>
                    <p class="text-xs text-crema-500">usuario(s)</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.roles.edit', $rol) }}"
                       class="rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition hover:bg-oro-500/12 hover:text-crema-100">
                        Editar
                    </a>

                    @unless ($protegido)
                        <form method="POST" action="{{ route('admin.roles.destroy', $rol) }}"
                              data-confirmar="Vas a eliminar el rol «{{ $rol->nom_rol }}».">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="rounded-full px-3.5 py-1.5 text-xs text-error-400 transition hover:bg-error-400/12">
                                Eliminar
                            </button>
                        </form>
                    @endunless
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Matriz de permisos --}}
<div class="mb-4">
    <h2 class="titulo-serif text-2xl">Matriz de permisos</h2>
    <p class="mt-1.5 text-sm text-crema-300">
        Derivada de las restricciones reales de cada ruta. Es informativa: el control
        efectivo lo aplica el middleware <code class="rounded bg-crema-100/6 px-1.5 py-0.5 text-xs">VerificarRol</code>.
    </p>
</div>

<div class="vidrio overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <caption class="sr-only">Permisos de cada rol del sistema</caption>
            <thead>
                <tr class="border-b border-oro-500/12 text-xs uppercase tracking-wide text-crema-500">
                    <th scope="col" class="px-5 py-3.5 text-left font-medium">Acción</th>
                    @foreach ($roles as $rol)
                        <th scope="col" class="px-5 py-3.5 text-center font-medium">{{ $rol->nom_rol }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($permisos as $accion => $rolesPermitidos)
                    <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
                        <th scope="row" class="px-5 py-3.5 text-left font-normal text-crema-100">{{ $accion }}</th>

                        @foreach ($roles as $rol)
                            <td class="px-5 py-3.5 text-center">
                                @if (in_array($rol->nom_rol, $rolesPermitidos, true))
                                    <span class="sr-only">Sí</span>
                                    <svg viewBox="0 0 24 24" class="mx-auto h-4 w-4 text-exito-400" fill="currentColor" aria-hidden="true">
                                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                                    </svg>
                                @else
                                    <span class="sr-only">No</span>
                                    <span class="text-crema-500" aria-hidden="true">&middot;</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
