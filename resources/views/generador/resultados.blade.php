@extends('layouts.app')

@section('titulo', 'Recetas sugeridas')

@section('contenido')
<x-cabecera rotulo="Mi cocina" titulo="Esto puedes cocinar"
            subtitulo="Ordenadas por cuánto cubres de cada receta, cuánto aprovechas de tu despensa y qué está por caducar." />

<div class="mb-6 flex flex-wrap items-center gap-3">
    <a href="{{ route('generador.index') }}" class="boton-vidrio vidrio text-sm">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Cambiar ingredientes o filtros
    </a>

    <p class="text-xs text-crema-500">
        {{ trans_choice(':count receta|:count recetas', $resultados->count()) }}
        &middot; {{ trans_choice(':count ingrediente usado|:count ingredientes usados', $despensa->count()) }}
        &middot; generado en {{ $milisegundos }} ms
    </p>
</div>

{{-- Qué preferencias se aplicaron a ESTE resultado, y cuáles no --}}
@if ($dieta['tiene'] ?? false)
    <div class="mb-6">
        <x-aviso-dieta :dieta="$dieta" :aplicada="$dietaAplicada" />
    </div>
@endif

@if ($resultados->isEmpty())
    <div class="vidrio rounded-2xl p-10 text-center">
        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10 text-alerta-400" fill="currentColor" aria-hidden="true">
            <path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7 14 5 12 5 9.5S7 5 9.5 5 14 7 14 9.5 12 14 9.5 14z"/>
        </svg>

        <h2 class="titulo-serif mt-4 text-2xl">No encontramos ninguna receta</h2>

        <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-crema-300">
            Ninguna receta del recetario usa los ingredientes que seleccionaste, o los filtros
            son muy estrictos. Prueba a quitar filtros, o agrega más ingredientes a tu despensa.
        </p>

        {{-- Si la dieta filtró, es la primera sospechosa: decirlo ahorra el intento a ciegas --}}
        @if ($dietaAplicada)
            <p class="mx-auto mt-3 max-w-lg rounded-lg bg-alerta-400/10 px-4 py-3 text-sm leading-relaxed text-alerta-400">
                La búsqueda solo consideró recetas
                {{ $dieta['aplicables']->pluck('preferencia')->implode(' y ') }}.
                Si quieres ver el resto, desmarca «Respetarlas» al volver a buscar.
            </p>
        @endif

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('generador.index') }}" class="boton-oro">Cambiar la búsqueda</a>
            <a href="{{ route('inventario.index') }}" class="boton-vidrio vidrio">Ir a mi despensa</a>
        </div>
    </div>

    {{-- Aquí es donde la IA tiene sentido: el recetario no tenía nada que dar --}}
    @if ($asistente && $restantesIa > 0)
        <div class="vidrio mt-5 rounded-2xl p-8 text-center">
            <h3 class="titulo-serif text-xl">¿Quieres que el asistente invente una?</h3>

            <p class="mx-auto mt-2 max-w-lg text-sm leading-relaxed text-crema-300">
                Puede proponer una receta con los ingredientes que tienes. La escribe
                una inteligencia artificial, así que hay que leerla con criterio: no
                se publica hasta que el nutricionista la revise.
            </p>

            <form method="POST" action="{{ route('asistente.proponer') }}" class="mt-5">
                @csrf
                <button type="submit" class="boton-oro">Proponerme una receta</button>
            </form>

            <p class="mt-3 text-xs text-crema-500">{{ trans_choice('Te queda 1 propuesta|Te quedan :count propuestas', $restantesIa) }} hoy.</p>
        </div>
    @endif
@else
    @php
        $completas = $resultados->where('compatibilidad', 100);
        $parciales = $resultados->where('compatibilidad', '<', 100);
    @endphp

    @if ($completas->isNotEmpty())
        <section class="mb-10">
            <h2 class="titulo-serif mb-1 text-2xl">
                Puedes cocinarlas ahora mismo
                <span class="text-oro-400">({{ $completas->count() }})</span>
            </h2>
            <p class="mb-5 text-sm text-crema-300">Tienes todos los ingredientes necesarios.</p>

            <div class="grid gap-5 lg:grid-cols-2">
                @foreach ($completas as $receta)
                    <x-tarjeta-receta :receta="$receta" :niveles="$niveles" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($parciales->isNotEmpty())
        <section>
            <h2 class="titulo-serif mb-1 text-2xl">
                Te falta poco
                <span class="text-crema-300">({{ $parciales->count() }})</span>
            </h2>
            <p class="mb-5 text-sm text-crema-300">Cada tarjeta indica qué tendrías que comprar.</p>

            <div class="grid gap-5 lg:grid-cols-2">
                @foreach ($parciales as $receta)
                    <x-tarjeta-receta :receta="$receta" :niveles="$niveles" />
                @endforeach
            </div>
        </section>
    @endif

    <div class="vidrio mt-8 rounded-2xl p-6">
        <h2 class="mb-2 text-sm font-medium text-crema-100">Cómo se ordenaron</h2>
        <p class="text-xs leading-relaxed text-crema-300">
            Cada receta recibe un puntaje que pondera tres cosas: cuánto de la receta puedes
            cubrir con lo que tienes (70&nbsp;%), cuánto de tu despensa aprovecha (20&nbsp;%)
            y si usa ingredientes por caducar (10&nbsp;%). Por eso una receta grande que usa
            media despensa puede aparecer antes que una pequeña que tienes completa.
        </p>
    </div>
@endif
@endsection
