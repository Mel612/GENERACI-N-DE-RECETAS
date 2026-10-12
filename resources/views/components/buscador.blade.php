{{-- Barra de busqueda y filtros. Los filtros extra van en la ranura. --}}
@props(['limpiar', 'marcador' => 'Buscar...'])

<form method="GET" class="vidrio mb-6 flex flex-wrap items-center gap-3 rounded-2xl p-4">
    <label class="sr-only" for="buscar">Buscar</label>
    <input id="buscar" type="search" name="buscar" value="{{ request('buscar') }}"
           placeholder="{{ $marcador }}" class="campo max-w-xs">

    {{ $slot }}

    <button type="submit" class="boton-vidrio vidrio">Filtrar</button>

    @if (collect(request()->query())->filter()->isNotEmpty())
        <a href="{{ $limpiar }}" class="boton-vidrio">Limpiar</a>
    @endif
</form>
