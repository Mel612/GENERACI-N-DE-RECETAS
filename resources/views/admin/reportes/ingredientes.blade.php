@extends('layouts.app')

@section('titulo', 'Reporte de ingredientes')

@section('contenido')
<x-cabecera rotulo="Reportes" titulo="Ingredientes"
            subtitulo="Estado del catálogo y uso real de cada alimento." />

<a href="{{ route('admin.reportes.index') }}"
   class="mb-7 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver a reportes
</a>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ficha titulo="En el catálogo" :valor="$total" />
    <x-ficha titulo="Sin datos nutricionales" :valor="$sinNutricion"
             :tono="$sinNutricion > 0 ? 'alerta' : 'exito'"
             :enlace="route('admin.ingredientes.index', ['sin_nutricion' => 1])"
             detalle="Impiden calcular calorías por porción" />
    <x-ficha titulo="Marcados como alérgenos" :valor="$alergenos" tono="neutro" />
    <x-ficha titulo="Por caducar en 7 días" :valor="$porCaducar"
             :tono="$porCaducar > 0 ? 'alerta' : 'neutro'"
             detalle="En las despensas de los usuarios" />
</div>

<x-grafico-barras titulo="Ingredientes por categoría" :datos="$porCategoria" unidad="ingredientes"
                  vacio="Todavía no hay ingredientes registrados." />

<div class="mt-5 grid gap-5 lg:grid-cols-2">
    <x-tabla-top titulo="Más usados en recetas" :datos="$masUsadosEnRecetas" columna="Recetas"
                 vacio="Todavía no hay recetas con ingredientes." />
    <x-tabla-top titulo="Más presentes en las despensas" :datos="$masEnDespensas" columna="Usuarios"
                 vacio="Todavía nadie ha registrado su despensa." />
</div>
@endsection
