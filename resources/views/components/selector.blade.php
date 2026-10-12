{{--
    Lista desplegable.
    <x-selector nombre="cod_x" etiqueta="Categoría" :opciones="$cats" clave="id" texto="nombre" :valor="$m->x" requerido />
--}}
@props([
    'nombre', 'etiqueta', 'opciones' => [], 'clave' => 'id', 'texto' => 'nombre',
    'valor' => null, 'requerido' => false, 'vacio' => null, 'ayuda' => null,
])

<div>
    <label for="{{ $nombre }}" class="etiqueta-campo">
        {{ $etiqueta }}
        @if ($requerido)
            <span class="text-oro-400" aria-hidden="true">*</span>
            <span class="sr-only">(obligatorio)</span>
        @endif
    </label>

    <select id="{{ $nombre }}" name="{{ $nombre }}" @if ($requerido) required @endif
            @class(['campo', 'campo-error' => $errors->has($nombre)])>
        @if ($vacio)
            <option value="">{{ $vacio }}</option>
        @endif

        @foreach ($opciones as $opcion)
            @php $v = is_array($opcion) ? $opcion[$clave] : $opcion->{$clave}; @endphp
            <option value="{{ $v }}" @selected(old($nombre, $valor) == $v)>
                {{ is_array($opcion) ? $opcion[$texto] : $opcion->{$texto} }}
            </option>
        @endforeach
    </select>

    @error($nombre)
        <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p>
    @else
        @if ($ayuda)
            <p class="mt-1.5 text-xs text-crema-500">{{ $ayuda }}</p>
        @endif
    @enderror
</div>
