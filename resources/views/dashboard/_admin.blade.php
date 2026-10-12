{{--
    Panel del administrador.

    Tres bloques, de lo que pide una acción a lo que solo informa:
    1. Requiere tu atención: lo accionable, cada fila lleva a donde se resuelve.
    2. Actividad: búsquedas de los últimos 14 días comparadas con los 14 anteriores.
    3. Salud del catálogo: estado, sin acciones.
    Lo personal (despensa, favoritas, búsquedas propias) va al final, en una línea.
--}}
@php
    /**
     * "12 más que los 14 días anteriores". La comparación se dice con
     * palabras: la flecha acompaña, el color no carga nada.
     */
    $comparar = function (?int $ahora, ?int $antes, string $periodo, string $mas = 'más', string $menos = 'menos', string $unidad = '') {
        if ($ahora === null || $antes === null) {
            return ['texto' => "Sin datos de {$periodo} para comparar.", 'flecha' => null];
        }

        $diferencia = $ahora - $antes;

        return match (true) {
            $diferencia > 0 => ['texto' => "{$diferencia}{$unidad} {$mas} que {$periodo}", 'flecha' => 'sube'],
            $diferencia < 0 => ['texto' => abs($diferencia) . "{$unidad} {$menos} que {$periodo}", 'flecha' => 'baja'],
            default         => ['texto' => "Igual que {$periodo}", 'flecha' => 'igual'],
        };
    };

    $flechas = [
        'sube'  => 'M7 14l5-5 5 5H7z',
        'baja'  => 'M7 10l5 5 5-5H7z',
        'igual' => 'M5 11h14v2H5z',
    ];

    $a = $actividad;
    $cifras = [
        [
            'titulo' => 'Búsquedas',
            'valor'  => $a['busquedas'],
            'detalle' => 'en los últimos 14 días',
            'cambio' => $comparar($a['busquedas'], $a['busquedasAntes'], 'los 14 días anteriores'),
        ],
        [
            'titulo' => 'Tiempo de respuesta',
            'valor'  => $a['promedioMs'] === null ? 'Sin datos' : $a['promedioMs'] . ' ms',
            'detalle' => 'promedio del generador',
            // Para un tiempo, "más" es peor: se dice lento o rápido
            'cambio' => $comparar($a['promedioMs'], $a['promedioMsAntes'], 'los 14 días anteriores',
                                  'más lento', 'más rápido', ' ms'),
        ],
        [
            'titulo' => 'Personas activas',
            'valor'  => $a['activos'],
            'detalle' => 'con alguna acción en los últimos 7 días',
            'cambio' => $comparar($a['activos'], $a['activosAntes'], 'los 7 días anteriores'),
        ],
    ];

    $c = $catalogo;
    $porcentaje = $c['recetas'] > 0 ? (int) round(100 * $c['verificadas'] / $c['recetas']) : 0;
@endphp

{{-- ═══════════════ 1. Requiere tu atención ═══════════════ --}}
<section class="mb-8" aria-labelledby="atencion-titulo">
    <h2 id="atencion-titulo" class="titulo-serif mb-4 text-2xl">Pendientes</h2>

    @if ($atencion->isEmpty())
        <div class="vidrio flex items-center gap-3 rounded-2xl px-6 py-5">
            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0 text-exito-400" fill="currentColor" aria-hidden="true">
                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
            </svg>
            <p class="text-sm text-crema-100">Al día: no hay nada pendiente en el sistema.</p>
        </div>
    @else
        <ul class="vidrio divide-y divide-oro-500/10 rounded-2xl">
            @foreach ($atencion as $fila)
                <li>
                    <a href="{{ $fila['enlace'] }}"
                       class="flex items-center gap-4 px-5 py-4 transition hover:bg-oro-500/8 sm:px-6">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0 text-alerta-400" fill="currentColor" aria-hidden="true">
                            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>

                        <span class="titulo-serif w-12 shrink-0 text-right text-3xl tabular-nums text-alerta-400">{{ $fila['cantidad'] }}</span>

                        <span class="min-w-0 flex-1">
                            <span class="block text-sm text-crema-100">
                                <span class="sr-only">Pendiente: </span>{{ $fila['texto'] }}
                            </span>
                            @isset($fila['detalle'])
                                <span class="mt-0.5 block text-xs leading-relaxed text-crema-500">{{ $fila['detalle'] }}</span>
                            @endisset
                        </span>

                        <span class="hidden shrink-0 text-xs text-oro-400 sm:inline">Revisar</span>
                        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-oro-400" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>

{{-- ═══════════════ 2. Actividad ═══════════════ --}}
<section class="mb-8" aria-labelledby="actividad-titulo">
    <h2 id="actividad-titulo" class="titulo-serif mb-4 text-2xl">Uso del generador</h2>

    <div class="grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <x-grafico-barras titulo="Búsquedas por día, últimos 14 días"
                          :datos="$a['grafica']"
                          unidad="búsquedas" />

        <div class="grid content-start gap-4 sm:grid-cols-3 lg:grid-cols-1">
            @foreach ($cifras as $cifra)
                <div class="vidrio rounded-2xl p-5">
                    <p class="text-sm text-crema-300">{{ $cifra['titulo'] }}</p>
                    <p class="titulo-serif mt-2 text-4xl text-crema-100">{{ $cifra['valor'] }}</p>
                    <p class="mt-1 text-xs text-crema-500">{{ $cifra['detalle'] }}</p>

                    <p class="mt-3 flex items-center gap-1.5 border-t border-oro-500/10 pt-3 text-xs text-crema-300">
                        @if ($cifra['cambio']['flecha'])
                            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                                <path d="{{ $flechas[$cifra['cambio']['flecha']] }}"/>
                            </svg>
                        @endif
                        {{ $cifra['cambio']['texto'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ 3. Salud del catálogo ═══════════════ --}}
<section class="mb-8" aria-labelledby="catalogo-titulo">
    <h2 id="catalogo-titulo" class="titulo-serif mb-4 text-2xl">Recetas e ingredientes</h2>

    <div class="mb-4 grid gap-4 lg:grid-cols-[2fr_1fr]">
        <div class="vidrio rounded-2xl p-5">
            <p class="text-sm text-crema-300">Recetas verificadas</p>

            <p class="titulo-serif mt-2 text-4xl text-oro-400">
                {{ $c['verificadas'] }}<span class="text-xl text-crema-500"> de {{ $c['recetas'] }}</span>
            </p>

            <div class="mt-3 flex items-center gap-3">
                <span class="block h-2 flex-1 rounded-full bg-crema-100/6" role="img"
                      aria-label="{{ $porcentaje }}% de las recetas activas están verificadas">
                    <span class="block h-full rounded-r-[4px] bg-oro-500"
                          style="width: {{ $c['verificadas'] > 0 ? max(2, $porcentaje) : 0 }}%"></span>
                </span>
                <span class="text-xs tabular-nums text-crema-100">{{ $porcentaje }}%</span>
            </div>

            <p class="mt-3 text-xs text-crema-500">
                {{ $c['recetasNuevas'] }} {{ $c['recetasNuevas'] === 1 ? 'receta nueva' : 'recetas nuevas' }} en los últimos 30 días.
            </p>
        </div>

        <x-ficha titulo="Ingredientes activos" :valor="$c['ingredientes']" tono="neutro"
                 :detalle="$c['ingredientesNuevos'] . ($c['ingredientesNuevos'] === 1 ? ' nuevo' : ' nuevos') . ' en los últimos 30 días'" />
    </div>

    {{-- El reparto por estado: cada ficha lleva icono y nombre, no solo color --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($c['estados'] as $estado => $cantidad)
            <x-ficha-estado :estado="$estado" :valor="$cantidad" />
        @endforeach
    </div>
</section>

{{-- ═══════════════ Lo tuyo ═══════════════ --}}
<section aria-labelledby="personal-titulo"
         class="vidrio flex flex-wrap items-center gap-x-6 gap-y-3 rounded-2xl px-5 py-4 text-sm sm:px-6">
    <h2 id="personal-titulo" class="rotulo w-full sm:w-auto">Lo tuyo</h2>

    <a href="{{ route('inventario.index') }}" class="text-crema-300 transition hover:text-oro-400">
        Despensa: <span class="tabular-nums text-crema-100">{{ $personal['despensa'] }}</span>
        @if ($personal['porCaducar'] > 0)
            <span class="text-alerta-400">({{ $personal['porCaducar'] }} por caducar)</span>
        @endif
    </a>

    <a href="{{ route('recetas.favoritas') }}" class="text-crema-300 transition hover:text-oro-400">
        Favoritas: <span class="tabular-nums text-crema-100">{{ $personal['favoritos'] }}</span>
    </a>

    <a href="{{ route('generador.historial') }}" class="text-crema-300 transition hover:text-oro-400">
        Tus búsquedas: <span class="tabular-nums text-crema-100">{{ $personal['consultas'] }}</span>
    </a>

    <a href="{{ route('generador.index') }}" class="text-oro-400 hover:underline sm:ml-auto">Ir al generador</a>
</section>
