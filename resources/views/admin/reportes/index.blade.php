@extends('layouts.app')

@section('titulo', 'Reportes')

@section('contenido')
<x-cabecera titulo="Reportes del sistema"
            subtitulo="Informes sobre usuarios, ingredientes, recetas y consultas." />

<div class="mb-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <x-ficha titulo="Usuarios activos" :valor="$resumen['usuarios']"
             icono="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />

    <x-ficha titulo="Ingredientes en el catálogo" :valor="$resumen['ingredientes']"
             icono="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3z" />

    <x-ficha titulo="Recetas registradas" :valor="$resumen['recetas']"
             icono="M18 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2zM8 6h8v2H8V6zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" />

    <x-ficha titulo="Consultas realizadas" :valor="$resumen['consultas']"
             icono="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5z" />

    <x-ficha titulo="Valoraciones" :valor="$resumen['valoraciones']"
             icono="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />

    <x-ficha titulo="Acciones en la bitácora" :valor="$resumen['acciones']" tono="neutro"
             :enlace="route('admin.bitacora.index')"
             icono="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
</div>

<h2 class="titulo-serif mb-4 text-2xl">Informes detallados</h2>

<div class="grid gap-4 sm:grid-cols-2">
    @foreach ([
        ['admin.reportes.usuarios',     'Usuarios',     'Distribución por rol, altas por mes y preferencias alimentarias más elegidas.'],
        ['admin.reportes.ingredientes', 'Ingredientes', 'Ingredientes por categoría, faltantes de datos nutricionales y los más usados.'],
        ['admin.reportes.recetas',      'Recetas',      'Estado de validación, categorías, origen, mejor valoradas y más guardadas.'],
        ['admin.reportes.consultas',    'Consultas',    'Volumen de búsquedas, eficiencia del generador y recetas más sugeridas.'],
    ] as [$ruta, $titulo, $descripcion])
        <a href="{{ route($ruta) }}" class="vidrio elevar flex items-start gap-4 rounded-2xl p-6 transition">
            <div class="flex-1">
                <h3 class="titulo-serif text-xl">{{ $titulo }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-crema-300">{{ $descripcion }}</p>
            </div>
            <svg viewBox="0 0 24 24" class="mt-1 h-4 w-4 shrink-0 text-oro-400" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    @endforeach
</div>
@endsection
