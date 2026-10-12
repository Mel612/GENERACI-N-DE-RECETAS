@extends('layouts.app')

@section('titulo', 'Propuestas del asistente')

@section('contenido')

<a href="{{ route('generador.index') }}"
   class="mb-6 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver al generador
</a>

<div class="mb-7">
    <h1 class="titulo-serif text-[clamp(1.9rem,4vw,2.9rem)]">
        {{ count($lote) }} {{ count($lote) === 1 ? 'receta' : 'recetas' }} con lo que tienes
    </h1>
    <p class="mt-3 text-sm text-crema-500">
        Guarda la que quieras, o varias. Las que no guardes se pierden al descartar o pedir otras.
    </p>
</div>

{{--
    El aviso va arriba, una sola vez, y no se puede pasar por alto. Quien
    guarde algo de aquí tiene que saber que lo escribió una máquina y que
    nadie lo revisó todavía.
--}}
<div class="mb-7 flex items-start gap-3 rounded-xl border border-alerta-400/30 bg-alerta-400/10 px-5 py-4 text-sm text-alerta-400">
    <svg viewBox="0 0 24 24" class="mt-0.5 h-5 w-5 shrink-0" fill="currentColor" aria-hidden="true">
        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
    </svg>
    <div class="leading-relaxed">
        <p class="font-medium">Estas recetas las escribió una inteligencia artificial.</p>
        <p class="mt-1 text-crema-300">
            Puede equivocarse en cantidades, tiempos o en el orden de los pasos.
            Lo que guardes entra a la cola del nutricionista y solo llevará el
            distintivo de verificada cuando una persona lo apruebe. Léelas con
            criterio antes de cocinarlas.
        </p>
    </div>
</div>

<div class="grid gap-5 lg:grid-cols-2">
    @foreach ($lote as $indice => $propuesta)
        @php
            $total     = $propuesta['tiempo_preparacion'] + $propuesta['tiempo_coccion'];
            $guardada  = $guardadas[$indice] ?? null;
        @endphp

        {{-- Escrita en lápiz hasta que se guarda: borde punteado y sello.
             Guardada, pasa a tinta y entra a la cola del nutricionista. --}}
        <article class="vidrio flex flex-col rounded-2xl p-6 {{ $guardada ? 'opacity-70' : 'en-lapiz' }}">
            <p class="mb-3">
                @if ($guardada)
                    <span class="sello text-oro-500"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>Guardada</span>
                @else
                    <span class="sello sello-lapiz"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zM20.7 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>En lápiz</span>
                @endif
            </p>
            <h2 class="titulo-serif text-2xl">{{ $propuesta['nombre'] }}</h2>

            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-crema-500">
                <span>{{ ($categorias[$propuesta['categoria']] ?? null)?->nom_car }}</span>
                <span>&middot;</span>
                <span>{{ $total }} min</span>
                <span>&middot;</span>
                <span>{{ $propuesta['porciones'] }} porciones</span>
                <span>&middot;</span>
                <span>{{ $niveles[$propuesta['nivel']] ?? $propuesta['nivel'] }}</span>
            </div>

            @if ($propuesta['descripcion'])
                <p class="mt-3 text-sm leading-relaxed text-crema-300">{{ $propuesta['descripcion'] }}</p>
            @endif

            {{-- Ingredientes, señalando cuáles ya tienes --}}
            <h3 class="titulo-serif mb-1 mt-5 text-lg">Ingredientes</h3>
            <p class="mb-3 text-xs text-crema-500">
                {{ $propuesta['usa_de_despensa'] }} de {{ count($propuesta['ingredientes']) }} salen de tu despensa.
            </p>

            <ul class="space-y-2.5">
                @foreach ($propuesta['ingredientes'] as $fila)
                    @php
                        $ing = $ingredientes[$fila['ingrediente']] ?? null;
                        $uni = $unidades[$fila['unidad']] ?? null;
                        $tengo = $despensa->has($fila['ingrediente']);
                    @endphp

                    <li class="flex items-start justify-between gap-3 border-b border-oro-500/8 pb-2.5 last:border-0">
                        <div class="min-w-0">
                            <span class="text-sm text-crema-100">{{ $ing?->nom_ing ?? 'Ingrediente ' . $fila['ingrediente'] }}</span>

                            @if ($tengo)
                                <span class="ml-1.5 rounded-full bg-exito-400/12 px-2 py-0.5 text-[0.62rem] text-exito-400">Ya lo tienes</span>
                            @else
                                <span class="ml-1.5 rounded-full bg-crema-100/6 px-2 py-0.5 text-[0.62rem] text-crema-500">Te falta</span>
                            @endif

                            @if ($fila['opcional'])
                                <span class="ml-1.5 text-[0.62rem] text-crema-500">(opcional)</span>
                            @endif

                            @if ($ing?->ale_ing)
                                <span class="ml-1.5 rounded-full bg-alerta-400/12 px-2 py-0.5 text-[0.62rem] text-alerta-400">Alérgeno</span>
                            @endif

                            @if ($fila['nota'])
                                <p class="mt-0.5 text-xs text-crema-500">{{ $fila['nota'] }}</p>
                            @endif
                        </div>

                        <span class="shrink-0 whitespace-nowrap text-sm tabular-nums text-oro-400">
                            {{ rtrim(rtrim(number_format((float) $fila['cantidad'], 3, '.', ''), '0'), '.') }}
                            {{ $uni?->abr_uni }}
                        </span>
                    </li>
                @endforeach
            </ul>

            {{-- Pasos: cerrados, para que la página no sea kilométrica --}}
            <details class="mt-5">
                <summary class="cursor-pointer text-sm text-crema-300 transition hover:text-oro-400">
                    Preparación ({{ count($propuesta['pasos']) }} pasos)
                </summary>

                <ol class="mt-4 space-y-5">
                    @foreach ($propuesta['pasos'] as $i => $paso)
                        <li class="flex gap-4">
                            <span class="titulo-serif w-8 shrink-0 text-2xl text-oro-500/70">{{ $i + 1 }}</span>

                            <div class="flex-1">
                                <p class="text-sm leading-relaxed text-crema-100">{{ $paso['instruccion'] }}</p>

                                <div class="mt-1.5 flex flex-wrap gap-3 text-xs text-crema-500">
                                    @if ($paso['tiempo'])
                                        <span>{{ $paso['tiempo'] }} min</span>
                                    @endif
                                    @if ($paso['critico'])
                                        <span class="text-alerta-400">Paso crítico</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </details>

            <div class="mt-auto pt-6">
                @if ($guardada)
                    <a href="{{ route('recetas.show', $guardada) }}" class="boton-vidrio vidrio">
                        Ver la receta guardada
                    </a>
                @else
                    <form method="POST" action="{{ route('asistente.guardar') }}">
                        @csrf
                        <input type="hidden" name="indice" value="{{ $indice }}">
                        <button type="submit" class="boton-oro">Guardar esta receta</button>
                    </form>
                @endif
            </div>
        </article>
    @endforeach
</div>

<div class="mt-7 flex flex-wrap items-center gap-3">
    <form method="POST" action="{{ route('asistente.proponer') }}">
        @csrf
        <button type="submit" class="boton-vidrio vidrio" @disabled($restantes <= 0)>
            Proponer otras
        </button>
    </form>

    <form method="POST" action="{{ route('asistente.descartar') }}">
        @csrf
        <button type="submit" class="boton-vidrio">Descartar todas</button>
    </form>

    <p class="text-xs text-crema-500">
        Te quedan {{ $restantes }} propuesta(s) hoy.
    </p>
</div>
@endsection
