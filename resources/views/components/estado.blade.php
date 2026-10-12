{{-- Distintivo de activo / inactivo. --}}
@props(['activo' => true, 'si' => 'Activo', 'no' => 'Inactivo'])

@if ($activo)
    <span class="inline-flex items-center gap-1.5 rounded-full bg-exito-400/12 px-3 py-1 text-xs text-exito-400">
        <span class="h-1.5 w-1.5 rounded-full bg-exito-400"></span>{{ $si }}
    </span>
@else
    <span class="inline-flex items-center gap-1.5 rounded-full bg-crema-100/6 px-3 py-1 text-xs text-crema-500">
        <span class="h-1.5 w-1.5 rounded-full bg-crema-500"></span>{{ $no }}
    </span>
@endif
