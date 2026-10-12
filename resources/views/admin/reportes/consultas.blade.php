@extends('layouts.app')

@section('titulo', 'Reporte de consultas')

@section('contenido')
<x-cabecera rotulo="Reportes" titulo="Consultas y generación"
            subtitulo="Volumen de búsquedas y eficiencia del generador de recetas." />

<a href="{{ route('admin.reportes.index') }}"
   class="mb-7 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver a reportes
</a>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ficha titulo="Consultas realizadas" :valor="$total" />
    <x-ficha titulo="Resultados por consulta" :valor="$promedioResultados" detalle="Promedio" />
    <x-ficha titulo="Sin ningún resultado" :valor="$sinResultados"
             :tono="$sinResultados > 0 ? 'alerta' : 'exito'"
             detalle="El usuario no encontró nada que cocinar" />
    <x-ficha titulo="Compatibilidad promedio" :valor="$compatibilidadPromedio . '%'"
             detalle="Cuánto cubre la despensa" />
</div>

{{-- Estas dos cifras sostienen el objetivo específico de evaluar la eficiencia --}}
<div class="vidrio mb-6 rounded-2xl p-6">
    <h2 class="mb-1 text-sm font-medium text-crema-100">Eficiencia del sistema</h2>
    <p class="mb-5 text-xs text-crema-500">
        Sostiene el objetivo específico de evaluar el desempeño del sistema en términos
        de eficiencia y precisión.
    </p>

    <div class="grid gap-6 sm:grid-cols-3">
        <div>
            <p class="titulo-serif text-3xl text-oro-400">{{ $tiempoPromedio }} ms</p>
            <p class="mt-1 text-xs text-crema-500">Tiempo de respuesta promedio</p>
        </div>
        <div>
            <p class="titulo-serif text-3xl text-crema-100">{{ $tiempoMaximo }} ms</p>
            <p class="mt-1 text-xs text-crema-500">Peor tiempo registrado</p>
        </div>
        <div>
            <p class="titulo-serif text-3xl text-crema-100">
                {{ $resultados > 0 ? round($aperturas / $resultados * 100, 1) : 0 }}%
            </p>
            <p class="mt-1 text-xs text-crema-500">
                Recetas sugeridas que el usuario abrió ({{ $aperturas }} de {{ $resultados }})
            </p>
        </div>
    </div>
</div>

<div class="grid gap-5 lg:grid-cols-2">
    <x-grafico-barras titulo="Consultas por mes (últimos 12 meses)" :datos="$porMes" unidad="consultas"
                      vacio="Todavía no se han hecho consultas." />
    <x-tabla-top titulo="Recetas más sugeridas" :datos="$recetasMasSugeridas" columna="Veces"
                 vacio="Todavía no hay resultados registrados." />
</div>
@endsection
