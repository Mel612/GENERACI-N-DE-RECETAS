@extends('layouts.app')

@section('titulo', 'Reporte de recetas')

@section('contenido')
<x-cabecera rotulo="Reportes" titulo="Recetas"
            subtitulo="Estado de validación, composición del recetario y qué prefieren los usuarios." />

<a href="{{ route('admin.reportes.index') }}"
   class="mb-7 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver a reportes
</a>

<h2 class="mb-4 text-sm font-medium text-crema-100">Estado de validación</h2>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ficha-estado estado="pendiente" :valor="$estados['pendiente']" />
    <x-ficha-estado estado="aprobada"  :valor="$estados['aprobada']" />
    <x-ficha-estado estado="observada" :valor="$estados['observada']" />
    <x-ficha-estado estado="rechazada" :valor="$estados['rechazada']" />
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-3">
    <x-ficha titulo="Recetas activas" :valor="$total" />
    <x-ficha titulo="Marcadas como verificadas" :valor="$verificadas" tono="exito"
             detalle="Solo las aprobadas pueden marcarse" />
    <x-ficha titulo="Incompletas" :valor="$sinIngredientes + $sinPasos"
             :tono="($sinIngredientes + $sinPasos) > 0 ? 'alerta' : 'exito'"
             detalle="{{ $sinIngredientes }} sin ingredientes, {{ $sinPasos }} sin pasos" />
</div>

<div class="grid gap-5 lg:grid-cols-2">
    <x-grafico-barras titulo="Recetas por categoría" :datos="$porCategoria" unidad="recetas"
                      vacio="Todavía no hay recetas registradas." />

    <div class="space-y-5">
        <x-grafico-barras titulo="Origen de las recetas" :datos="$porOrigen" unidad="recetas"
                          vacio="Todavía no hay recetas." />
        <x-grafico-barras titulo="Nivel de dificultad" :datos="$porDificultad" unidad="recetas"
                          vacio="Todavía no hay recetas." />
    </div>
</div>

<div class="mt-5 grid gap-5 lg:grid-cols-2">
    <x-tabla-top titulo="Mejor valoradas" :datos="$mejorValoradas" columna="Promedio" segunda="votos"
                 vacio="Todavía no hay valoraciones." />
    <x-tabla-top titulo="Más guardadas como favoritas" :datos="$masFavoritas" columna="Usuarios"
                 vacio="Todavía nadie ha guardado favoritas." />
</div>
@endsection
