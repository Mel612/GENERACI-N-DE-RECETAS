{{--
    Receta en un listado general, sin comparar contra la despensa.

    La foto solo aparece cuando existe: un listado lleno de recuadros vacíos
    no le dice nada a nadie. La receta sin verificar va escrita en lápiz
    (borde punteado); la verificada, en tinta.
--}}
@props(['receta', 'niveles' => [], 'esFavorita' => false])

@php $foto = $receta->urlImagen(); @endphp

<article @class(['vidrio elevar flex flex-col overflow-hidden rounded-2xl', 'en-lapiz' => ! $receta->ver_rec])>

    @if ($foto)
        <a href="{{ route('recetas.show', $receta) }}" class="block border-b border-linea">
            <x-foto :url="$foto" class="h-40 w-full" />
        </a>
    @endif

    <div class="flex flex-1 flex-col p-6">
    <div class="flex items-start justify-between gap-3">
        <a href="{{ route('recetas.show', $receta) }}"
           class="titulo-serif text-xl transition hover:text-oro-400">{{ $receta->nom_rec }}</a>

        @if ($esFavorita)
            <svg viewBox="0 0 24 24" class="mt-1 h-4 w-4 shrink-0 text-oro-400" fill="currentColor"
                 role="img" aria-label="En tus favoritas">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        @endif
    </div>

    <p class="mt-1.5 text-xs text-crema-500">
        <span class="font-semibold text-oro-500">{{ $receta->categoria->nom_car }}</span>
        &middot; <span class="tabular-nums">{{ $receta->tie_tot_rec }} min</span>
        &middot; {{ $niveles[$receta->niv_rec] ?? $receta->niv_rec }}
    </p>

    <p class="mt-3">
        @if ($receta->ver_rec)
            <span class="sello-verificada sello-recto"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada</span>
        @else
            <span class="sello sello-lapiz sello-recto"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zM20.7 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>Sin verificar</span>
        @endif
    </p>

    @if ($receta->des_rec)
        <p class="mt-3 line-clamp-2 flex-1 text-sm leading-relaxed text-crema-300">{{ $receta->des_rec }}</p>
    @else
        <div class="flex-1"></div>
    @endif

    @if ($receta->etiquetas->isNotEmpty())
        <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach ($receta->etiquetas->take(3) as $etiqueta)
                {{-- En grafito: el color de cada etiqueta se gestiona en
                     administración, aquí sería una paleta paralela a la del cuaderno --}}
                <span class="inline-flex items-center rounded-full border border-linea-fuerte px-2.5 py-0.5 text-xs text-crema-300">
                    {{ $etiqueta->nom_eti }}
                </span>
            @endforeach
        </div>
    @endif

    @if ($receta->valoraciones_count > 0)
        <p class="mt-4 flex items-center gap-1.5 text-xs text-oro-400">
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
            {{ round($receta->valoraciones_avg_pun_val, 1) }}
            <span class="text-crema-500">({{ $receta->valoraciones_count }})</span>
        </p>
    @endif
    </div>
</article>
