{{--
    Los tres botones de «¿qué pasó con esto?»: Lo consumí / Se desperdició / Otro.

    Son botones de envío de verdad, sin JavaScript. Apuntan con el atributo
    form a dos formularios ocultos que viven fuera de la fila (los
    formularios no se anidan):
      · formulario   recibe mot_sal del botón pulsado (consumido u otro)
      · desperdicio  ya trae mot_sal=desperdiciado; el botón pone cau_sal

    «Se desperdició» es un <details> que despliega las cuatro causas.
    flotante: las causas salen en un recuadro encima del contenido (en la
    cola); sin flotante, los botones se apilan y las causas se abren debajo
    (dentro de otro desplegable o de un recuadro).

    <x-motivo-salida formulario="salida-5" desperdicio="desperdicio-5" nombre="Leche" />
--}}
@props(['formulario', 'desperdicio', 'nombre' => null, 'flotante' => true])

@use('App\Models\SalidaInventario', 'Salida')

@php
    $sufijo = $nombre ? " {$nombre}" : '';
    $boton  = $flotante
        ? 'boton-vidrio cursor-pointer px-3.5 py-2 text-sm'
        : 'flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2.5 text-left text-sm text-crema-100 transition hover:bg-oro-500/10';
@endphp

<div {{ $attributes->class([
        'flex flex-wrap items-center gap-2' => $flotante,
        'flex flex-col'                     => ! $flotante,
    ]) }}>

    <button type="submit" form="{{ $formulario }}" name="mot_sal" value="consumido" class="{{ $boton }}">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-exito-400" fill="currentColor" aria-hidden="true">
            <path d="{{ Salida::ICONO_MOTIVO['consumido'] }}"/>
        </svg>
        Lo consumí<span class="sr-only">{{ $sufijo }}</span>
    </button>

    <details class="menu relative">
        <summary class="{{ $boton }} list-none">
            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-error-400" fill="currentColor" aria-hidden="true">
                <path d="{{ Salida::ICONO_MOTIVO['desperdiciado'] }}"/>
            </svg>
            Se desperdició<span class="sr-only">{{ $sufijo }}</span>
            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-crema-500 {{ $flotante ? '' : 'ml-auto' }}" fill="currentColor" aria-hidden="true">
                <path d="M7 10l5 5 5-5H7z"/>
            </svg>
        </summary>

        <div @class([
                'vidrio-solido absolute right-0 top-full z-30 mt-2 w-56 rounded-2xl p-2' => $flotante,
                'mb-1 ml-5 border-l border-linea-fuerte pl-2' => ! $flotante,
            ])>
            <p class="px-3 pb-1 pt-1.5 text-xs text-crema-500">¿Por qué se desperdició?</p>
            @foreach (Salida::TEXTO_CAUSA as $clave => $texto)
                <button type="submit" form="{{ $desperdicio }}" name="cau_sal" value="{{ $clave }}"
                        class="flex w-full items-center rounded-lg px-3 py-2.5 text-left text-sm text-crema-100 transition hover:bg-oro-500/10">
                    {{ $texto }}
                </button>
            @endforeach
        </div>
    </details>

    <button type="submit" form="{{ $formulario }}" name="mot_sal" value="otro" class="{{ $boton }}">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-crema-300" fill="currentColor" aria-hidden="true">
            <path d="{{ Salida::ICONO_MOTIVO['otro'] }}"/>
        </svg>
        Otro<span class="sr-only">{{ $sufijo }}</span>
    </button>
</div>
