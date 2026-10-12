@extends('layouts.app')

@section('titulo', 'Generar recetas')

@section('contenido')
<x-cabecera rotulo="Mi cocina" titulo="¿Qué cocino hoy?"
            subtitulo="El sistema compara tu despensa con el recetario y te muestra qué puedes preparar." />

@if ($despensa->isEmpty())
    <div class="vidrio rounded-2xl p-10 text-center">
        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10 text-oro-500/60" fill="currentColor" aria-hidden="true">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <h2 class="titulo-serif mt-4 text-2xl">Primero necesitas una despensa</h2>
        <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-crema-300">
            Registra lo que tienes en casa y el sistema podrá decirte qué cocinar con ello.
        </p>
        <a href="{{ route('inventario.index') }}" class="boton-oro mt-6">Ir a mi despensa</a>
    </div>
@else
    <form method="POST" action="{{ route('generador.generar') }}" class="space-y-6">
        @csrf

        {{-- ═══ Qué usar ═══ --}}
        <section class="vidrio rounded-2xl p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="titulo-serif text-xl">Con qué cocinar</h2>
                    <p class="mt-1 text-xs text-crema-500">
                        Por defecto se usa toda tu despensa. Desmarca lo que no quieras usar hoy.
                    </p>
                </div>

                <div class="flex gap-2 text-xs">
                    <button type="button" data-marcar="todos"
                            class="rounded-full px-3 py-1.5 text-crema-300 transition hover:bg-oro-500/12">
                        Marcar todo
                    </button>
                    <button type="button" data-marcar="ninguno"
                            class="rounded-full px-3 py-1.5 text-crema-300 transition hover:bg-oro-500/12">
                        Desmarcar todo
                    </button>
                </div>
            </div>

            @php $porGrupo = $despensa->groupBy('nom_cat'); @endphp

            <div class="space-y-5" id="lista-despensa">
                @foreach ($porGrupo as $categoria => $grupo)
                    <div>
                        <p class="rotulo mb-2">{{ $categoria }}</p>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($grupo as $item)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="ingredientes[]"
                                           value="{{ $item->cod_ingrediente_inv }}" checked
                                           class="peer sr-only">
                                    <span @class([
                                        'vidrio inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs transition',
                                        'text-crema-300 peer-checked:bg-oro-500 peer-checked:text-masa-900',
                                        'peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-oro-400',
                                    ])>
                                        {{ $item->nom_ing }}
                                        <span class="opacity-70">
                                            {{ rtrim(rtrim(number_format((float) $item->can_inv, 2, '.', ''), '0'), '.') }}
                                            {{ $item->abr_uni }}
                                        </span>
                                        @if ($item->urgente)
                                            <span class="h-1.5 w-1.5 rounded-full bg-alerta-400"
                                                  title="Por caducar" aria-hidden="true"></span>
                                            <span class="sr-only">(por caducar)</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @php $urgentes = $despensa->where('urgente', true)->count(); @endphp
            @if ($urgentes > 0)
                <p class="mt-5 rounded-xl border border-alerta-400/20 bg-alerta-400/5 px-4 py-3 text-xs text-alerta-400">
                    {{ trans_choice('Tienes 1 ingrediente por caducar|Tienes :count ingredientes por caducar', $urgentes) }}.
                    {{ $urgentes === 1 ? 'Las recetas que lo usen' : 'Las recetas que los usen' }} aparecerán primero.
                </p>
            @endif
        </section>

        {{-- ═══ Preferencias alimentarias ═══ --}}
        <x-aviso-dieta :dieta="$dieta" editable />

        {{-- ═══ Filtros ═══ --}}
        <section class="vidrio rounded-2xl p-6">
            <h2 class="titulo-serif mb-4 text-xl">Qué tipo de receta buscas</h2>
            <x-filtros-receta :categorias="$categorias" :etiquetas="$etiquetas" :niveles="$niveles" />
        </section>

        <button type="submit" class="boton-oro w-full sm:w-auto">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                <path d="M12 3l1.9 5.9H20l-4.9 3.6 1.9 5.9L12 14.8 7 18.4l1.9-5.9L4 8.9h6.1L12 3z"/>
            </svg>
            Generar recetas
        </button>
    </form>
@endif
@endsection
