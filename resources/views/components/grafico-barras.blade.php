{{--
    Barras horizontales de una sola serie.

    Una sola serie significa un solo color: no hace falta leyenda ni paleta
    categórica. El título nombra la serie y cada barra lleva su valor escrito,
    así que ningún dato depende de pasar el cursor por encima.

    <x-grafico-barras titulo="Ingredientes por categoría" :datos="$porCategoria" unidad="ingredientes" />

    Cada dato necesita 'etiqueta' y 'valor'.
--}}
@props(['titulo' => null, 'datos' => [], 'unidad' => '', 'vacio' => 'Todavía no hay datos que mostrar.', 'limite' => null])

@php
    $filas = collect($datos)->map(fn ($d) => [
        'etiqueta' => is_array($d) ? $d['etiqueta'] : $d->etiqueta,
        'valor'    => (float) (is_array($d) ? $d['valor'] : $d->valor),
    ]);

    if ($limite) {
        $filas = $filas->take($limite);
    }

    $maximo = max(1, $filas->max('valor') ?? 1);
    $total  = max(1, $filas->sum('valor'));
@endphp

<figure class="vidrio rounded-2xl p-6">
    @if ($titulo)
        <figcaption class="mb-5 text-sm font-medium text-crema-100">{{ $titulo }}</figcaption>
    @endif

    @if ($filas->isEmpty())
        <p class="py-8 text-center text-sm text-crema-500">{{ $vacio }}</p>
    @else
        <div class="space-y-2.5">
            @foreach ($filas as $fila)
                @php
                    // Un cero no lleva barra: la astilla mínima es para que
                    // un valor chico se vea, no para inventar uno
                    $ancho      = $fila['valor'] > 0 ? max(1.5, $fila['valor'] / $maximo * 100) : 0;
                    $porcentaje = round($fila['valor'] / $total * 100, 1);
                @endphp

                <div class="grid grid-cols-[minmax(6rem,11rem)_1fr_auto] items-center gap-3"
                     title="{{ $fila['etiqueta'] }}: {{ $fila['valor'] }} {{ $unidad }} ({{ $porcentaje }}% del total)">

                    <span class="truncate text-xs text-crema-300">{{ $fila['etiqueta'] }}</span>

                    {{-- La pista es el fondo recesivo; la barra es el dato --}}
                    <span class="block h-2.5 rounded-full bg-crema-100/6">
                        <span class="block h-full rounded-r-[4px] bg-oro-500"
                              style="width: {{ $ancho }}%"></span>
                    </span>

                    <span class="w-10 text-right text-xs tabular-nums text-crema-100">{{ $fila['valor'] }}</span>
                </div>
            @endforeach
        </div>

        @if ($unidad)
            <p class="mt-5 border-t border-oro-500/10 pt-3 text-xs text-crema-500">
                Total: {{ $filas->sum('valor') }} {{ $unidad }}
            </p>
        @endif
    @endif
</figure>
