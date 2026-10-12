@extends('layouts.app')

@section('titulo', 'Inicio')

{{-- El panel de quien cocina trae su propio margen rojo, entre la despensa
     y la receta; una página del cuaderno tiene un solo margen --}}
@if ($rol !== 'admin')
    @section('sin-margen', '1')
@endif

@section('contenido')
@php
    $usuario = auth()->user();
    $hora    = (int) $reloj->hora;
    $saludo  = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');

    // La fecha va arriba a la derecha, como en el encabezado de una página
    $f       = \Carbon\Carbon::parse($reloj->fecha);
    $dias    = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $meses   = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
                'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $fecha   = "{$dias[$f->dayOfWeek]} {$f->day} de {$meses[$f->month]}";

    // Lo primero de la línea bajo el saludo es lo que más urge. El panel del
    // administrador trae su propia nota; los demás hablan de su despensa.
    $nota ??= match (true) {
        $resumen['por_resolver'] > 0 => $resumen['por_resolver'] === 1
            ? 'Tienes 1 ingrediente vencido por resolver en tu despensa.'
            : "Tienes {$resumen['por_resolver']} ingredientes vencidos por resolver en tu despensa.",
        $resumen['por_caducar'] > 0 => $resumen['por_caducar'] === 1
            ? 'Tienes 1 ingrediente por caducar esta semana.'
            : "Tienes {$resumen['por_caducar']} ingredientes por caducar esta semana.",
        $resumen['ingredientes'] === 0 => 'Tu despensa está vacía. Agrega lo que tienes y el sistema te dirá qué cocinar.',
        default => 'Todo en orden en tu despensa.',
    };
@endphp

<div class="mb-6 border-b border-linea-fuerte pb-5">
    <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1">
        <h1 class="titulo-serif text-[clamp(2rem,4.5vw,2.9rem)]">
            {{ $saludo }}, {{ \Illuminate\Support\Str::of($usuario->nom_usu)->explode(' ')->first() }}
        </h1>
        <p class="text-sm first-letter:uppercase text-crema-500">
            <time datetime="{{ $reloj->fecha }}">{{ $fecha }}</time>
        </p>
    </div>
    <p class="mt-2 text-sm text-crema-300">{{ $nota }}</p>
</div>

@include('dashboard._' . $rol)
@endsection
