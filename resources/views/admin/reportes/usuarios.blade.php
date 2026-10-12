@extends('layouts.app')

@section('titulo', 'Reporte de usuarios')

@section('contenido')
<x-cabecera rotulo="Reportes" titulo="Usuarios"
            subtitulo="Composición de las cuentas registradas en el sistema." />

<a href="{{ route('admin.reportes.index') }}"
   class="mb-7 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver a reportes
</a>

<div class="mb-6 grid gap-4 sm:grid-cols-3">
    <x-ficha titulo="Cuentas activas"        :valor="$activos" tono="exito" />
    <x-ficha titulo="Inactivas o dadas de baja" :valor="$inactivos" tono="neutro" />
    <x-ficha titulo="Con preferencias declaradas" :valor="$conPreferencias"
             detalle="de {{ $activos }} cuentas activas" />
</div>

<div class="grid gap-5 lg:grid-cols-2">
    <x-grafico-barras titulo="Usuarios por rol" :datos="$porRol" unidad="usuarios" />
    <x-grafico-barras titulo="Altas por mes (últimos 12 meses)" :datos="$porMes" unidad="altas"
                      vacio="Todavía no hay registros en los últimos doce meses." />
</div>

<div class="mt-5">
    <x-tabla-top titulo="Preferencias alimentarias más elegidas" :datos="$preferenciasMasElegidas"
                 columna="Usuarios" vacio="Ningún usuario ha declarado preferencias todavía." />
</div>
@endsection
