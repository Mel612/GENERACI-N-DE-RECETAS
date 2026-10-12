{{--
    Ficha con un número destacado.
    <x-ficha titulo="Usuarios activos" :valor="12" detalle="de 15 registrados" />
--}}
@props(['titulo', 'valor', 'detalle' => null, 'tono' => 'oro', 'icono' => null, 'enlace' => null])

@php
    $color = [
        'oro'    => 'text-oro-400',
        'exito'  => 'text-exito-400',
        'alerta' => 'text-alerta-400',
        'error'  => 'text-error-400',
        'neutro' => 'text-crema-300',
    ][$tono] ?? 'text-oro-400';

    $etiqueta = $enlace ? 'a' : 'div';
@endphp

<{{ $etiqueta }} @if ($enlace) href="{{ $enlace }}" @endif
    class="vidrio block rounded-2xl p-5 {{ $enlace ? 'elevar transition' : '' }}">

    <div class="flex items-start justify-between gap-3">
        <p class="text-sm leading-snug text-crema-300">{{ $titulo }}</p>

        @if ($icono)
            <span class="vidrio grid h-9 w-9 shrink-0 place-items-center rounded-lg">
                <svg viewBox="0 0 24 24" class="h-4 w-4 {{ $color }}" fill="currentColor" aria-hidden="true">
                    <path d="{{ $icono }}"/>
                </svg>
            </span>
        @endif
    </div>

    <p class="titulo-serif mt-3 text-4xl {{ $color }}">{{ $valor }}</p>

    @if ($detalle)
        <p class="mt-1 text-xs text-crema-500">{{ $detalle }}</p>
    @endif
</{{ $etiqueta }}>
