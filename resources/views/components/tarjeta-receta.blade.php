{{--
    Una receta en la lista de resultados.

    El porcentaje va escrito además de dibujado en la barra: nadie debería
    tener que interpretar un color para saber cuánto le falta.
--}}
@props(['receta', 'niveles' => []])

@php
    $compat = (float) ($receta->compatibilidad ?? 0);
    $faltan = $receta->ingredientes_faltantes ?? [];
    $tono   = $compat >= 100 ? 'text-exito-400' : ($compat >= 70 ? 'text-oro-400' : 'text-crema-300');
@endphp

{{-- Sin verificar va en lápiz (borde punteado), verificada en tinta --}}
<article @class(['vidrio elevar flex flex-col rounded-2xl p-6', 'en-lapiz' => ! $receta->ver_rec])>
    <div class="flex items-start justify-between gap-4">

        {{-- El generador devuelve filas de una consulta, no modelos: x-foto
             recibe el valor crudo de la columna y lo resuelve él. --}}
        <div class="flex min-w-0 items-start gap-4">
            <a href="{{ route('recetas.show', $receta->cod_receta) }}" class="shrink-0">
                <x-foto :valor="$receta->img_rec ?? null" class="h-16 w-16 rounded-xl" />
            </a>

            <div class="min-w-0">
                <a href="{{ route('recetas.show', $receta->cod_receta) }}"
                   class="titulo-serif line-clamp-2 block text-2xl transition hover:text-oro-400">
                    {{ $receta->nom_rec }}
                </a>

                <p class="mt-1 text-xs text-crema-500">
                    {{ $receta->categoria }} &middot; {{ $receta->tie_tot_rec }} min &middot;
                    {{ trans_choice(':count porción|:count porciones', (int) $receta->por_rec) }} &middot; {{ $niveles[$receta->niv_rec] ?? $receta->niv_rec }}
                </p>

                <p class="mt-2.5">
                    @if ($receta->ver_rec)
                        <span class="sello-verificada sello-recto"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada</span>
                    @else
                        <span class="sello sello-lapiz sello-recto"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zM20.7 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>Sin verificar</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="shrink-0 text-right">
            <p class="titulo-serif text-3xl tabular-nums {{ $tono }}">{{ rtrim(rtrim(number_format($compat, 1), '0'), '.') }}%</p>
            <p class="text-[0.62rem] uppercase tracking-wide text-crema-500">compatible</p>
        </div>
    </div>

    {{-- Barra de compatibilidad --}}
    <span class="mt-4 block h-2 rounded-full bg-crema-100/6" role="img"
          aria-label="Cubre {{ $receta->cubiertos }} de {{ $receta->requeridos }} ingredientes">
        <span class="block h-full rounded-r-[4px] {{ $compat >= 100 ? 'bg-exito-400' : 'bg-oro-500' }}"
              style="width: {{ $compat > 0 ? max(2, min(100, $compat)) : 0 }}%"></span>
    </span>

    <p class="mt-2 text-xs text-crema-500">
        Tienes {{ $receta->cubiertos }} de {{ $receta->requeridos }} ingredientes
        &middot; usa {{ $receta->usa_de_despensa }} de tu despensa
        @if ($receta->usa_urgentes > 0)
            &middot; <span class="text-alerta-400">aprovecha {{ $receta->usa_urgentes }} por caducar</span>
        @endif
    </p>

    @if ($receta->des_rec)
        <p class="mt-4 line-clamp-2 text-sm leading-relaxed text-crema-300">{{ $receta->des_rec }}</p>
    @endif

    <div class="flex-1"></div>

    @if ($faltan)
        <div class="mt-5 rounded-xl border border-alerta-400/20 bg-alerta-400/5 px-4 py-3">
            <p class="text-xs font-medium text-alerta-400">Te falta comprar</p>
            <p class="mt-1 text-xs leading-relaxed text-crema-300">{{ implode(', ', $faltan) }}</p>
        </div>
    @else
        <div class="mt-5 rounded-xl border border-exito-400/20 bg-exito-400/5 px-4 py-3">
            <p class="text-xs text-exito-400">Tienes todo lo necesario para prepararla.</p>
        </div>
    @endif

    <a href="{{ route('recetas.show', $receta->cod_receta) }}"
       class="boton-vidrio vidrio mt-4 w-full text-sm">Ver la receta</a>
</article>
