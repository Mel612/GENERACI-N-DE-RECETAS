@extends('layouts.app')

@section('titulo', 'Mi despensa')

@section('contenido')
<x-cabecera rotulo="Mi cocina" titulo="Mi despensa"
            subtitulo="Registra lo que tienes en casa para que el sistema sepa qué puedes cocinar." />

{{-- Formularios ocultos de salida, un par por fila: los botones de motivo
     apuntan a ellos con el atributo form, desde la cola y desde la lista. --}}
@foreach ($inventario->concat($porResolver)->unique('cod_inventario') as $fila)
    <form id="salida-{{ $fila->cod_inventario }}" method="POST" class="hidden"
          action="{{ route('inventario.destroy', $fila) }}">
        @csrf
        @method('DELETE')
    </form>
    <form id="desperdicio-{{ $fila->cod_inventario }}" method="POST" class="hidden"
          action="{{ route('inventario.destroy', $fila) }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="mot_sal" value="desperdiciado">
    </form>
@endforeach

{{-- ═══════════ Por resolver: lo vencido espera respuesta ═══════════ --}}
@if ($porResolver->isNotEmpty())
    <section aria-labelledby="por-resolver-titulo"
             class="vidrio mb-6 overflow-visible rounded-2xl border-error-400/40">
        <div class="border-b border-linea px-5 pb-4 pt-5 sm:px-6">
            <p class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-error-400">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {{ trans_choice('1 vencido por resolver|:count vencidos por resolver', $porResolver->count()) }}
            </p>
            <h2 id="por-resolver-titulo" class="titulo-serif mt-1 text-2xl">¿Qué pasó con esto?</h2>
            <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-crema-300">
                Ya no se usan para sugerirte recetas. Cuéntanos qué pasó con cada uno: así
                se mide cuánto de tu despensa se aprovecha. Lo que siga sin respuesta una
                semana después de vencer se cierra solo como «sin resolver», sin contarlo
                como desperdicio.
            </p>
        </div>

        <ul>
            @foreach ($porResolver as $fila)
                <li class="flex flex-col gap-3 border-b border-linea px-5 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="min-w-0">
                        <p class="font-medium text-crema-100">
                            {{ $fila->ingrediente->nom_ing }}
                            <span class="font-normal tabular-nums text-crema-300">
                                &middot; {{ rtrim(rtrim(number_format((float) $fila->can_inv, 3, ',', ''), '0'), ',') }} {{ $fila->unidad->abr_uni }}
                            </span>
                        </p>
                        <p class="mt-0.5 inline-flex flex-wrap items-center gap-1 text-xs text-error-400">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                                <path d="M12 2a10 10 0 110 20 10 10 0 010-20zm1 5h-2v6l5 3 1-1.7-4-2.3V7z"/>
                            </svg>
                            Venció el {{ $fila->fec_cad_inv->format('d/m/Y') }}
                            ({{ trans_choice('hace :count día|hace :count días', (int) $fila->dias_vencido) }})
                        </p>
                    </div>

                    <x-motivo-salida :formulario="'salida-' . $fila->cod_inventario"
                                     :desperdicio="'desperdicio-' . $fila->cod_inventario"
                                     :nombre="$fila->ingrediente->nom_ing"
                                     class="shrink-0" />
                </li>
            @endforeach
        </ul>
    </section>
@endif

<div class="mb-6 grid gap-4 sm:grid-cols-3">
    <x-ficha titulo="Ingredientes disponibles" :valor="$resumen['total']" />
    <x-ficha titulo="Por caducar en 7 días" :valor="$resumen['por_caducar']"
             :tono="$resumen['por_caducar'] > 0 ? 'alerta' : 'neutro'"
             detalle="Se priorizan al generar recetas" />
    <x-ficha titulo="Vencidos" :valor="$resumen['vencidos']"
             :tono="$resumen['vencidos'] > 0 ? 'error' : 'neutro'" />
</div>

@if ($resumen['total'] > 0)
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('generador.index') }}" class="boton-oro">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                <path d="M12 3l1.9 5.9H20l-4.9 3.6 1.9 5.9L12 14.8 7 18.4l1.9-5.9L4 8.9h6.1L12 3z"/>
            </svg>
            Ver qué puedo cocinar
        </a>
    </div>
@endif

{{-- ═══════════ Agregar ═══════════ --}}
{{-- overflow-visible: la lista del buscador debe poder salir de la tarjeta --}}
<section class="vidrio relative z-20 mb-6 overflow-visible rounded-2xl p-6">
    <h2 class="titulo-serif mb-4 text-xl">Agregar a la despensa</h2>

    @if ($disponibles->isEmpty() && ! $iaDisponible)
        <p class="text-sm text-crema-300">
            Ya tienes en tu despensa todos los ingredientes del catálogo.
        </p>
    @else
        @php
            $elegido = $disponibles->firstWhere('cod_ingrediente', (int) old('cod_ingrediente_inv'));
        @endphp

        <form method="POST" action="{{ route('inventario.store') }}" id="formulario-despensa"
              class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_7rem_10rem_11rem_auto]">
            @csrf

            {{-- Buscador de ingredientes: filtra el catálogo mientras se escribe y,
                 si no encuentra nada, ofrece consultarlo con la IA (que solo acepta
                 cosas comestibles). La lógica está en resources/js/despensa.js. --}}
            <div class="relative" data-buscador-ingredientes
                 data-url-ia="{{ $iaDisponible ? route('inventario.ia') : '' }}">
                <label for="buscar-ingrediente" class="etiqueta-campo">Ingrediente</label>
                <input id="buscar-ingrediente" type="text" autocomplete="off" required
                       role="combobox" aria-autocomplete="list" aria-expanded="false"
                       aria-controls="opciones-ingrediente" maxlength="60"
                       placeholder="Escribe: papa, locoto, quinua…"
                       value="{{ $elegido?->nom_ing }}"
                       @class(['campo', 'campo-error' => $errors->has('cod_ingrediente_inv')])>
                <input type="hidden" name="cod_ingrediente_inv" value="{{ $elegido?->cod_ingrediente }}">

                <ul id="opciones-ingrediente" role="listbox" hidden
                    class="absolute inset-x-0 top-full z-30 mt-1 max-h-72 overflow-y-auto rounded-xl border border-linea-fuerte bg-masa-850 py-1 shadow-lg"></ul>

                <p data-aviso role="status" aria-live="polite" class="mt-1.5 text-xs text-crema-500"></p>
                @error('cod_ingrediente_inv') <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror

                <script type="application/json" data-catalogo>
                    {!! \Illuminate\Support\Js::encode($disponibles->map(fn ($i) => [
                        'id'        => $i->cod_ingrediente,
                        'nombre'    => $i->nom_ing,
                        'sinonimos' => (string) $i->sin_ing,
                        'categoria' => $i->categoria->nom_cat,
                    ])->values()) !!}
                </script>
            </div>

            <div>
                <label for="can_inv" class="etiqueta-campo">Cantidad</label>
                <input id="can_inv" type="number" step="0.001" min="0.001" name="can_inv"
                       value="{{ old('can_inv') }}" required placeholder="500"
                       @class(['campo', 'campo-error' => $errors->has('can_inv')])>
                @error('can_inv') <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="cod_unidad_inv" class="etiqueta-campo">Unidad</label>
                <select id="cod_unidad_inv" name="cod_unidad_inv" required
                        @class(['campo', 'campo-error' => $errors->has('cod_unidad_inv')])>
                    <option value="">Unidad</option>
                    @foreach ($unidades->groupBy('tip_uni') as $tipo => $grupo)
                        <optgroup label="{{ ucfirst($tipo) }}">
                            @foreach ($grupo as $unidad)
                                <option value="{{ $unidad->cod_unidad }}" @selected(old('cod_unidad_inv') == $unidad->cod_unidad)>
                                    {{ $unidad->nom_uni }} ({{ $unidad->abr_uni }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('cod_unidad_inv') <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="fec_cad_inv" class="etiqueta-campo">
                    Caduca <span class="font-normal text-crema-500">(opcional)</span>
                </label>
                <input id="fec_cad_inv" type="date" name="fec_cad_inv" value="{{ old('fec_cad_inv') }}"
                       min="{{ now()->toDateString() }}"
                       @class(['campo', 'campo-error' => $errors->has('fec_cad_inv')])>
                @error('fec_cad_inv') <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-end">
                <button type="submit" class="boton-oro w-full sm:w-auto">Agregar</button>
            </div>
        </form>
    @endif
</section>

{{-- ═══════════ Listado ═══════════ --}}
@if ($resumen['filas'] === 0 && ! request()->hasAny(['buscar', 'categoria', 'estado']))
    <div class="vidrio rounded-2xl p-10 text-center">
        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10 text-oro-500/60" fill="currentColor" aria-hidden="true">
            <path d="M5 2h14a1 1 0 011 1v18a1 1 0 01-1 1H5a1 1 0 01-1-1V3a1 1 0 011-1zm2 3v5h4V5H7zm6 0v5h4V5h-4zm-6 9v5h4v-5H7zm6 0v5h4v-5h-4z"/>
        </svg>
        <h2 class="titulo-serif mt-4 text-2xl">Tu despensa está vacía</h2>
        <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-crema-300">
            Agrega lo que tienes en casa con el formulario de arriba. Con tres o cuatro
            ingredientes el sistema ya empieza a sugerirte recetas.
        </p>
    </div>
@else
    <x-buscador :limpiar="route('inventario.index')" marcador="Buscar en mi despensa">
        <label class="sr-only" for="categoria">Categoría</label>
        <select id="categoria" name="categoria" class="campo max-w-[14rem]">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->cod_categoria_ingrediente }}"
                        @selected(request('categoria') == $categoria->cod_categoria_ingrediente)>
                    {{ $categoria->nom_cat }}
                </option>
            @endforeach
        </select>

        <label class="sr-only" for="estado">Estado</label>
        <select id="estado" name="estado" class="campo max-w-[12rem]">
            <option value="">Todos</option>
            <option value="por_caducar" @selected(request('estado') === 'por_caducar')>Por caducar</option>
            <option value="vencidos"    @selected(request('estado') === 'vencidos')>Vencidos</option>
            <option value="sin_fecha"   @selected(request('estado') === 'sin_fecha')>Sin fecha</option>
        </select>
    </x-buscador>

    @if ($inventario->isEmpty())
        <div class="vidrio rounded-2xl px-6 py-14 text-center text-crema-500">
            No se encontró nada con esos criterios.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($inventario as $fila)
                @php
                    // $hoy viene de la base: con now() de PHP (UTC), de noche
                    // lo que vence hoy ya salía como vencido
                    $vencido = $fila->fec_cad_inv && $fila->fec_cad_inv->lt($hoy);
                    $urgente = ! $vencido && $fila->fec_cad_inv
                               && $fila->fec_cad_inv->lte($hoy->copy()->addDays(7));
                @endphp

                <form method="POST" action="{{ route('inventario.update', $fila) }}"
                      @class([
                          // overflow-visible: el desplegable de Quitar sale de la tarjeta
                          'vidrio grid grid-cols-2 items-end gap-3 overflow-visible rounded-2xl p-4 sm:grid-cols-[1fr_6.5rem_9rem_10rem_auto]',
                          'border border-error-400/30'  => $vencido,
                          'border border-alerta-400/30' => $urgente,
                      ])>
                    @csrf
                    @method('PUT')

                    <div class="col-span-2 sm:col-span-1">
                        <p class="font-medium text-crema-100">{{ $fila->ingrediente->nom_ing }}</p>
                        <p class="mt-0.5 text-xs text-crema-500">{{ $fila->ingrediente->categoria->nom_cat }}</p>

                        @if ($vencido)
                            <p class="mt-1 text-xs text-error-400">
                                Venció el {{ $fila->fec_cad_inv->format('d/m/Y') }}
                            </p>
                        @elseif ($urgente)
                            <p class="mt-1 text-xs text-alerta-400">
                                Caduca el {{ $fila->fec_cad_inv->format('d/m/Y') }} &middot; se priorizará
                            </p>
                        @endif
                    </div>

                    {{-- En móvil las etiquetas se ven: sin la cabecera de columnas de
                         escritorio, un campo suelto no dice qué es. --}}
                    <div>
                        <label class="etiqueta-campo sm:sr-only" for="can-{{ $fila->cod_inventario }}">Cantidad</label>
                        <input id="can-{{ $fila->cod_inventario }}" type="number" step="0.001" min="0"
                               name="can_inv" value="{{ rtrim(rtrim(number_format((float) $fila->can_inv, 3, '.', ''), '0'), '.') }}"
                               class="campo text-sm" required>
                    </div>

                    <div>
                        <label class="etiqueta-campo sm:sr-only" for="uni-{{ $fila->cod_inventario }}">Unidad</label>
                        <select id="uni-{{ $fila->cod_inventario }}" name="cod_unidad_inv" class="campo text-sm" required>
                            @foreach ($unidades->groupBy('tip_uni') as $tipo => $grupo)
                                <optgroup label="{{ ucfirst($tipo) }}">
                                    @foreach ($grupo as $unidad)
                                        <option value="{{ $unidad->cod_unidad }}" @selected($fila->cod_unidad_inv == $unidad->cod_unidad)>
                                            {{ $unidad->abr_uni }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="etiqueta-campo sm:sr-only" for="cad-{{ $fila->cod_inventario }}">Caduca</label>
                        <input id="cad-{{ $fila->cod_inventario }}" type="date" name="fec_cad_inv"
                               value="{{ $fila->fec_cad_inv?->toDateString() }}" class="campo text-sm">
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-1 sm:col-span-1">
                        <button type="submit"
                                class="rounded-full px-3.5 py-2 text-xs text-crema-300 transition hover:bg-oro-500/12 hover:text-crema-100">
                            Guardar<span class="sr-only"> {{ $fila->ingrediente->nom_ing }}</span>
                        </button>
                        {{-- Quitar ya no es un borrado mudo: pregunta qué pasó. Los
                             botones envían los formularios ocultos de arriba. --}}
                        <details class="menu relative">
                            <summary class="cursor-pointer list-none rounded-full px-3.5 py-2 text-xs text-error-400 transition hover:bg-error-400/10">
                                Quitar<span class="sr-only"> {{ $fila->ingrediente->nom_ing }} de la despensa</span>
                            </summary>
                            <div class="vidrio-solido absolute right-0 top-full z-30 mt-2 w-64 rounded-2xl p-2">
                                <p class="px-3 pb-1 pt-1.5 text-sm font-medium text-crema-100">¿Qué pasó con esto?</p>
                                <x-motivo-salida :formulario="'salida-' . $fila->cod_inventario"
                                                 :desperdicio="'desperdicio-' . $fila->cod_inventario"
                                                 :nombre="$fila->ingrediente->nom_ing"
                                                 :flotante="false" />
                            </div>
                        </details>
                    </div>
                </form>
            @endforeach
        </div>

        {{-- Vaciar también registra la salida, con un solo motivo para todo --}}
        @foreach (['vaciar' => null, 'vaciar-desperdicio' => 'desperdiciado'] as $id => $motivo)
            <form id="{{ $id }}" method="POST" action="{{ route('inventario.vaciar') }}" class="hidden"
                  data-confirmar-titulo="¿Vaciar toda tu despensa?"
                  data-confirmar="{{ trans_choice('Esto quita el único ingrediente que tienes.|Esto quita los :count ingredientes que tienes.', $resumen['filas']) }} No se puede deshacer."
                  data-confirmar-boton="Sí, vaciar">
                @csrf
                @if ($motivo)
                    <input type="hidden" name="mot_sal" value="{{ $motivo }}">
                @endif
            </form>
        @endforeach

        <details class="menu mt-8">
            <summary class="inline-flex cursor-pointer list-none text-xs text-error-400 transition hover:underline">
                Vaciar toda la despensa
            </summary>
            <div class="vidrio mt-3 max-w-sm rounded-2xl p-2">
                <p class="px-3 pt-1.5 text-sm font-medium text-crema-100">¿Qué pasó con todo esto?</p>
                <p class="px-3 pb-2 pt-0.5 text-xs text-crema-500">
                    {{ trans_choice('Se anota el mismo motivo para el único ingrediente.|Se anota el mismo motivo para los :count ingredientes.', $resumen['filas']) }}
                </p>
                <x-motivo-salida formulario="vaciar" desperdicio="vaciar-desperdicio" :flotante="false" />
            </div>
        </details>
    @endif
@endif
@endsection
