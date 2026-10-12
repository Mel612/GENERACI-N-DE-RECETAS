{{-- Tabla corta de "los más...": etiqueta, valor y una barra de proporción. --}}
@props(['titulo', 'datos' => [], 'columna' => 'Cantidad', 'vacio' => 'Todavía no hay datos.', 'segunda' => null])

@php
    $filas  = collect($datos);
    $maximo = max(1, $filas->max(fn ($d) => (float) ($d->valor ?? $d->promedio ?? 0)) ?: 1);
@endphp

<figure class="vidrio overflow-hidden rounded-2xl">
    <figcaption class="border-b border-oro-500/12 px-6 py-4 text-sm font-medium text-crema-100">
        {{ $titulo }}
    </figcaption>

    @if ($filas->isEmpty())
        <p class="px-6 py-10 text-center text-sm text-crema-500">{{ $vacio }}</p>
    @else
        <table class="min-w-full text-sm">
            <thead class="sr-only">
                <tr><th scope="col">Nombre</th><th scope="col">{{ $columna }}</th></tr>
            </thead>
            <tbody>
                @foreach ($filas as $i => $fila)
                    @php $v = (float) ($fila->valor ?? $fila->promedio ?? 0); @endphp
                    <tr class="border-b border-oro-500/8 last:border-0">
                        <td class="w-8 px-6 py-3 text-xs tabular-nums text-crema-500">{{ $i + 1 }}</td>
                        <td class="py-3 pr-4 text-crema-100">{{ $fila->etiqueta }}</td>
                        <td class="w-28 py-3 pr-4">
                            <span class="block h-2 rounded-full bg-crema-100/6">
                                <span class="block h-full rounded-r-[4px] bg-oro-500"
                                      style="width: {{ max(2, $v / $maximo * 100) }}%"></span>
                            </span>
                        </td>
                        <td class="w-20 py-3 pr-6 text-right text-xs tabular-nums text-crema-100">
                            {{ $fila->promedio ?? $fila->valor }}
                            @if ($segunda && isset($fila->{$segunda}))
                                <span class="text-crema-500">({{ $fila->{$segunda} }})</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</figure>
