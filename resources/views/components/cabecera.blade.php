{{--
    Cabecera de un modulo: titulo, subtitulo y accion principal.

    'rotulo' se sigue aceptando para no romper las vistas que lo pasan,
    pero ya no se pinta: el titulo se sostiene solo, sin etiqueta encima.
--}}
@props(['rotulo' => null, 'titulo', 'subtitulo' => null, 'accion' => null, 'textoAccion' => 'Nuevo'])

<div class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-linea pb-5">
    <div>
        <h1 class="titulo-serif text-[clamp(1.9rem,4vw,2.7rem)]">{{ $titulo }}</h1>
        @if ($subtitulo)
            <p class="mt-2 text-sm text-crema-300">{{ $subtitulo }}</p>
        @endif
    </div>

    @if ($accion)
        <a href="{{ $accion }}" class="boton-oro">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            {{ $textoAccion }}
        </a>
    @endif
</div>
