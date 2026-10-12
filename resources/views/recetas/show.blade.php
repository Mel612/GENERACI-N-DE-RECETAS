@extends('layouts.app')

@section('titulo', $receta->nom_rec)

@section('contenido')
@php
    $tengo  = collect($comparacion)->filter(fn ($c) => $c['cubierto'])->count();
    $total  = $receta->ingredientes->where('opc_inr', false)->count();
    $compat = $total > 0
        ? round(100 * $receta->ingredientes->where('opc_inr', false)
            ->filter(fn ($i) => $comparacion[$i->cod_ingrediente_inr]['cubierto'] ?? false)->count() / $total, 1)
        : 0;
    $promedio = $receta->valoraciones->avg('pun_val');
@endphp

<a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('recetas.index') }}"
   class="mb-6 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver
</a>

@if ($receta->tieneImagen())
    {{-- La foto antes del titulo: es lo que la persona quiere ver primero --}}
    <x-foto :url="$receta->urlImagen()" :alt="$receta->nom_rec" :decorativa="false"
            class="mb-7 h-64 w-full rounded-2xl sm:h-80" />
@endif

<div class="mb-7 flex flex-wrap items-start justify-between gap-5">
    <div class="min-w-0">
        <h1 class="titulo-serif text-[clamp(2rem,4.5vw,3rem)]">{{ $receta->nom_rec }}</h1>
        <p class="mt-2 text-sm font-semibold text-oro-500">{{ $receta->categoria->nom_car }}</p>

        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-crema-300">
            <span>{{ $receta->tie_tot_rec }} min</span>
            <span aria-hidden="true">&middot;</span>
            <span>{{ trans_choice(':count porción|:count porciones', (int) $receta->por_rec) }}</span>
            <span aria-hidden="true">&middot;</span>
            <span>{{ $niveles[$receta->niv_rec] ?? $receta->niv_rec }}</span>

            {{-- El estado de validación se ve siempre, también cuando todavía falta --}}
            @if ($receta->ver_rec)
                <span class="sello-verificada sello-estampar"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada por un nutricionista</span>
            @else
                <span class="sello sello-lapiz sello-estampar"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zM20.7 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>Sin verificar por un nutricionista</span>
            @endif

            @if ($promedio)
                <span class="inline-flex items-center gap-1 text-oro-400">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                    {{ round($promedio, 1) }}
                    <span class="text-crema-500">({{ $receta->valoraciones->count() }})</span>
                </span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('recetas.favorita', $receta) }}">
        @csrf
        <button type="submit" @class(['boton-oro' => $esFavorita, 'boton-vidrio vidrio' => ! $esFavorita])>
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            {{ $esFavorita ? 'En tus favoritas' : 'Guardar en favoritas' }}
        </button>
    </form>
</div>

@if ($receta->des_rec)
    <p class="mb-7 max-w-3xl text-sm leading-relaxed text-crema-300">{{ $receta->des_rec }}</p>
@endif

@if ($receta->etiquetas->isNotEmpty())
    <div class="mb-7 flex flex-wrap gap-2">
        @foreach ($receta->etiquetas as $etiqueta)
            <span class="inline-flex items-center rounded-full border border-linea-fuerte px-3 py-1 text-xs font-medium text-crema-300">
                {{ $etiqueta->nom_eti }}
            </span>
        @endforeach
    </div>
@endif

{{-- Qué tienes de esta receta --}}
<div class="vidrio mb-7 rounded-2xl p-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-crema-100">Con tu despensa</p>
            <p class="mt-1 text-xs text-crema-500">
                Tienes {{ $tengo }} de {{ $total }} ingredientes necesarios
            </p>
        </div>
        <p class="titulo-serif text-4xl {{ $compat >= 100 ? 'text-exito-400' : 'text-oro-400' }}">
            {{ rtrim(rtrim(number_format($compat, 1), '0'), '.') }}%
        </p>
    </div>

    <span class="mt-4 block h-2 rounded-full bg-crema-100/6">
        <span class="block h-full rounded-r-[4px] {{ $compat >= 100 ? 'bg-exito-400' : 'bg-oro-500' }}"
              style="width: {{ $compat > 0 ? max(2, min(100, $compat)) : 0 }}%"></span>
    </span>
</div>

<div class="grid gap-5 lg:grid-cols-[22rem_1fr]">

    {{-- Ingredientes --}}
    <section class="vidrio h-fit rounded-2xl p-6">
        <h2 class="titulo-serif mb-4 text-xl">Ingredientes</h2>

        <ul class="space-y-3">
            @foreach ($receta->ingredientes->sortBy('ord_inr') as $fila)
                @php
                    $estado   = $comparacion[$fila->cod_ingrediente_inr] ?? ['cubierto' => false, 'en_despensa' => false];
                    $cubierto = $estado['cubierto'];
                @endphp

                <li class="flex items-start gap-3 border-b border-oro-500/8 pb-3 last:border-0">
                    <span @class([
                        'mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full',
                        'bg-exito-400/15 text-exito-400' => $cubierto,
                        'bg-crema-100/6 text-crema-500'       => ! $cubierto,
                    ])>
                        @if ($cubierto)
                            <svg viewBox="0 0 24 24" class="h-3 w-3" fill="currentColor" aria-hidden="true">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/>
                            </svg>
                            <span class="sr-only">Lo tienes</span>
                        @else
                            <svg viewBox="0 0 24 24" class="h-3 w-3" fill="currentColor" aria-hidden="true">
                                <path d="M19 13H5v-2h14v2z"/>
                            </svg>
                            <span class="sr-only">Te falta</span>
                        @endif
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline justify-between gap-2">
                            <span @class(['text-sm', 'text-crema-100' => $cubierto, 'text-crema-300' => ! $cubierto])>
                                {{ $fila->ingrediente->nom_ing }}
                            </span>
                            <span class="shrink-0 whitespace-nowrap text-sm tabular-nums text-oro-400">
                                {{ rtrim(rtrim(number_format((float) $fila->can_inr, 3, '.', ''), '0'), '.') }}
                                {{ $fila->unidad->abr_uni }}
                            </span>
                        </div>

                        <div class="mt-0.5 flex flex-wrap gap-2 text-xs text-crema-500">
                            @if ($fila->opc_inr) <span>opcional</span> @endif
                            @if ($fila->ingrediente->ale_ing) <span class="text-alerta-400">alérgeno</span> @endif
                            @if (! $cubierto && $estado['en_despensa'])
                                <span class="text-alerta-400">tienes, pero no alcanza</span>
                            @endif
                        </div>

                        @if ($fila->not_inr)
                            <p class="mt-1 text-xs text-crema-500">{{ $fila->not_inr }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($receta->cal_por_rec !== null)
            <div class="mt-5 border-t border-oro-500/10 pt-4">
                <p class="text-xs font-medium text-crema-300">Por porción</p>
                <p class="mt-1 text-sm text-crema-100">{{ (float) $receta->cal_por_rec }} kcal</p>
                <p class="mt-0.5 text-xs text-crema-500">
                    Proteínas {{ (float) $receta->pro_por_rec }} g &middot;
                    Carbohidratos {{ (float) $receta->car_por_rec }} g &middot;
                    Grasas {{ (float) $receta->gra_por_rec }} g
                </p>
            </div>
        @endif
    </section>

    {{-- Preparación --}}
    <section class="vidrio rounded-2xl p-6">
        <h2 class="titulo-serif mb-5 text-xl">Preparación</h2>

        <ol class="space-y-6">
            @foreach ($receta->pasos as $paso)
                <li class="flex gap-4">
                    <span class="titulo-serif w-8 shrink-0 text-2xl text-oro-500/70">{{ $paso->num_pas }}</span>

                    <div class="flex-1">
                        <p class="text-sm leading-relaxed text-crema-100">{{ $paso->ins_pas }}</p>

                        <div class="mt-1.5 flex flex-wrap gap-3 text-xs">
                            @if ($paso->tie_pas)
                                <span class="text-crema-500">{{ $paso->tie_pas }} min</span>
                            @endif
                            @if ($paso->cri_pas)
                                <span class="text-alerta-400">No te saltes este paso</span>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>
</div>

{{-- Valoración --}}
<section class="vidrio mt-5 rounded-2xl p-6">
    <h2 class="titulo-serif mb-4 text-xl">
        {{ $miValoracion ? 'Tu valoración' : '¿La preparaste? Cuéntanos qué tal' }}
    </h2>

    <form method="POST" action="{{ route('recetas.valorar', $receta) }}" class="space-y-4">
        @csrf

        <fieldset>
            <legend class="etiqueta-campo">Puntuación</legend>
            <div class="mt-1 flex gap-2">
                @for ($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="pun_val" value="{{ $i }}" class="peer sr-only"
                               @checked(old('pun_val', $miValoracion?->pun_val) == $i) required>
                        <span class="vidrio grid h-11 w-11 place-items-center rounded-xl text-sm transition
                                     peer-checked:bg-oro-500 peer-checked:text-masa-900
                                     peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-oro-400">
                            {{ $i }}<span class="sr-only"> de 5</span>
                        </span>
                    </label>
                @endfor
            </div>
            @error('pun_val') <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
        </fieldset>

        <div>
            <label for="com_val" class="etiqueta-campo">Comentario <span class="font-normal text-crema-500">(opcional)</span></label>
            <textarea id="com_val" name="com_val" rows="3" class="campo"
                      placeholder="Le puse más ají del que dice la receta y quedó mejor.">{{ old('com_val', $miValoracion?->com_val) }}</textarea>
            @error('com_val') <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="boton-oro">
            {{ $miValoracion ? 'Actualizar mi valoración' : 'Enviar valoración' }}
        </button>
    </form>

    @php $otras = $receta->valoraciones->where('cod_usuario_val', '!=', auth()->id())->where('com_val', '!=', null); @endphp

    @if ($otras->isNotEmpty())
        <div class="mt-8 space-y-4 border-t border-oro-500/10 pt-6">
            <h3 class="text-sm font-medium text-crema-100">Lo que dicen otras personas</h3>

            @foreach ($otras->take(5) as $valoracion)
                <div class="border-b border-oro-500/8 pb-4 last:border-0 last:pb-0">
                    <div class="flex flex-wrap items-center gap-2 text-xs text-crema-500">
                        <span>{{ $valoracion->usuario?->nom_usu ?? 'Usuario' }}</span>
                        <span aria-hidden="true">&middot;</span>
                        <span class="text-oro-400">{{ $valoracion->pun_val }}/5</span>
                        <span aria-hidden="true">&middot;</span>
                        <span>{{ $valoracion->fec_cre_val?->format('d/m/Y') }}</span>
                    </div>
                    <p class="mt-1.5 text-sm leading-relaxed text-crema-300">{{ $valoracion->com_val }}</p>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
