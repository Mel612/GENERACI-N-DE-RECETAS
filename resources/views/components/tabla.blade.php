{{--
    Envoltorio de tabla. Recibe las cabeceras y el cuerpo como ranura.
    <x-tabla :cabeceras="['Nombre', 'Estado']" descripcion="Listado de etiquetas">
--}}
@props(['cabeceras' => [], 'descripcion' => null, 'vacio' => 'No se encontraron registros.', 'hay' => true])

<div class="vidrio overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            @if ($descripcion)
                <caption class="sr-only">{{ $descripcion }}</caption>
            @endif

            <thead>
                <tr class="border-b border-oro-500/12 text-left text-xs uppercase tracking-wide text-crema-500">
                    @foreach ($cabeceras as $cabecera)
                        <th scope="col" @class([
                            'px-5 py-3.5 font-medium',
                            'text-right' => str_starts_with($cabecera, '>'),
                        ])>{{ ltrim($cabecera, '>') }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @if ($hay)
                    {{ $slot }}
                @else
                    <tr>
                        <td colspan="{{ count($cabeceras) }}" class="px-5 py-14 text-center text-crema-500">
                            {{ $vacio }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
