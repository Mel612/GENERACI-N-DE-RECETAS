@extends('layouts.app')

@section('titulo', 'Mis favoritas')

@section('contenido')
<x-cabecera rotulo="Mi cocina" titulo="Mis recetas favoritas"
            :subtitulo="trans_choice(':count receta guardada|:count recetas guardadas', $recetas->count()) . '.'" />

@if ($recetas->isEmpty())
    <div class="vidrio rounded-2xl p-10 text-center">
        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10 text-oro-500/60" fill="currentColor" aria-hidden="true">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        <h2 class="titulo-serif mt-4 text-2xl">Todavía no guardaste ninguna</h2>
        <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-crema-300">
            Cuando encuentres una receta que te guste, tócale «Guardar en favoritas» y aparecerá aquí.
        </p>
        <a href="{{ route('recetas.index') }}" class="boton-oro mt-6">Explorar el recetario</a>
    </div>
@else
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($recetas as $receta)
            <x-tarjeta-simple :receta="$receta" :niveles="$niveles" :esFavorita="true" />
        @endforeach
    </div>
@endif
@endsection
