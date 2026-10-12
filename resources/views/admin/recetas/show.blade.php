@extends('layouts.app')

@section('titulo', $receta->nom_rec)

@section('contenido')
@php
    $tonos = ['pendiente' => 'text-crema-300', 'aprobada' => 'text-exito-400',
              'observada' => 'text-alerta-400', 'rechazada' => 'text-error-400'];
@endphp

<a href="{{ route('admin.recetas.index') }}"
   class="mb-6 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver a recetas
</a>

@if ($receta->tieneImagen())
    <x-foto :url="$receta->urlImagen()" :alt="$receta->nom_rec" :decorativa="false"
            class="mb-7 h-56 w-full rounded-2xl" />
@endif

<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="titulo-serif text-[clamp(1.9rem,4vw,2.9rem)]">{{ $receta->nom_rec }}</h1>
        <p class="mt-2 text-sm font-semibold text-oro-500">{{ $receta->categoria->nom_car }}</p>

        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
            <span class="{{ $tonos[$receta->est_val_rec] ?? 'text-crema-300' }}">
                {{ $estados[$receta->est_val_rec] ?? $receta->est_val_rec }}
            </span>

            @if ($receta->ver_rec)
                <span class="sello-verificada"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada</span>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.recetas.edit', $receta) }}" class="boton-oro">Editar receta</a>
</div>

@if ($receta->des_rec)
    <p class="mb-7 max-w-3xl text-sm leading-relaxed text-crema-300">{{ $receta->des_rec }}</p>
@endif

<div class="mb-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ficha titulo="Tiempo total" :valor="$receta->tie_tot_rec . ' min'"
             detalle="{{ $receta->tie_pre_rec }} de preparación, {{ $receta->tie_coc_rec }} de cocción" />
    <x-ficha titulo="Porciones" :valor="$receta->por_rec" tono="neutro" />
    <x-ficha titulo="Dificultad" :valor="$niveles[$receta->niv_rec] ?? $receta->niv_rec" tono="neutro" />
    <x-ficha titulo="Calorías por porción"
             :valor="$receta->cal_por_rec !== null ? (float) $receta->cal_por_rec . ' kcal' : 'Sin datos'"
             :tono="$receta->cal_por_rec !== null ? 'oro' : 'alerta'"
             :detalle="$receta->cal_por_rec !== null
                ? 'P ' . (float) $receta->pro_por_rec . ' · C ' . (float) $receta->car_por_rec . ' · G ' . (float) $receta->gra_por_rec
                : null" />
</div>

@if ($receta->etiquetas->isNotEmpty())
    <div class="mb-7 flex flex-wrap gap-2">
        @foreach ($receta->etiquetas as $etiqueta)
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
                  style="background-color: {{ $etiqueta->col_eti }}22; color: {{ $etiqueta->col_eti }};">
                <span class="h-2 w-2 rounded-full" style="background-color: {{ $etiqueta->col_eti }}"></span>
                {{ $etiqueta->nom_eti }}
            </span>
        @endforeach
    </div>
@endif

<div class="grid gap-5 lg:grid-cols-[22rem_1fr]">

    {{-- Ingredientes --}}
    <section class="vidrio h-fit rounded-2xl p-6">
        <h2 class="titulo-serif mb-4 text-xl">Ingredientes</h2>

        @if ($receta->ingredientes->isEmpty())
            <p class="rounded-xl border border-error-400/25 bg-error-400/10 px-4 py-3 text-sm text-error-400">
                Esta receta no tiene ingredientes. No podrá aparecer en ninguna búsqueda por compatibilidad.
            </p>
        @else
            <ul class="space-y-2.5">
                @foreach ($receta->ingredientes->sortBy('ord_inr') as $fila)
                    <li class="flex items-start justify-between gap-3 border-b border-oro-500/8 pb-2.5 last:border-0">
                        <div>
                            <span class="text-sm text-crema-100">{{ $fila->ingrediente->nom_ing }}</span>
                            @if ($fila->opc_inr)
                                <span class="ml-1.5 text-[0.62rem] text-crema-500">(opcional)</span>
                            @endif
                            @if ($fila->ingrediente->ale_ing)
                                <span class="ml-1.5 rounded-full bg-alerta-400/12 px-2 py-0.5 text-[0.62rem] text-alerta-400">Alérgeno</span>
                            @endif
                            @if ($fila->not_inr)
                                <p class="mt-0.5 text-xs text-crema-500">{{ $fila->not_inr }}</p>
                            @endif
                        </div>

                        <span class="shrink-0 whitespace-nowrap text-sm tabular-nums text-oro-400">
                            {{ rtrim(rtrim(number_format((float) $fila->can_inr, 3, '.', ''), '0'), '.') }}
                            {{ $fila->unidad->abr_uni }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Pasos --}}
    <section class="vidrio rounded-2xl p-6">
        <h2 class="titulo-serif mb-4 text-xl">Preparación</h2>

        @if ($receta->pasos->isEmpty())
            <p class="rounded-xl border border-error-400/25 bg-error-400/10 px-4 py-3 text-sm text-error-400">
                Esta receta no tiene pasos de preparación.
            </p>
        @else
            <ol class="space-y-5">
                @foreach ($receta->pasos as $paso)
                    <li class="flex gap-4">
                        <span class="titulo-serif w-8 shrink-0 text-2xl text-oro-500/70">{{ $paso->num_pas }}</span>

                        <div class="flex-1">
                            <p class="text-sm leading-relaxed text-crema-100">{{ $paso->ins_pas }}</p>

                            <div class="mt-1.5 flex flex-wrap gap-3 text-xs text-crema-500">
                                @if ($paso->tie_pas)
                                    <span>{{ $paso->tie_pas }} min</span>
                                @endif
                                @if ($paso->cri_pas)
                                    <span class="text-alerta-400">Paso crítico</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>
</div>

{{-- Historial de revisiones del nutricionista --}}
@if ($receta->validaciones->isNotEmpty())
    <section class="vidrio mt-5 rounded-2xl p-6">
        <h2 class="titulo-serif mb-4 text-xl">Revisiones del nutricionista</h2>

        <ol class="space-y-4">
            @foreach ($receta->validaciones as $validacion)
                <li class="border-b border-oro-500/8 pb-4 last:border-0 last:pb-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-sm {{ $tonos[$validacion->est_vre] ?? 'text-crema-300' }}">
                            {{ $estados[$validacion->est_vre] ?? $validacion->est_vre }}
                        </span>
                        <span class="text-xs text-crema-500">
                            {{ $validacion->nutricionista?->nom_usu }} &middot;
                            {{ $validacion->fec_rev_vre?->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    @if ($validacion->obs_vre)
                        <p class="mt-2 text-sm leading-relaxed text-crema-300">{{ $validacion->obs_vre }}</p>
                    @endif
                </li>
            @endforeach
        </ol>
    </section>
@endif

<p class="mt-6 text-xs text-crema-500">
    Creada el {{ $receta->fec_cre_rec?->format('d/m/Y H:i') }}
    @if ($receta->autor) por {{ $receta->autor->nom_usu }} @endif
    @if ($receta->fec_act_rec) &middot; última edición el {{ $receta->fec_act_rec->format('d/m/Y H:i') }} @endif
</p>
@endsection
