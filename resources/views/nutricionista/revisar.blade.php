@extends('layouts.app')

@section('titulo', 'Revisar: ' . $receta->nom_rec)

@section('contenido')
@php
    $tonos = ['pendiente' => 'text-crema-300', 'aprobada' => 'text-exito-400',
              'observada' => 'text-alerta-400', 'rechazada' => 'text-error-400'];

    $nombres = ['pendiente' => 'Pendiente de revisión', 'aprobada' => 'Aprobada',
                'observada' => 'Devuelta con observaciones', 'rechazada' => 'Rechazada'];

    $niveles = ['facil' => 'Fácil', 'medio' => 'Medio', 'dificil' => 'Difícil'];

    // Avisos: el color nunca carga el significado solo, cada uno lleva su icono.
    $estilosAviso = [
        'error'  => ['border-error-400/25 bg-error-400/10 text-error-400',
                     'M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'],
        'alerta' => ['border-alerta-400/25 bg-alerta-400/10 text-alerta-400',
                     'M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'],
        'aviso'  => ['border-oro-500/20 bg-oro-500/8 text-oro-300',
                     'M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'],
    ];

    $calculado = $calculo['por_porcion'];

    // Diferencia entre lo declarado y lo calculado desde los ingredientes.
    $declarado = $receta->cal_por_rec !== null ? (float) $receta->cal_por_rec : null;
    $brecha = ($declarado !== null && $calculado['cal'] > 0)
        ? abs($declarado - $calculado['cal']) / max(1, $calculado['cal']) * 100
        : null;
@endphp

<a href="{{ route('nutricionista.index') }}"
   class="mb-6 inline-flex items-center gap-2 text-sm text-crema-300 transition hover:text-oro-400">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Volver a la bandeja
</a>

<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="titulo-serif text-[clamp(1.9rem,4vw,2.9rem)]">{{ $receta->nom_rec }}</h1>
        <p class="mt-2 text-sm font-semibold text-oro-500">{{ $receta->categoria->nom_car }}</p>

        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
            <span class="{{ $tonos[$receta->est_val_rec] ?? 'text-crema-300' }}">
                {{ $nombres[$receta->est_val_rec] ?? $receta->est_val_rec }}
            </span>

            @if ($receta->ver_rec)
                <span class="sello-verificada"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada</span>
            @endif

            <span class="text-crema-500">
                {{ $receta->tie_tot_rec }} min &middot;
                {{ $receta->por_rec }} porciones &middot;
                {{ $niveles[$receta->niv_rec] ?? $receta->niv_rec }}
            </span>
        </div>
    </div>

    <a href="{{ route('admin.recetas.edit', $receta) }}" class="boton-vidrio vidrio">
        Editar la receta
    </a>
</div>

@if ($receta->des_rec)
    <p class="mb-7 max-w-3xl text-sm leading-relaxed text-crema-300">{{ $receta->des_rec }}</p>
@endif

{{-- Lo que el sistema detectó solo. No decide: señala dónde mirar. --}}
@if (! empty($avisos))
    <section class="mb-7 space-y-2.5" aria-label="Avisos automáticos de la revisión">
        @foreach ($avisos as [$tono, $texto])
            @php [$clases, $icono] = $estilosAviso[$tono] ?? $estilosAviso['aviso']; @endphp
            <div class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {{ $clases }}">
                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                    <path d="{{ $icono }}"/>
                </svg>
                <span class="leading-relaxed">{{ $texto }}</span>
            </div>
        @endforeach
    </section>
@endif

<div class="grid gap-5 lg:grid-cols-[1fr_24rem]">

    {{--
        ------------------------------------------------ Contenido de la receta

        min-w-0 no es adorno: sin eso la tabla de ingredientes (min-w-full)
        estira su columna de la cuadrícula y en el celular la página entera
        se va de lado. Medido: 559 px de ancho en una pantalla de 390.
    --}}
    <div class="min-w-0 space-y-5">

        {{-- Ingredientes, con sus valores nutricionales a la vista --}}
        <section class="vidrio overflow-hidden rounded-2xl">
            <h2 class="titulo-serif border-b border-oro-500/12 px-6 py-4 text-xl">
                Ingredientes
                <span class="ml-1 text-sm font-normal text-crema-500">({{ $receta->ingredientes->count() }})</span>
            </h2>

            @if ($receta->ingredientes->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-error-400">
                    La receta no tiene ingredientes. No hay nada que validar todavía.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <caption class="sr-only">Ingredientes de la receta con sus valores por 100 g</caption>
                        <thead>
                            <tr class="border-b border-oro-500/12 text-left text-xs uppercase tracking-wide text-crema-500">
                                <th scope="col" class="px-6 py-3 font-medium">Ingrediente</th>
                                <th scope="col" class="px-3 py-3 font-medium">Cantidad</th>
                                <th scope="col" class="px-3 py-3 text-right font-medium">kcal/100g</th>
                                <th scope="col" class="px-3 py-3 text-right font-medium">P</th>
                                <th scope="col" class="px-3 py-3 text-right font-medium">C</th>
                                <th scope="col" class="px-6 py-3 text-right font-medium">G</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($receta->ingredientes->sortBy('ord_inr') as $fila)
                                @php $ing = $fila->ingrediente; @endphp
                                <tr class="border-b border-oro-500/8 last:border-0">
                                    <td class="px-6 py-3">
                                        <span class="text-crema-100">{{ $ing->nom_ing }}</span>

                                        @if ($fila->opc_inr)
                                            <span class="ml-1.5 text-[0.62rem] text-crema-500">(opcional)</span>
                                        @endif

                                        @if ($ing->ale_ing)
                                            <span class="ml-1.5 rounded-full bg-alerta-400/12 px-2 py-0.5 text-[0.62rem] text-alerta-400">
                                                Alérgeno
                                            </span>
                                        @endif

                                        @if ($fila->not_inr)
                                            <p class="mt-0.5 text-xs text-crema-500">{{ $fila->not_inr }}</p>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 tabular-nums text-oro-400">
                                        {{ rtrim(rtrim(number_format((float) $fila->can_inr, 3, '.', ''), '0'), '.') }}
                                        {{ $fila->unidad->abr_uni }}
                                    </td>

                                    @if ($ing->cal_ing === null)
                                        <td colspan="4" class="px-6 py-3 text-right text-xs text-alerta-400">
                                            Sin datos nutricionales
                                        </td>
                                    @else
                                        <td class="px-3 py-3 text-right tabular-nums text-crema-300">{{ (float) $ing->cal_ing }}</td>
                                        <td class="px-3 py-3 text-right tabular-nums text-crema-500">{{ (float) $ing->pro_ing }}</td>
                                        <td class="px-3 py-3 text-right tabular-nums text-crema-500">{{ (float) $ing->car_ing }}</td>
                                        <td class="px-6 py-3 text-right tabular-nums text-crema-500">{{ (float) $ing->gra_ing }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="border-t border-oro-500/8 px-6 py-3 text-xs text-crema-500">
                    P proteínas, C carbohidratos, G grasas, en gramos por 100 g de ingrediente.
                    Los valores se editan en
                    <a href="{{ route('admin.ingredientes.index') }}" class="text-oro-400 hover:underline">el catálogo de ingredientes</a>.
                </p>
            @endif
        </section>

        {{-- Pasos --}}
        <section class="vidrio rounded-2xl p-6">
            <h2 class="titulo-serif mb-4 text-xl">
                Preparación
                <span class="ml-1 text-sm font-normal text-crema-500">({{ $receta->pasos->count() }} pasos)</span>
            </h2>

            @if ($receta->pasos->isEmpty())
                <p class="rounded-xl border border-error-400/25 bg-error-400/10 px-4 py-3 text-sm text-error-400">
                    La receta no tiene pasos de preparación.
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

        {{-- Historial: quién dijo qué y cuándo --}}
        @if ($receta->validaciones->isNotEmpty())
            <section class="vidrio rounded-2xl p-6">
                <h2 class="titulo-serif mb-4 text-xl">Revisiones anteriores</h2>

                <ol class="space-y-4">
                    @foreach ($receta->validaciones as $validacion)
                        <li class="border-b border-oro-500/8 pb-4 last:border-0 last:pb-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-sm {{ $tonos[$validacion->est_vre] ?? 'text-crema-300' }}">
                                    {{ $nombres[$validacion->est_vre] ?? $validacion->est_vre }}
                                </span>
                                <span class="text-xs text-crema-500">
                                    {{ $validacion->nutricionista?->nom_usu }} &middot;
                                    {{ $validacion->fec_rev_vre?->format('d/m/Y H:i') }}
                                </span>
                            </div>

                            <div class="mt-1.5 flex flex-wrap gap-3 text-[0.68rem] text-crema-500">
                                <span>Ingredientes: {{ $validacion->val_ing_vre ? 'revisados' : 'sin revisar' }}</span>
                                <span>Pasos: {{ $validacion->val_pas_vre ? 'revisados' : 'sin revisar' }}</span>
                                <span>Nutrición: {{ $validacion->val_nut_vre ? 'revisada' : 'sin revisar' }}</span>
                            </div>

                            @if ($validacion->obs_vre)
                                <p class="mt-2 text-sm leading-relaxed text-crema-300">{{ $validacion->obs_vre }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif
    </div>

    {{-- ------------------------------------------------------ Panel de dictamen --}}
    <form method="POST" action="{{ route('nutricionista.dictaminar', $receta) }}"
          class="space-y-5 lg:sticky lg:top-24 lg:h-fit">
        @csrf

        {{-- Información nutricional (Historia 11) --}}
        <section class="vidrio rounded-2xl p-6">
            <h2 class="titulo-serif mb-1 text-xl">Información nutricional</h2>
            <p class="mb-5 text-xs leading-relaxed text-crema-500">
                Por porción. La receta rinde {{ $receta->por_rec }} porciones.
            </p>

            {{-- Lo que sale de los ingredientes, para contrastar --}}
            <div class="mb-5 rounded-xl border border-oro-500/15 bg-oro-500/5 p-4">
                <p class="mb-3 text-xs uppercase tracking-wide text-crema-500">Calculado desde los ingredientes</p>

                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-xs text-crema-500">Calorías</dt>
                        <dd class="tabular-nums text-oro-400">{{ $calculado['cal'] }} kcal</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-crema-500">Proteínas</dt>
                        <dd class="tabular-nums text-crema-100">{{ $calculado['pro'] }} g</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-crema-500">Carbohidratos</dt>
                        <dd class="tabular-nums text-crema-100">{{ $calculado['car'] }} g</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-crema-500">Grasas</dt>
                        <dd class="tabular-nums text-crema-100">{{ $calculado['gra'] }} g</dd>
                    </div>
                </dl>

                {{--
                    Honestidad sobre el número. Es una referencia, no un dato
                    definitivo, y conviene que se vea por qué:
                      - lo medido en piezas no se puede llevar a gramos, así
                        que queda fuera y el total sale corto;
                      - lo que se cuenta entra completo, aunque no se consuma
                        completo (el aceite de freír), así que sale largo.
                    Por eso el valor final lo decide el nutricionista.
                --}}
                <p class="mt-3 text-[0.68rem] leading-relaxed text-crema-500">
                    Calculado con {{ $calculo['contados'] }} de {{ $calculo['total'] }} ingredientes.
                    @if (! empty($calculo['ignorados']))
                        <span class="text-alerta-400">
                            Quedaron fuera: {{ implode(', ', $calculo['ignorados']) }}.
                        </span>
                    @endif
                    Es una referencia: suma cada ingrediente entero, así que en
                    frituras o cocciones largas queda por encima de lo que
                    realmente se consume. El valor que se publica lo decides tú.
                </p>

                @if ($brecha !== null && $brecha > 20)
                    <p class="mt-3 rounded-lg bg-alerta-400/10 px-3 py-2 text-[0.68rem] leading-relaxed text-alerta-400">
                        Lo declarado ({{ $declarado }} kcal) se aparta
                        {{ round($brecha) }}% del cálculo. Vale la pena revisarlo.
                    </p>
                @endif

                <button type="button" id="copiar-calculo" hidden
                        class="boton-vidrio mt-3 w-full text-xs"
                        data-cal="{{ $calculado['cal'] }}" data-pro="{{ $calculado['pro'] }}"
                        data-car="{{ $calculado['car'] }}" data-gra="{{ $calculado['gra'] }}">
                    Usar estos valores
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <x-campo nombre="cal_por_rec" etiqueta="Calorías (kcal)" tipo="number" step="0.01" min="0"
                         :valor="$receta->cal_por_rec !== null ? (float) $receta->cal_por_rec : null" />
                <x-campo nombre="pro_por_rec" etiqueta="Proteínas (g)" tipo="number" step="0.01" min="0"
                         :valor="$receta->pro_por_rec !== null ? (float) $receta->pro_por_rec : null" />
                <x-campo nombre="car_por_rec" etiqueta="Carbohidratos (g)" tipo="number" step="0.01" min="0"
                         :valor="$receta->car_por_rec !== null ? (float) $receta->car_por_rec : null" />
                <x-campo nombre="gra_por_rec" etiqueta="Grasas (g)" tipo="number" step="0.01" min="0"
                         :valor="$receta->gra_por_rec !== null ? (float) $receta->gra_por_rec : null" />
            </div>

            <div class="mt-4">
                <label for="obs_nut_rec" class="etiqueta-campo">Nota nutricional</label>
                <textarea id="obs_nut_rec" name="obs_nut_rec" rows="3"
                          placeholder="Ej.: aporte alto en fibra; se puede reducir la sal sin afectar el sabor."
                          @class(['campo', 'campo-error' => $errors->has('obs_nut_rec')])>{{ old('obs_nut_rec', $receta->obs_nut_rec) }}</textarea>
                <p class="mt-1.5 text-xs text-crema-500">La ve quien consulta la receta.</p>
            </div>
        </section>

        {{-- Dictamen (Historias 12 y 13) --}}
        <section class="vidrio rounded-2xl p-6">
            <h2 class="titulo-serif mb-5 text-xl">Dictamen</h2>

            <fieldset class="space-y-2.5">
                <legend class="etiqueta-campo">Qué decides <span class="text-oro-400" aria-hidden="true">*</span></legend>

                @foreach ($estados as $clave => $texto)
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-oro-500/12 px-4 py-3 text-sm transition hover:border-oro-500/30 hover:bg-oro-500/5">
                        <input type="radio" name="est_vre" value="{{ $clave }}" required
                               @checked(old('est_vre') === $clave)
                               class="mt-0.5 h-4 w-4 border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                        <span>
                            <span class="block text-crema-100">{{ $texto }}</span>
                            <span class="block text-xs text-crema-500">
                                @switch($clave)
                                    @case('aprobada') La receta queda apta para publicarse. @break
                                    @case('observada') Vuelve a quien la escribió con tus correcciones. @break
                                    @case('rechazada') No se publica. @break
                                @endswitch
                            </span>
                        </span>
                    </label>
                @endforeach
            </fieldset>

            <div class="mt-4">
                <label for="obs_vre" class="etiqueta-campo">Observaciones</label>
                <textarea id="obs_vre" name="obs_vre" rows="4"
                          placeholder="Qué revisaste y qué hay que corregir."
                          @class(['campo', 'campo-error' => $errors->has('obs_vre')])>{{ old('obs_vre') }}</textarea>

                @error('obs_vre')
                    <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p>
                @else
                    <p class="mt-1.5 text-xs text-crema-500">
                        Obligatorias si devuelves con observaciones o rechazas.
                    </p>
                @enderror
            </div>

            {{-- Qué alcanzó a revisar: queda en el historial --}}
            <fieldset class="mt-5 space-y-2.5 border-t border-oro-500/10 pt-5">
                <legend class="etiqueta-campo">Qué revisaste</legend>

                <x-interruptor nombre="val_ing_vre" etiqueta="Ingredientes y cantidades" :valor="false" />
                <x-interruptor nombre="val_pas_vre" etiqueta="Pasos de preparación" :valor="false" />
                <x-interruptor nombre="val_nut_vre" etiqueta="Información nutricional" :valor="false" />
            </fieldset>

            {{-- Verificar: solo sobre aprobada, y la base lo impone con un CHECK --}}
            <div class="mt-5 border-t border-oro-500/10 pt-5">
                <x-interruptor nombre="verificar"
                               etiqueta="Marcar como verificada"
                               :valor="$receta->ver_rec"
                               ayuda="Solo si la apruebas. La receta lleva el distintivo «Verificada» y aparece en el filtro de recetas verificadas." />

                @error('verificar')
                    <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="boton-oro">Guardar dictamen</button>
                <a href="{{ route('nutricionista.index') }}" class="boton-vidrio vidrio">Cancelar</a>
            </div>
        </section>
    </form>
</div>

<p class="mt-6 text-xs text-crema-500">
    Creada el {{ $receta->fec_cre_rec?->format('d/m/Y H:i') }}
    @if ($receta->autor) por {{ $receta->autor->nom_usu }} @endif
    @if ($receta->fec_act_rec) &middot; última edición el {{ $receta->fec_act_rec->format('d/m/Y H:i') }} @endif
</p>

{{-- El botón de copiar el cálculo nace oculto: si no hay JS, nunca aparece. --}}
<script>
    (function () {
        const boton = document.getElementById('copiar-calculo');
        if (! boton) return;

        boton.hidden = false;

        boton.addEventListener('click', function () {
            for (const clave of ['cal', 'pro', 'car', 'gra']) {
                const campo = document.getElementById(clave + '_por_rec');
                if (campo) campo.value = boton.dataset[clave];
            }
        });
    })();
</script>
@endsection
