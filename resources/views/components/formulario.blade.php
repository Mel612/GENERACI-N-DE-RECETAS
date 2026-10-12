{{-- Tarjeta de formulario con sus botones. --}}
@props(['accion', 'metodo' => 'POST', 'volver', 'textoEnviar' => 'Guardar', 'ancho' => 'max-w-2xl', 'archivos' => false])

<div class="mx-auto {{ $ancho }}">
    {{-- Sin enctype el archivo no llega al servidor y no hay error que lo delate --}}
    <form method="POST" action="{{ $accion }}" class="vidrio space-y-5 rounded-2xl p-7"
          @if ($archivos) enctype="multipart/form-data" @endif>
        @csrf
        @if (strtoupper($metodo) !== 'POST')
            @method($metodo)
        @endif

        {{ $slot }}

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="boton-oro">{{ $textoEnviar }}</button>
            <a href="{{ $volver }}" class="boton-vidrio vidrio">Cancelar</a>
        </div>
    </form>
</div>
