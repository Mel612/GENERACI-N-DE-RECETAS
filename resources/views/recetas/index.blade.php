@extends('layouts.app')

@section('titulo', 'Recetario')

@section('contenido')
<x-cabecera rotulo="Mi cocina" titulo="Recetario"
            :subtitulo="trans_choice(':count receta|:count recetas', $recetas->total()) . '. Busca por nombre o por ingrediente.'" />

<form method="GET" class="vidrio mb-6 space-y-4 rounded-2xl p-5">
    <div>
        <label for="buscar" class="etiqueta-campo">Buscar</label>
        <div class="flex flex-col gap-3 sm:flex-row">
            <input id="buscar" type="search" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Silpancho, quinua, sopa..." class="campo flex-1"
                   aria-describedby="ayuda-buscar">
            <div class="flex shrink-0 gap-3">
                <button type="submit" class="boton-oro">Buscar</button>
                @if (collect(request()->query())->filter()->isNotEmpty())
                    <a href="{{ route('recetas.index') }}" class="boton-vidrio vidrio">Limpiar</a>
                @endif
            </div>
        </div>
        <p id="ayuda-buscar" class="mt-1.5 text-xs text-crema-500">
            También encuentra recetas por sus ingredientes: escribe «quinua» y verás todo lo que la lleva.
        </p>
    </div>

    <x-filtros-receta :categorias="$categorias" :etiquetas="$etiquetas" :niveles="$niveles"
                      :valores="request()->all()" />

    {{--
        Aquí las preferencias no se imponen: en el recetario la persona está
        mirando qué existe, no decidiendo qué cenar. En el generador sí van
        marcadas por omisión.
    --}}
    @if ($dieta['aplicables']->isNotEmpty())
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-oro-500/10 pt-4">
            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-crema-300">
                <input type="checkbox" name="segun_mi_dieta" value="1" @checked($segunDieta)
                       class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                Solo lo que va con mis preferencias
                <span class="text-xs text-crema-500">
                    ({{ $dieta['aplicables']->pluck('preferencia')->implode(', ') }})
                </span>
            </label>

            @if ($dieta['sin_soporte']->isNotEmpty())
                <p class="text-xs text-alerta-400">
                    {{ $dieta['sin_soporte']->implode(', ') }}: todavía no hay recetas etiquetadas así,
                    así que no entra en el filtro.
                </p>
            @endif
        </div>
    @endif
</form>

@if ($recetas->isEmpty())
    <div class="vidrio rounded-2xl px-6 py-14 text-center">
        <p class="text-crema-300">No se encontró ninguna receta con esos criterios.</p>
        <a href="{{ route('recetas.index') }}" class="boton-vidrio vidrio mt-5">Ver todas</a>
    </div>
@else
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($recetas as $receta)
            <x-tarjeta-simple :receta="$receta" :niveles="$niveles"
                              :esFavorita="$favoritas->contains($receta->cod_receta)" />
        @endforeach
    </div>

    <div class="mt-6">{{ $recetas->links() }}</div>
@endif
@endsection
