@extends('layouts.app')

@section('titulo', 'Aprovechamiento')

@use('App\Models\SalidaInventario', 'Salida')

@section('contenido')
<x-cabecera rotulo="Mi cocina" titulo="Aprovechamiento"
            subtitulo="Qué parte de lo que sale de tu despensa se come y qué parte se bota, según lo que tú respondes." />

@if (! $totales)
    <div class="vidrio rounded-2xl p-10 text-center">
        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10 text-oro-500/60" fill="currentColor" aria-hidden="true">
            <path d="M5 9.2h3V19H5V9.2zM10.6 5h2.8v14h-2.8V5zm5.6 8H19v6h-2.8v-6z"/>
        </svg>
        <h2 class="titulo-serif mt-4 text-2xl">Todavía no hay nada que medir</h2>
        <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-crema-300">
            Cada vez que quites algo de tu despensa y digas qué pasó con ello —lo
            consumiste, se desperdició u otra cosa—, aquí verás cuánto aprovechas.
        </p>
        <a href="{{ route('inventario.index') }}" class="boton-oro mt-6">Ir a mi despensa</a>
    </div>
@else
    @php
        $tasaPeso     = $totales->tasa_por_peso;
        $tasaRegistro = $totales->tasa_por_registro;
        $nConsumido   = (int) $totales->n_consumido;
        $nDesperdicio = (int) $totales->n_desperdiciado;
        $gConsumido   = (float) $totales->g_consumido;
        $gDesperdicio = (float) $totales->g_desperdiciado;
        $sinPeso      = (int) $totales->sin_peso;
    @endphp

    {{-- ═══════════ Las dos tasas ═══════════ --}}
    <div class="grid gap-4 lg:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">

        <section class="vidrio rounded-2xl p-6 sm:p-7" aria-labelledby="tasa-peso-titulo">
            <h2 id="tasa-peso-titulo" class="text-sm text-crema-300">Tasa de aprovechamiento por peso</h2>

            @if ($tasaPeso !== null)
                <p class="titulo-serif mt-2 text-[clamp(3.2rem,10vw,5rem)] leading-none text-crema-50">
                    {{ Salida::porcentaje($tasaPeso) }}
                </p>
                <p class="mt-2 text-sm text-crema-300">
                    de lo que salió de tu despensa, medido en gramos, se consumió.
                </p>

                {{-- Barra partida: consumido y desperdiciado. La leyenda de abajo
                     repite cada parte con icono y palabra. --}}
                <div class="mt-6 flex h-3 overflow-hidden rounded-full bg-masa-950" role="img"
                     aria-label="Consumido {{ Salida::peso($gConsumido) }}, desperdiciado {{ Salida::peso($gDesperdicio) }}">
                    <span class="block h-full bg-exito-400" style="width: {{ (float) $tasaPeso }}%"></span>
                    <span class="block h-full bg-error-400" style="width: {{ 100 - (float) $tasaPeso }}%"></span>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="inline-flex items-center gap-1.5 text-sm text-exito-400">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true"><path d="{{ Salida::ICONO_MOTIVO['consumido'] }}"/></svg>
                            Consumido
                        </dt>
                        <dd class="mt-0.5 text-2xl tabular-nums text-crema-100">{{ Salida::peso($gConsumido) }}</dd>
                    </div>
                    <div>
                        <dt class="inline-flex items-center gap-1.5 text-sm text-error-400">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true"><path d="{{ Salida::ICONO_MOTIVO['desperdiciado'] }}"/></svg>
                            Desperdiciado
                        </dt>
                        <dd class="mt-0.5 text-2xl tabular-nums text-crema-100">{{ Salida::peso($gDesperdicio) }}</dd>
                    </div>
                </dl>
            @else
                <p class="titulo-serif mt-2 text-5xl leading-none text-crema-500" aria-label="Sin dato">—</p>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-crema-300">
                    Todavía no hay nada consumido o desperdiciado que se pueda pesar: lo que
                    salió se mide en piezas o en volumen sin densidad registrada.
                </p>
            @endif
        </section>

        <section class="vidrio rounded-2xl p-6" aria-labelledby="tasa-registro-titulo">
            <h2 id="tasa-registro-titulo" class="text-sm text-crema-300">Tasa por registro</h2>

            @if ($tasaRegistro !== null)
                <p class="titulo-serif mt-2 text-4xl text-crema-100">{{ Salida::porcentaje($tasaRegistro) }}</p>
                <p class="mt-1 text-xs text-crema-500">
                    {{ $nConsumido }} de {{ $nConsumido + $nDesperdicio }} salidas se consumieron
                </p>
                <div class="mt-4 flex h-2 overflow-hidden rounded-full bg-masa-950" aria-hidden="true">
                    <span class="block h-full bg-exito-400" style="width: {{ (float) $tasaRegistro }}%"></span>
                    <span class="block h-full bg-error-400" style="width: {{ 100 - (float) $tasaRegistro }}%"></span>
                </div>
            @else
                <p class="titulo-serif mt-2 text-4xl text-crema-500" aria-label="Sin dato">—</p>
                <p class="mt-1 text-xs text-crema-500">Nada consumido ni desperdiciado todavía.</p>
            @endif

            {{-- Las dos cifras miden cosas distintas y ninguna es perfecta: decirlo
                 es parte del trabajo. --}}
            <p class="mt-5 flex items-start border-t border-linea pt-4 gap-2 text-xs leading-relaxed text-crema-500">
                <svg viewBox="0 0 24 24" class="mt-0.5 h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true">
                    <path d="M11 7h2v2h-2V7zm0 4h2v6h-2v-6zm1-9a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>
                <span>
                    La tasa por peso deja fuera lo que se mide en piezas (y los volúmenes de
                    ingredientes sin densidad): {{ trans_choice(':count de tus salidas no tiene peso medible.|:count de tus salidas no tienen peso medible.', $sinPeso) }}
                    La tasa por registro sí las cuenta, pero pesa igual un huevo que un kilo de arroz.
                    Ninguna de las dos incluye lo «sin resolver» ni lo marcado como «otro».
                </span>
            </p>
        </section>
    </div>

    {{-- ═══════════ Dinero y lo que no cuenta ═══════════ --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        @if ((int) $precios->total === 0)
            <x-ficha titulo="Bolivianos desperdiciados" valor="Bs 0" tono="neutro"
                     detalle="No registraste nada desperdiciado." />
        @elseif ((int) $precios->con_precio > 0)
            <x-ficha titulo="Bolivianos desperdiciados" tono="error"
                     :valor="'Bs ' . number_format((float) $totales->bs_desperdiciado, 2, ',', '.')"
                     :icono="Salida::ICONO_MOTIVO['desperdiciado']"
                     :detalle="(int) $precios->con_precio < (int) $precios->total
                        ? 'Precio de referencia. ' . ((int) $precios->total - (int) $precios->con_precio) . ' de ' . (int) $precios->total . ' desperdicios no tienen precio o peso: la cifra se queda corta.'
                        : 'Según el precio de referencia por kilo.'" />
        @else
            <div class="vidrio rounded-2xl p-5">
                <p class="text-sm text-crema-300">Bolivianos desperdiciados</p>
                <p class="mt-3 flex items-start gap-2 text-sm leading-relaxed text-alerta-400">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <span>
                        Falta cargar el precio de referencia de lo que desperdiciaste.
                        @if (auth()->user()->esAdministrador() || auth()->user()->esNutricionista())
                            <a href="{{ route('admin.ingredientes.index', ['sin_precio' => 1, 'estado' => 'activos']) }}" class="underline">Cargar precios</a>.
                        @else
                            Lo registra el administrador en el catálogo de ingredientes.
                        @endif
                    </span>
                </p>
            </div>
        @endif

        <x-ficha titulo="Sin resolver" :valor="(int) $totales->n_sin_resolver" tono="alerta"
                 :icono="Salida::ICONO_MOTIVO['sin_resolver']"
                 detalle="Vencidos que nadie respondió en 7 días. No cuentan ni a favor ni en contra." />

        <x-ficha titulo="Otro motivo" :valor="(int) $totales->n_otro" tono="neutro"
                 :icono="Salida::ICONO_MOTIVO['otro']"
                 detalle="Regalado, cambiado o quitado por otra razón. Tampoco cuenta." />
    </div>

    {{-- ═══════════ Ranking e historial ═══════════ --}}
    <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)]">
        <x-tabla-top titulo="Lo que más se desperdicia" columna="Veces"
                     :datos="$ranking->map(fn ($r) => (object) [
                         'etiqueta' => $r->etiqueta,
                         'valor'    => (int) $r->valor,
                         'peso'     => $r->gramos !== null ? Salida::peso($r->gramos) : null,
                     ])"
                     segunda="peso"
                     vacio="Nada desperdiciado todavía." />

        <section class="vidrio rounded-2xl" aria-labelledby="historial-titulo">
            <div class="flex items-baseline justify-between gap-3 border-b border-oro-500/12 px-5 py-4 sm:px-6">
                <h2 id="historial-titulo" class="text-sm font-medium text-crema-100">Historial de salidas</h2>
                <span class="text-xs tabular-nums text-crema-500">{{ trans_choice(':count salida|:count salidas', $historial->total()) }}</span>
            </div>

            <ul>
                @foreach ($historial as $salida)
                    @php
                        $retraso = $salida->dias_de_retraso;
                        $cuando  = match (true) {
                            $retraso === null => null,
                            $retraso > 0      => trans_choice(':count día después de vencer|:count días después de vencer', $retraso),
                            $retraso < 0      => trans_choice(':count día antes de vencer|:count días antes de vencer', -$retraso),
                            default           => 'el día que vencía',
                        };
                    @endphp
                    <li class="flex flex-wrap items-start justify-between gap-x-4 gap-y-1 border-b border-oro-500/8 px-5 py-3.5 last:border-0 sm:px-6">
                        <div class="min-w-0">
                            <p class="text-sm text-crema-100">
                                {{ $salida->nom_ing }}
                                <span class="tabular-nums text-crema-300">
                                    &middot; {{ rtrim(rtrim(number_format((float) $salida->can_sal, 3, ',', '.'), '0'), ',') }} {{ $salida->abr_uni }}
                                </span>
                            </p>
                            <p class="mt-0.5 text-xs text-crema-500">
                                <time datetime="{{ $salida->fec_sal }}">{{ \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') }}</time>
                                @if ($cuando) &middot; {{ $cuando }} @endif
                            </p>
                        </div>

                        <p class="inline-flex items-center gap-1.5 text-sm font-medium {{ Salida::COLOR_MOTIVO[$salida->mot_sal] }}">
                            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                                <path d="{{ Salida::ICONO_MOTIVO[$salida->mot_sal] }}"/>
                            </svg>
                            {{ Salida::TEXTO_MOTIVO[$salida->mot_sal] }}
                            @if ($salida->cau_sal)
                                <span class="font-normal text-crema-500">&middot; {{ Salida::TEXTO_CAUSA[$salida->cau_sal] }}</span>
                            @endif
                        </p>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <div class="mt-6">{{ $historial->links() }}</div>
@endif
@endsection
