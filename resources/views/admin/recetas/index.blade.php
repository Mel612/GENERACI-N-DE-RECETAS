@extends('layouts.app')

@section('titulo', 'Recetas')

@section('contenido')
<x-cabecera rotulo="Recetas" titulo="Gestión de recetas"
            subtitulo="{{ $recetas->total() }} receta(s) activa(s)."
            :accion="route('admin.recetas.create')"
            textoAccion="Nueva receta" />

<form method="GET" class="vidrio mb-6 grid gap-3 rounded-2xl p-4 sm:grid-cols-2 lg:grid-cols-5">
    <div class="lg:col-span-2">
        <label class="sr-only" for="buscar">Buscar</label>
        <input id="buscar" type="search" name="buscar" value="{{ request('buscar') }}"
               placeholder="Buscar por nombre o descripción" class="campo">
    </div>

    <div>
        <label class="sr-only" for="categoria">Categoría</label>
        <select id="categoria" name="categoria" class="campo">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->cod_categoria_receta }}" @selected(request('categoria') == $categoria->cod_categoria_receta)>
                    {{ $categoria->nom_car }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="sr-only" for="estado">Estado</label>
        <select id="estado" name="estado" class="campo">
            <option value="">Todos los estados</option>
            @foreach ($estados as $clave => $texto)
                <option value="{{ $clave }}" @selected(request('estado') === $clave)>{{ $texto }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="sr-only" for="origen">Origen</label>
        <select id="origen" name="origen" class="campo">
            <option value="">Todos los orígenes</option>
            @foreach ($origenes as $clave => $texto)
                <option value="{{ $clave }}" @selected(request('origen') === $clave)>{{ $texto }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-wrap items-center gap-3 sm:col-span-2 lg:col-span-5">
        <label class="flex items-center gap-2 text-sm text-crema-300">
            <input type="checkbox" name="incompletas" value="1" @checked(request()->boolean('incompletas'))
                   class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
            Solo incompletas (sin ingredientes o sin pasos)
        </label>

        <button type="submit" class="boton-vidrio vidrio">Filtrar</button>

        @if (collect(request()->query())->filter()->isNotEmpty())
            <a href="{{ route('admin.recetas.index') }}" class="boton-vidrio">Limpiar</a>
        @endif
    </div>
</form>

<x-tabla :cabeceras="['Receta', 'Categoría', 'Tiempo', 'Contenido', 'Estado', '>Acciones']"
         descripcion="Recetas registradas en el sistema"
         :hay="$recetas->isNotEmpty()"
         vacio="No se encontraron recetas con esos criterios.">

    @foreach ($recetas as $receta)
        @php
            $incompleta = $receta->ingredientes_count === 0 || $receta->pasos_count === 0;
            $tonos = ['pendiente' => 'text-crema-300', 'aprobada' => 'text-exito-400',
                      'observada' => 'text-alerta-400', 'rechazada' => 'text-error-400'];
        @endphp

        <tr class="border-b border-oro-500/8 align-top transition last:border-0 hover:bg-oro-500/5">
            <td class="px-5 py-4">
                <div class="flex items-start gap-3">
                    <a href="{{ route('admin.recetas.show', $receta) }}" class="shrink-0">
                        <x-foto :url="$receta->urlImagen()" class="h-11 w-11 rounded-lg" />
                    </a>

                    <div class="min-w-0">
                        <a href="{{ route('admin.recetas.show', $receta) }}"
                           class="font-medium text-crema-100 transition hover:text-oro-400">{{ $receta->nom_rec }}</a>

                        <p class="mt-0.5 text-xs text-crema-500">
                            {{ $origenes[$receta->ori_rec] ?? $receta->ori_rec }}
                            @if ($receta->ver_rec)
                                &middot; <span class="sello-verificada"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada</span>
                            @endif
                        </p>
                    </div>
                </div>
            </td>

            <td class="px-5 py-4 text-crema-300">{{ $receta->categoria->nom_car }}</td>

            <td class="whitespace-nowrap px-5 py-4 text-xs text-crema-300">
                {{ $receta->tie_tot_rec }} min<br>
                <span class="text-crema-500">{{ $receta->por_rec }} porción(es)</span>
            </td>

            <td class="px-5 py-4 text-xs">
                <span class="{{ $receta->ingredientes_count ? 'text-crema-300' : 'text-error-400' }}">
                    {{ $receta->ingredientes_count }} ingrediente(s)
                </span><br>
                <span class="{{ $receta->pasos_count ? 'text-crema-300' : 'text-error-400' }}">
                    {{ $receta->pasos_count }} paso(s)
                </span>
            </td>

            <td class="px-5 py-4">
                <span class="text-xs font-medium {{ $tonos[$receta->est_val_rec] ?? 'text-crema-300' }}">
                    {{ $estados[$receta->est_val_rec] ?? $receta->est_val_rec }}
                </span>
                @if ($incompleta)
                    <p class="mt-1 text-[0.62rem] text-error-400">Incompleta</p>
                @endif
            </td>

            <td class="px-5 py-4">
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.recetas.show', $receta) }}"
                       class="rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition hover:bg-oro-500/12 hover:text-crema-100">
                        Ver
                    </a>
                    <a href="{{ route('admin.recetas.edit', $receta) }}"
                       class="rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition hover:bg-oro-500/12 hover:text-crema-100">
                        Editar
                    </a>
                    <form method="POST" action="{{ route('admin.recetas.destroy', $receta) }}"
                          data-confirmar="Vas a dar de baja la receta «{{ $receta->nom_rec }}». Dejará de aparecer en el recetario, pero se conserva en el historial de los usuarios."
                          data-confirmar-boton="Sí, dar de baja">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-full px-3.5 py-1.5 text-xs text-error-400 transition hover:bg-error-400/12">
                            Dar de baja
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $recetas->links() }}</div>
@endsection
