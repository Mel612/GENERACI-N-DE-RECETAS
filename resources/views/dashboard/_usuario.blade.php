{{--
    Panel de quien cocina.

    Como una página del cuaderno: a la izquierda lo anotado en la despensa,
    renglón por renglón, con lo que vence primero arriba; a la derecha la
    receta que se puede cocinar hoy, con su sello de validación. Las cifras
    sueltas van al pie, en una línea: no son a lo que se entra.

    El nutricionista usa este mismo panel; sus cifras propias (bandeja y
    catálogo) aparecen en la línea del pie y solo si no están en cero.
--}}
@php
    $niveles = ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'];

    $cantidad = fn ($d) => rtrim(rtrim(number_format((float) $d->can_inv, 2, '.', ''), '0'), '.') . ' ' . $d->abr_uni;

    // Cuántos días faltan, dicho como se dice
    $vence = function ($d) {
        if (! $d->fec_cad_inv) {
            return null;
        }
        $dias = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($d->fec_cad_inv), false);

        return match (true) {
            $dias < 0   => 'vencido',
            $dias === 0 => 'vence hoy',
            $dias === 1 => 'vence mañana',
            default     => "vence en {$dias} días",
        };
    };
@endphp

{{-- En pantalla ancha el margen rojo del cuaderno separa las dos columnas --}}
<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_5px_minmax(0,1.4fr)]">

    {{-- ═══════════════ La despensa, renglón por renglón ═══════════════ --}}
    <section class="vidrio flex flex-col rounded-2xl" aria-labelledby="despensa-titulo">
        <div class="flex items-baseline justify-between gap-3 border-b border-linea-fuerte px-6 pb-3 pt-5">
            <h2 id="despensa-titulo" class="titulo-serif text-2xl">Mi despensa</h2>
            <span class="text-sm tabular-nums text-crema-500">
                {{ trans_choice(':count ingrediente|:count ingredientes', $resumen['ingredientes']) }}
            </span>
        </div>

        @if ($despensa->isEmpty())
            <div class="flex flex-1 flex-col items-start justify-center gap-4 px-6 py-10">
                <p class="max-w-xs text-sm leading-relaxed text-crema-300">
                    Todavía no anotaste nada. Registra lo que tienes en casa y el
                    sistema te dirá qué puedes cocinar con eso.
                </p>
                <a href="{{ route('inventario.index') }}" class="boton-oro">Agregar ingredientes</a>
            </div>
        @else
            <ul class="flex-1 px-6">
                @foreach ($despensa as $d)
                    @php $aviso = $vence($d); @endphp
                    <li class="flex min-h-12 items-center justify-between gap-3 border-b border-linea py-2 last:border-0">
                        <span class="min-w-0 truncate text-crema-100">{{ $d->nom_ing }}</span>
                        <span class="flex shrink-0 items-center gap-3 text-sm">
                            @if ($d->urgente && $aviso)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $d->vencido ? 'text-error-400' : 'text-alerta-400' }}">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                                        <path d="M12 2a10 10 0 110 20 10 10 0 010-20zm1 5h-2v6l5 3 1-1.7-4-2.3V7z"/>
                                    </svg>
                                    {{ $aviso }}
                                </span>
                            @endif
                            <span class="tabular-nums text-crema-300">{{ $cantidad($d) }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>

            <div class="border-t border-linea px-6 py-3.5">
                <a href="{{ route('inventario.index') }}" class="text-sm font-semibold text-oro-500 hover:underline">
                    @if ($resumen['ingredientes'] > $despensa->count())
                        Ver toda la despensa ({{ $resumen['ingredientes'] }})
                    @else
                        Editar la despensa
                    @endif
                </a>
            </div>
        @endif
    </section>

    <span class="hidden border-x border-margen opacity-60 lg:block" aria-hidden="true"></span>

    {{-- ═══════════════ Lo que se puede cocinar hoy ═══════════════ --}}
    {{-- Sin verificar, la receta del día va en lápiz como en el recetario --}}
    <section @class(['vidrio flex flex-col overflow-hidden rounded-2xl', 'en-lapiz' => $destacada && ! $destacada->ver_rec])
             aria-labelledby="hoy-titulo">
        @if ($destacada)
            @php $foto = \App\Support\Imagen::url($destacada->img_rec ?? null); @endphp

            <div class="flex items-center justify-between gap-3 border-b border-linea-fuerte px-6 py-3">
                <h2 id="hoy-titulo" class="titulo-serif text-2xl">Puedes cocinar hoy</h2>
                <a href="{{ route('generador.index') }}" class="boton-oro px-4 py-2 text-sm">Buscar qué cocino</a>
            </div>

            @if ($foto)
                <a href="{{ route('recetas.show', $destacada->cod_receta) }}" class="block border-b border-linea">
                    <x-foto :url="$foto" class="h-48 w-full" />
                </a>
            @endif

            <div class="flex flex-1 flex-col p-6">
                <div class="flex items-start justify-between gap-6">
                    <h3 class="titulo-serif text-[clamp(1.7rem,3vw,2.3rem)]">
                        <a href="{{ route('recetas.show', $destacada->cod_receta) }}" class="hover:text-oro-500">{{ $destacada->nom_rec }}</a>
                    </h3>
                    <p class="shrink-0 text-right">
                        <span class="titulo-serif block text-4xl tabular-nums {{ $destacada->compatibilidad >= 100 ? 'text-exito-400' : 'text-oro-500' }}">
                            {{ rtrim(rtrim(number_format((float) $destacada->compatibilidad, 1), '0'), '.') }}%
                        </span>
                        <span class="text-xs text-crema-500">de lo que pide</span>
                    </p>
                </div>

                <p class="mt-3">
                    @if ($destacada->ver_rec)
                        <span class="sello-verificada sello-estampar"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada</span>
                    @else
                        <span class="sello sello-lapiz sello-estampar"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zM20.7 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>Sin verificar</span>
                    @endif
                </p>

                @if ($destacada->des_rec)
                    <p class="mt-4 line-clamp-3 max-w-prose text-sm leading-relaxed text-crema-300">{{ $destacada->des_rec }}</p>
                @endif

                <dl class="mt-5 grid grid-cols-3 border-y border-linea text-sm">
                    <div class="py-3">
                        <dt class="text-xs text-crema-500">Tiempo</dt>
                        <dd class="mt-0.5 tabular-nums text-crema-100">{{ $destacada->tie_tot_rec }} min</dd>
                    </div>
                    <div class="border-l border-linea py-3 pl-4">
                        <dt class="text-xs text-crema-500">Porciones</dt>
                        <dd class="mt-0.5 tabular-nums text-crema-100">{{ $destacada->por_rec }}</dd>
                    </div>
                    <div class="border-l border-linea py-3 pl-4">
                        <dt class="text-xs text-crema-500">Dificultad</dt>
                        <dd class="mt-0.5 text-crema-100">{{ $niveles[$destacada->niv_rec] ?? $destacada->niv_rec }}</dd>
                    </div>
                </dl>

                <div class="flex-1"></div>

                <div class="mt-6">
                    <a href="{{ route('recetas.show', $destacada->cod_receta) }}" class="boton-vidrio">Ver la receta completa</a>
                </div>
            </div>
        @elseif ($despensa->isEmpty())
            {{-- La acción de anotar ya está en la columna de la despensa: aquí
                 no se repite, se ofrece lo que se puede hacer mientras tanto --}}
            <div class="flex flex-1 flex-col justify-center p-8 sm:p-10">
                <h2 id="hoy-titulo" class="titulo-serif text-[clamp(1.7rem,3vw,2.3rem)]">Aquí verás qué cocinar hoy</h2>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-crema-300">
                    En cuanto anotes lo que tienes, el sistema elige la receta que
                    mejor lo aprovecha y te dice qué te falta.
                </p>
                <div class="mt-6">
                    <a href="{{ route('recetas.index') }}" class="boton-vidrio">Mientras tanto, ver el recetario</a>
                </div>
            </div>
        @else
            {{-- Hay despensa pero ninguna receta la usa: decirlo es más útil que esconderlo --}}
            <div class="flex flex-1 flex-col justify-center p-8 sm:p-10">
                <h2 id="hoy-titulo" class="titulo-serif text-[clamp(1.7rem,3vw,2.3rem)]">Todavía no alcanza para una receta</h2>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-crema-300">
                    Ninguna receta del recetario usa lo que tienes anotado. Prueba el
                    generador: te muestra qué te faltaría comprar para cada una.
                </p>
                <div class="mt-6">
                    <a href="{{ route('generador.index') }}" class="boton-oro">Buscar qué cocino</a>
                </div>
            </div>
        @endif
    </section>
</div>

{{-- ═══════════════ Aprovechamiento: la tasa por peso ═══════════════ --}}
<a href="{{ route('aprovechamiento.index') }}" class="vidrio elevar mt-6 block rounded-2xl px-6 py-5">
    <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
        <div class="min-w-0">
            <h2 id="aprovechamiento-titulo" class="text-sm text-crema-300">Aprovechamiento de tu despensa, por peso</h2>
            @if ($aprovechamiento?->tasa_por_peso !== null)
                <p class="titulo-serif mt-1 text-4xl text-crema-50">
                    {{ \App\Models\SalidaInventario::porcentaje($aprovechamiento->tasa_por_peso) }}
                </p>
            @else
                <p class="mt-2 max-w-md text-sm text-crema-300">
                    Todavía sin datos: cuando quites algo de la despensa y digas qué pasó,
                    aquí verás cuánto aprovechas.
                </p>
            @endif
        </div>
        <span class="text-sm font-semibold text-oro-500">Ver el detalle &rarr;</span>
    </div>

    @if ($aprovechamiento?->tasa_por_peso !== null)
        <div class="mt-4 flex h-2.5 overflow-hidden rounded-full bg-masa-950" aria-hidden="true">
            <span class="block h-full bg-exito-400" style="width: {{ (float) $aprovechamiento->tasa_por_peso }}%"></span>
            <span class="block h-full bg-error-400" style="width: {{ 100 - (float) $aprovechamiento->tasa_por_peso }}%"></span>
        </div>
        <p class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs">
            <span class="inline-flex items-center gap-1.5 text-exito-400">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true"><path d="{{ \App\Models\SalidaInventario::ICONO_MOTIVO['consumido'] }}"/></svg>
                Consumido: {{ \App\Models\SalidaInventario::peso($aprovechamiento->g_consumido) }}
            </span>
            <span class="inline-flex items-center gap-1.5 text-error-400">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true"><path d="{{ \App\Models\SalidaInventario::ICONO_MOTIVO['desperdiciado'] }}"/></svg>
                Desperdiciado: {{ \App\Models\SalidaInventario::peso($aprovechamiento->g_desperdiciado) }}
            </span>
        </p>
    @endif
</a>

{{-- ═══════════════ El pie de la página: el resto, en una línea ═══════════════ --}}
<section aria-label="Resumen"
         class="vidrio mt-6 flex flex-wrap items-center gap-x-7 gap-y-3 rounded-2xl px-6 py-4 text-sm">

    @if (($resumen['por_revisar'] ?? 0) > 0)
        <a href="{{ route('nutricionista.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-alerta-400 hover:underline">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            {{ trans_choice(':count receta por revisar|:count recetas por revisar', $resumen['por_revisar']) }}
        </a>
    @endif

    @if (($resumen['sin_nutricion'] ?? 0) > 0)
        <a href="{{ route('admin.ingredientes.index', ['sin_nutricion' => 1, 'estado' => 'activos']) }}" class="inline-flex items-center gap-1.5 font-semibold text-alerta-400 hover:underline">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            {{ trans_choice(':count ingrediente sin datos nutricionales|:count ingredientes sin datos nutricionales', $resumen['sin_nutricion']) }}
        </a>
    @endif

    <a href="{{ route('recetas.favoritas') }}" class="text-crema-300 transition hover:text-oro-500">
        Favoritas: <span class="font-semibold tabular-nums text-crema-100">{{ $resumen['favoritos'] }}</span>
    </a>

    <a href="{{ route('generador.historial') }}" class="text-crema-300 transition hover:text-oro-500">
        Búsquedas: <span class="font-semibold tabular-nums text-crema-100">{{ $resumen['consultas'] }}</span>
    </a>

    <span class="text-crema-300">
        Recetas verificadas: <span class="font-semibold tabular-nums text-crema-100">{{ $verificadas }} de {{ $totalRecetas }}</span>
    </span>

    @if ($usuario->esNutricionista())
        <a href="{{ route('nutricionista.index') }}" class="font-semibold text-oro-500 hover:underline sm:ml-auto">Ir a validación</a>
    @endif
</section>
