{{--
    Cuántas recetas hay en un estado de validación.

    El estado lo dice el sello: icono, palabra y trazo (el de pendiente es
    punteado, como el lápiz). La cifra va en tinta normal, así que nada
    depende del color: se entiende igual en blanco y negro.

    <x-ficha-estado estado="aprobada" :valor="12" />
--}}
@props(['estado', 'valor', 'detalle' => null])

@php
    $mapa = [
        'pendiente' => ['Pendientes de revisión', 'sello-lapiz',
                        'M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zM20.7 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z'],
        'aprobada'  => ['Aprobadas', 'text-exito-400',
                        'M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z'],
        'observada' => ['Con observaciones', 'text-alerta-400',
                        'M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'],
        'rechazada' => ['Rechazadas', 'text-error-400',
                        'M19 6.4L17.6 5 12 10.6 6.4 5 5 6.4l5.6 5.6L5 17.6 6.4 19l5.6-5.6 5.6 5.6 1.4-1.4-5.6-5.6z'],
    ];

    [$texto, $tono, $icono] = $mapa[$estado] ?? ['Sin estado', 'sello-lapiz', ''];
@endphp

<div class="vidrio rounded-2xl p-5">
    <span class="sello sello-recto {{ $tono }}">
        @if ($icono)
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{{ $icono }}"/></svg>
        @endif
        {{ $texto }}
    </span>

    <p class="titulo-serif mt-4 text-4xl tabular-nums text-crema-50">{{ $valor }}</p>

    @if ($detalle)
        <p class="mt-1 text-xs text-crema-500">{{ $detalle }}</p>
    @endif
</div>
