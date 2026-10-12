{{--
    Campo de formulario reutilizable.
    Uso: <x-campo nombre="cor_usu" etiqueta="Correo electrónico" tipo="email" requerido />
--}}
@props([
    'nombre',
    'etiqueta',
    'tipo'      => 'text',
    'valor'     => null,
    'requerido' => false,
    'ayuda'     => null,
    'icono'     => null,
    'readonly'  => false,
])

<div>
    <label for="{{ $nombre }}" class="etiqueta-campo">
        {{ $etiqueta }}
        @if ($requerido)
            <span class="text-oro-400" aria-hidden="true">*</span>
            <span class="sr-only">(obligatorio)</span>
        @endif
    </label>

    <div class="relative">
        @if ($icono)
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-crema-500">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="{{ $icono }}"/>
                </svg>
            </span>
        @endif

        <input id="{{ $nombre }}"
               name="{{ $nombre }}"
               type="{{ $tipo }}"
               value="{{ $tipo === 'password' ? '' : old($nombre, $valor) }}"
               @if ($requerido) required @endif
               @if ($readonly) readonly @endif
               @error($nombre) aria-invalid="true" aria-describedby="{{ $nombre }}-error" @enderror
               {{ $attributes->class([
                   'campo',
                   'pl-11'       => (bool) $icono,
                   'opacity-60 cursor-not-allowed' => $readonly,
                   'campo-error' => $errors->has($nombre),
               ]) }}>
    </div>

    @error($nombre)
        <p id="{{ $nombre }}-error" class="mt-1.5 flex items-start gap-1.5 text-xs text-error-400">
            <svg viewBox="0 0 24 24" class="mt-px h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true">
                <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <span>{{ $message }}</span>
        </p>
    @else
        @if ($ayuda)
            <p class="mt-1.5 text-xs text-crema-500">{{ $ayuda }}</p>
        @endif
    @enderror
</div>
