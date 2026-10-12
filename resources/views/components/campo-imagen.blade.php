{{--
    Campo para subir una imagen, con vista previa de la que ya hay.

    <x-campo-imagen nombre="img_rec" etiqueta="Fotografía" :url="$receta->urlImagen()" />

    El formulario que lo contenga DEBE llevar enctype="multipart/form-data",
    o el archivo nunca llega al servidor y no hay error que lo delate.

    ── Por qué no se usa el input de archivo tal cual ──────────────────────

    El botón nativo lo dibuja el navegador y su texto («Choose File», «No
    file chosen») viene del idioma del navegador, no del de la página: en un
    Chrome en inglés el formulario sale medio en inglés. Aquí el input queda
    oculto pero accesible, y el <label> hace de botón.

    Sin JavaScript sigue funcionando: se puede elegir el archivo igual, solo
    que no se ve el nombre ni la vista previa hasta guardar.
--}}
@props(['nombre', 'etiqueta' => 'Imagen', 'url' => null, 'ayuda' => null])

@php
    // Varios campos pueden convivir en una misma página
    $id = $nombre . '-' . \Illuminate\Support\Str::random(6);
@endphp

<div>
    <label for="{{ $id }}" class="etiqueta-campo">{{ $etiqueta }}</label>

    <div class="flex flex-wrap items-start gap-4" data-campo-imagen>

        {{-- Lo que hay hoy, o el hueco donde iría --}}
        <div class="shrink-0">
            <img src="{{ $url }}" alt=""
                 data-vista-previa
                 class="h-24 w-24 rounded-xl object-cover ring-1 ring-oro-500/20 @unless($url) hidden @endunless">

            <div data-hueco
                 class="grid h-24 w-24 place-items-center rounded-xl bg-crema-100/6 ring-1 ring-oro-500/12 @if($url) hidden @endif">
                <svg viewBox="0 0 24 24" class="h-7 w-7 text-crema-500" fill="currentColor" aria-hidden="true">
                    <path d="M21 19V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2zM8.5 13.5l2.5 3 3.5-4.5 4.5 6H5l3.5-4.5z"/>
                </svg>
            </div>
        </div>

        <div class="min-w-0 flex-1">
            {{-- sr-only y no hidden: oculto pero enfocable con el teclado --}}
            <input id="{{ $id }}" name="{{ $nombre }}" type="file"
                   accept="image/jpeg,image/png,image/webp"
                   data-entrada
                   @error($nombre) aria-invalid="true" @enderror
                   class="peer sr-only">

            <div class="flex flex-wrap items-center gap-3">
                <label for="{{ $id }}"
                       class="boton-vidrio vidrio cursor-pointer text-sm
                              peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-oro-400">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                        <path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z"/>
                    </svg>
                    {{ $url ? 'Cambiar imagen' : 'Elegir imagen' }}
                </label>

                <span data-nombre-archivo class="truncate text-sm text-crema-500"></span>
            </div>

            @error($nombre)
                <p class="mt-1.5 flex items-start gap-1.5 text-xs text-error-400">
                    <svg viewBox="0 0 24 24" class="mt-px h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <span>{{ $message }}</span>
                </p>
            @else
                <p class="mt-2 text-xs text-crema-500">
                    {{ $ayuda ?? 'JPG, PNG o WEBP, hasta 3 MB. Opcional.' }}
                </p>
            @enderror

            {{-- Sin esto solo se podría reemplazar la imagen, nunca dejarla sin ninguna --}}
            @if ($url)
                <label class="mt-3 flex cursor-pointer items-center gap-2.5 text-sm text-crema-300">
                    <input type="checkbox" name="quitar_{{ $nombre }}" value="1"
                           class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                    Quitar la imagen actual
                </label>
            @endif
        </div>
    </div>
</div>
