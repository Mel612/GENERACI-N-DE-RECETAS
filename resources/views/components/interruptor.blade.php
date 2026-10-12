{{-- Casilla de verificacion con el estilo del sistema. --}}
@props(['nombre', 'etiqueta', 'valor' => true, 'ayuda' => null])

<div>
    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-crema-300">
        <input type="checkbox" name="{{ $nombre }}" value="1"
               @checked(old($nombre, $valor))
               class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
        {{ $etiqueta }}
    </label>

    @if ($ayuda)
        <p class="mt-1.5 text-xs text-crema-500">{{ $ayuda }}</p>
    @endif
</div>
