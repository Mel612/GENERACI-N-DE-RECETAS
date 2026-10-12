@extends('layouts.app')

@section('titulo', 'Mi historial')

@section('contenido')
<x-cabecera rotulo="Mi cocina" titulo="Mis búsquedas anteriores"
            subtitulo="Cada consulta que hiciste, con los ingredientes que usaste y lo que te sugirió el sistema." />

@if ($consultas->isEmpty())
    <div class="vidrio rounded-2xl p-10 text-center">
        <h2 class="titulo-serif text-2xl">Todavía no has generado recetas</h2>
        <p class="mx-auto mt-3 max-w-md text-sm text-crema-300">
            Cuando busques qué cocinar, las consultas quedarán guardadas aquí.
        </p>
        <a href="{{ route('generador.index') }}" class="boton-oro mt-6">Generar recetas</a>
    </div>
@else
    <div class="space-y-4">
        @foreach ($consultas as $consulta)
            <article class="vidrio rounded-2xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-crema-100">
                            {{ $consulta->fec_cre_con?->format('d/m/Y H:i') }}
                        </p>
                        <p class="mt-1 text-xs text-crema-500">
                            {{ trans_choice(':count ingrediente|:count ingredientes', count($consulta->ing_json_con ?? [])) }} &middot;
                            {{ trans_choice(':count resultado|:count resultados', (int) $consulta->tot_res_con) }}
                            @if ($consulta->tie_res_con) &middot; {{ $consulta->tie_res_con }} ms @endif
                        </p>
                    </div>
                </div>

                @if ($consulta->ing_json_con)
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @foreach (array_slice($consulta->ing_json_con, 0, 12) as $ing)
                            <span class="vidrio rounded-full px-2.5 py-1 text-[0.62rem] text-crema-300">
                                {{ $ing['nombre'] ?? '' }}
                            </span>
                        @endforeach
                        @if (count($consulta->ing_json_con) > 12)
                            <span class="px-2 py-1 text-[0.62rem] text-crema-500">
                                y {{ count($consulta->ing_json_con) - 12 }} más
                            </span>
                        @endif
                    </div>
                @endif

                @if ($consulta->resultados->isNotEmpty())
                    <ol class="mt-5 space-y-2 border-t border-oro-500/10 pt-4">
                        @foreach ($consulta->resultados->sortBy('pos_res')->take(5) as $resultado)
                            <li class="flex items-center justify-between gap-3 text-sm">
                                <span class="flex items-center gap-2.5">
                                    <span class="w-4 text-xs tabular-nums text-crema-500">{{ $resultado->pos_res }}</span>
                                    @if ($resultado->receta)
                                        <a href="{{ route('recetas.show', $resultado->cod_receta_res) }}"
                                           class="text-crema-100 transition hover:text-oro-400">
                                            {{ $resultado->receta->nom_rec }}
                                        </a>
                                    @else
                                        <span class="text-crema-500">Receta eliminada</span>
                                    @endif
                                </span>
                                <span class="shrink-0 text-xs tabular-nums text-oro-400">
                                    {{ rtrim(rtrim(number_format((float) $resultado->sco_res, 1), '0'), '.') }}%
                                </span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </article>
        @endforeach
    </div>

    <div class="mt-6">{{ $consultas->links() }}</div>
@endif
@endsection
