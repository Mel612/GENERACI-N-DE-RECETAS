{{--
    Imagen de una receta o un ingrediente, con su hueco cuando no tiene.

    Acepta la URL ya resuelta o el valor crudo de la columna:

        <x-foto :url="$receta->urlImagen()" class="h-40 w-full" />
        <x-foto :valor="$fila->img_rec"     class="h-16 w-16 rounded-xl" />

    Lo segundo hace falta porque el generador devuelve filas de una consulta,
    no modelos, y ahí no existe urlImagen().

    El hueco no es un error: la mayoría de las recetas no van a tener foto
    nunca. Por eso es un bloque discreto con un icono, y no un recuadro roto
    ni un «imagen no disponible».

    El alt va vacío a propósito cuando la imagen acompaña a un título que ya
    dice lo mismo: repetirlo hace que un lector de pantalla lea dos veces el
    nombre del plato. Para una imagen que sí aporta, se pasa decorativa=false.
--}}
@props(['url' => null, 'valor' => null, 'alt' => '', 'icono' => 'plato', 'decorativa' => true])

@php
    $fuente = $url ?? \App\Support\Imagen::url($valor);

    $iconos = [
        'plato' => 'M12 2a1 1 0 011 1v1h3a1 1 0 010 2h-.1l-.8 3h1.4a1 1 0 011 1v1a3 3 0 01-3 3h-.2l-.5 5.1a2 2 0 01-2 1.9H9.2a2 2 0 01-2-1.9L6.7 14h-.2a3 3 0 01-3-3v-1a1 1 0 011-1h1.4l-.8-3H5a1 1 0 010-2h3V3a1 1 0 011-1h3z',
        'ingrediente' => 'M12 2a10 10 0 100 20 10 10 0 000-20zm0 4a6 6 0 110 12 6 6 0 010-12zm0 3a3 3 0 100 6 3 3 0 000-6z',
    ];
@endphp

@if ($fuente)
    <img src="{{ $fuente }}"
         alt="{{ $decorativa ? '' : $alt }}"
         loading="lazy"
         {{ $attributes->class(['object-cover']) }}>
@else
    {{-- El hueco es el espacio del cuaderno donde iría la foto pegada --}}
    <div {{ $attributes->class(['hueco-foto grid place-items-center']) }}
         role="presentation">
        <svg viewBox="0 0 24 24" class="h-7 w-7 text-oro-500/30" fill="currentColor" aria-hidden="true">
            <path d="{{ $iconos[$icono] ?? $iconos['plato'] }}"/>
        </svg>
    </div>
@endif
