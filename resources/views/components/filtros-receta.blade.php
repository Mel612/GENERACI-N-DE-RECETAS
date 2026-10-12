{{-- Filtros del generador. Se usan igual en el formulario y en los resultados. --}}
@props(['categorias', 'etiquetas', 'niveles', 'valores' => []])

@php $v = fn ($k, $d = null) => old($k, $valores[$k] ?? $d); @endphp

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label for="categoria" class="etiqueta-campo">Tipo de plato</label>
        <select id="categoria" name="categoria" class="campo">
            <option value="">Cualquiera</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->cod_categoria_receta }}"
                        @selected($v('categoria') == $categoria->cod_categoria_receta)>
                    {{ $categoria->nom_car }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="nivel" class="etiqueta-campo">Dificultad</label>
        <select id="nivel" name="nivel" class="campo">
            <option value="">Cualquiera</option>
            @foreach ($niveles as $clave => $texto)
                <option value="{{ $clave }}" @selected($v('nivel') === $clave)>{{ $texto }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="tiempo_max" class="etiqueta-campo">Tiempo máximo</label>
        <select id="tiempo_max" name="tiempo_max" class="campo">
            <option value="">Sin límite</option>
            @foreach ([15 => '15 minutos', 30 => '30 minutos', 45 => '45 minutos',
                       60 => '1 hora', 90 => '1 hora y media', 120 => '2 horas'] as $min => $texto)
                <option value="{{ $min }}" @selected($v('tiempo_max') == $min)>{{ $texto }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-col justify-end gap-2 pb-1">
        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-crema-300">
            <input type="checkbox" name="solo_completas" value="1" @checked($v('solo_completas'))
                   class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
            Solo lo que puedo cocinar ya
        </label>

        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-crema-300">
            <input type="checkbox" name="solo_verificadas" value="1" @checked($v('solo_verificadas'))
                   class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
            Solo recetas verificadas
        </label>
    </div>
</div>

<fieldset class="mt-4">
    <legend class="etiqueta-campo">Etiquetas</legend>
    @php $elegidas = (array) $v('etiquetas', []); @endphp

    <div class="mt-2 flex flex-wrap gap-2">
        @foreach ($etiquetas as $etiqueta)
            <label class="cursor-pointer">
                <input type="checkbox" name="etiquetas[]" value="{{ $etiqueta->cod_etiqueta }}"
                       @checked(in_array($etiqueta->cod_etiqueta, $elegidas))
                       class="peer sr-only">
                {{-- Elegida: tinta llena y una marca de visto, no solo el color --}}
                <span class="vidrio inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition
                             peer-checked:bg-oro-500 peer-checked:font-semibold peer-checked:text-masa-900 peer-checked:[&>svg]:inline
                             peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-oro-400">
                    <svg viewBox="0 0 24 24" class="hidden h-3 w-3" fill="currentColor" aria-hidden="true"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
                    {{ $etiqueta->nom_eti }}
                </span>
            </label>
        @endforeach
    </div>
</fieldset>
