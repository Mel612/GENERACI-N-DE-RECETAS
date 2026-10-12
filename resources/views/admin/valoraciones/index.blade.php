@extends('layouts.app')

@section('titulo', 'Valoraciones')

@section('contenido')
<x-cabecera titulo="Moderación de valoraciones"
            subtitulo="Puntuaciones y comentarios que los usuarios dejaron en las recetas." />

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ficha titulo="Valoraciones" :valor="$resumen['total']" />
    <x-ficha titulo="Promedio general" :valor="$resumen['promedio']" detalle="sobre 5" />
    <x-ficha titulo="Con comentario" :valor="$resumen['conComentario']" tono="neutro" />
    <x-ficha titulo="Negativas (1 o 2 estrellas)" :valor="$resumen['negativas']"
             :tono="$resumen['negativas'] > 0 ? 'alerta' : 'exito'" />
</div>

<div class="mb-6">
    <x-grafico-barras titulo="Distribución de puntuaciones" :datos="$distribucion" unidad="valoraciones"
                      vacio="Todavía no hay valoraciones." />
</div>

<x-buscador :limpiar="route('admin.valoraciones.index')" marcador="Buscar en comentarios, recetas o usuarios">
    <label class="sr-only" for="puntaje">Puntaje</label>
    <select id="puntaje" name="puntaje" class="campo max-w-[11rem]">
        <option value="">Todos los puntajes</option>
        @foreach ([5, 4, 3, 2, 1] as $p)
            <option value="{{ $p }}" @selected(request('puntaje') == $p)>
                {{ $p }} {{ $p === 1 ? 'estrella' : 'estrellas' }}
            </option>
        @endforeach
    </select>

    <label class="flex items-center gap-2 text-sm text-crema-300">
        <input type="checkbox" name="con_comentario" value="1" @checked(request()->boolean('con_comentario'))
               class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
        Solo con comentario
    </label>
</x-buscador>

@if ($valoraciones->isEmpty())
    <div class="vidrio rounded-2xl px-6 py-14 text-center text-crema-500">
        No se encontraron valoraciones con esos criterios.
    </div>
@else
    <div class="space-y-4">
        @foreach ($valoraciones as $valoracion)
            <article class="vidrio rounded-2xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="font-medium text-crema-100">{{ $valoracion->receta?->nom_rec ?? 'Receta eliminada' }}</h2>
                        <p class="mt-1 text-xs text-crema-500">
                            {{ $valoracion->usuario?->nom_usu ?? 'Cuenta eliminada' }}
                            &middot; {{ $valoracion->fec_cre_val?->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    {{-- La puntuación se escribe además de dibujarse --}}
                    <div class="flex items-center gap-2">
                        <span class="flex gap-0.5" aria-hidden="true">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg viewBox="0 0 24 24" class="h-4 w-4 {{ $i <= $valoracion->pun_val ? 'text-oro-400' : 'text-crema-100/20' }}"
                                     fill="currentColor">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            @endfor
                        </span>
                        <span class="text-xs tabular-nums text-crema-300">{{ $valoracion->pun_val }}/5</span>
                    </div>
                </div>

                @if ($valoracion->com_val)
                    <blockquote class="mt-4 border-l-2 border-oro-500/30 pl-4 text-sm leading-relaxed text-crema-300">
                        {{ $valoracion->com_val }}
                    </blockquote>
                @else
                    <p class="mt-4 text-xs italic text-crema-500">Sin comentario.</p>
                @endif

                <div class="mt-5 flex flex-wrap gap-2 border-t border-oro-500/10 pt-4">
                    @if ($valoracion->com_val)
                        <form method="POST" action="{{ route('admin.valoraciones.ocultar', $valoracion) }}"
                              data-confirmar-titulo="¿Eliminar el comentario?"
                              data-confirmar="Se borra solo el texto del comentario. La puntuación con estrellas se conserva."
                              data-confirmar-boton="Sí, eliminar comentario">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="rounded-full px-3.5 py-1.5 text-xs text-alerta-400 transition hover:bg-alerta-400/12">
                                Eliminar comentario
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.valoraciones.destroy', $valoracion) }}"
                          data-confirmar="Vas a eliminar la valoración completa: el comentario y la puntuación. No se puede deshacer.">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-full px-3.5 py-1.5 text-xs text-error-400 transition hover:bg-error-400/12">
                            Eliminar valoración
                        </button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
@endif

<div class="mt-6">{{ $valoraciones->links() }}</div>
@endsection
