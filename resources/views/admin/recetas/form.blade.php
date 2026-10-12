@extends('layouts.app')

@php
    $editando = $receta->exists;

    // Lo que ya está cargado, o una fila vacía si es una receta nueva.
    $ingredientesActuales = old('ingredientes', $editando
        ? $receta->ingredientes->map(fn ($i) => [
            'ingrediente' => $i->cod_ingrediente_inr,
            'unidad'      => $i->cod_unidad_inr,
            'cantidad'    => (float) $i->can_inr,
            'nota'        => $i->not_inr,
            'opcional'    => $i->opc_inr ? '1' : null,
        ])->all()
        : [['ingrediente' => '', 'unidad' => '', 'cantidad' => '', 'nota' => '', 'opcional' => null]]);

    $pasosActuales = old('pasos', $editando
        ? $receta->pasos->sortBy('num_pas')->map(fn ($p) => [
            'instruccion' => $p->ins_pas,
            'tiempo'      => $p->tie_pas,
            'critico'     => $p->cri_pas ? '1' : null,
        ])->values()->all()
        : [['instruccion' => '', 'tiempo' => '', 'critico' => null]]);

    $etiquetasElegidas = old('etiquetas', $editando ? $receta->etiquetas->pluck('cod_etiqueta')->all() : []);

    // Datos que necesita el cálculo nutricional asistido del navegador.
    $datosNutricion = [
        'ingredientes' => $ingredientes->mapWithKeys(fn ($i) => [$i->cod_ingrediente => [
            'nombre' => $i->nom_ing,
            'cal' => $i->cal_ing !== null ? (float) $i->cal_ing : null,
            'pro' => (float) $i->pro_ing,
            'car' => (float) $i->car_ing,
            'gra' => (float) $i->gra_ing,
        ]]),
        'unidades' => $unidades->mapWithKeys(fn ($u) => [$u->cod_unidad => [
            'tipo'   => $u->tip_uni,
            'factor' => (float) $u->fac_uni,
        ]]),
    ];
@endphp

@section('titulo', $editando ? 'Editar receta' : 'Nueva receta')

@section('contenido')
<x-cabecera rotulo="Recetas"
            :titulo="$editando ? 'Editar receta' : 'Nueva receta'"
            :subtitulo="$editando ? $receta->nom_rec : 'Los ingredientes y los pasos son obligatorios.'" />

@if ($ingredientes->isEmpty())
    <div class="vidrio rounded-2xl p-8 text-center">
        <svg viewBox="0 0 24 24" class="mx-auto h-10 w-10 text-alerta-400" fill="currentColor" aria-hidden="true">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>

        <h2 class="titulo-serif mt-4 text-2xl">El catálogo de ingredientes está vacío</h2>

        <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-crema-300">
            Una receta necesita al menos un ingrediente, y todavía no hay ninguno registrado.
            Carga primero el catálogo y vuelve aquí.
        </p>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('admin.ingredientes.create') }}" class="boton-oro">Registrar un ingrediente</a>
            <a href="{{ route('admin.recetas.index') }}" class="boton-vidrio vidrio">Volver</a>
        </div>

        <p class="mt-6 text-xs text-crema-500">
            Para cargar el catálogo completo de una vez, ejecuta
            <code class="rounded bg-crema-100/6 px-1.5 py-0.5">php artisan db:seed --class=IngredienteSeeder</code>
        </p>
    </div>
@else
{{-- Sin enctype el archivo no llega al servidor y no hay error que lo delate --}}
<form method="POST"
      enctype="multipart/form-data"
      action="{{ $editando ? route('admin.recetas.update', $receta) : route('admin.recetas.store') }}"
      class="space-y-6"
      id="formulario-receta"
      data-nutricion="{{ json_encode($datosNutricion, JSON_UNESCAPED_UNICODE) }}">
    @csrf
    @if ($editando) @method('PUT') @endif

    {{-- ═══════════════ Datos generales ═══════════════ --}}
    <section class="vidrio space-y-5 rounded-2xl p-7">
        <h2 class="titulo-serif text-xl">Datos generales</h2>

        <x-campo nombre="nom_rec" etiqueta="Nombre de la receta" :valor="$receta->nom_rec" requerido
                 placeholder="Silpancho paceño" />

        <div>
            <label for="des_rec" class="etiqueta-campo">Descripción</label>
            <textarea id="des_rec" name="des_rec" rows="3"
                      placeholder="Plato tradicional de Cochabamba con carne apanada, arroz, papa y huevo."
                      @class(['campo', 'campo-error' => $errors->has('des_rec')])>{{ old('des_rec', $receta->des_rec) }}</textarea>
            @error('des_rec') <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-selector nombre="cod_categoria_receta_rec" etiqueta="Categoría"
                        :opciones="$categorias" clave="cod_categoria_receta" texto="nom_car"
                        :valor="$receta->cod_categoria_receta_rec" requerido vacio="Elige una categoría" />

            <x-selector nombre="niv_rec" etiqueta="Dificultad"
                        :opciones="collect($niveles)->map(fn ($n, $c) => ['clave' => $c, 'nombre' => $n])->values()"
                        clave="clave" texto="nombre"
                        :valor="$receta->niv_rec ?? 'medio'" requerido />
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
            <x-campo nombre="tie_pre_rec" etiqueta="Preparación (min)" tipo="number" min="0" max="1440"
                     :valor="$receta->tie_pre_rec ?? 15" requerido />
            <x-campo nombre="tie_coc_rec" etiqueta="Cocción (min)" tipo="number" min="0" max="1440"
                     :valor="$receta->tie_coc_rec ?? 0" requerido />
            <x-campo nombre="por_rec" etiqueta="Porciones" tipo="number" min="1" max="100"
                     :valor="$receta->por_rec ?? 4" requerido />
        </div>

        <p class="-mt-2 text-xs text-crema-500">
            El tiempo total lo calcula la base de datos sumando preparación y cocción.
        </p>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-selector nombre="ori_rec" etiqueta="Origen"
                        :opciones="collect($origenes)->map(fn ($n, $c) => ['clave' => $c, 'nombre' => $n])->values()"
                        clave="clave" texto="nombre"
                        :valor="$receta->ori_rec ?? 'sistema'" requerido />

        </div>

        <div class="mt-5">
            <x-campo-imagen nombre="img_rec" etiqueta="Fotografía del plato"
                            :url="$receta->urlImagen()" />
        </div>
    </section>

    {{-- ═══════════════ Ingredientes ═══════════════ --}}
    <section class="vidrio rounded-2xl p-7">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="titulo-serif text-xl">Ingredientes</h2>
                <p class="mt-1 text-xs text-crema-500">
                    Un ingrediente no puede repetirse. Los marcados como opcionales no
                    cuentan para el porcentaje de compatibilidad.
                </p>
            </div>

            <button type="button" data-agregar="ingrediente" class="boton-vidrio vidrio text-sm">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Agregar ingrediente
            </button>
        </div>

        <div id="lista-ingredientes" class="space-y-3">
            @foreach ($ingredientesActuales as $i => $fila)
                <div class="fila-ingrediente grid items-start gap-3 rounded-xl border border-oro-500/10 p-4
                            sm:grid-cols-[1fr_7rem_9rem_auto]">

                    <div>
                        <label class="sr-only" for="ing-{{ $i }}">Ingrediente</label>
                        <select id="ing-{{ $i }}" name="ingredientes[{{ $i }}][ingrediente]" required
                                @class(['campo', 'campo-error' => $errors->has("ingredientes.$i.ingrediente")])>
                            <option value="">Elige un ingrediente</option>
                            @foreach ($ingredientes as $ing)
                                <option value="{{ $ing->cod_ingrediente }}" @selected(($fila['ingrediente'] ?? '') == $ing->cod_ingrediente)>
                                    {{ $ing->nom_ing }} &middot; {{ $ing->categoria->nom_cat }}
                                </option>
                            @endforeach
                        </select>
                        @error("ingredientes.$i.ingrediente") <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="sr-only" for="can-{{ $i }}">Cantidad</label>
                        <input id="can-{{ $i }}" type="number" step="0.001" min="0.001" required
                               name="ingredientes[{{ $i }}][cantidad]" value="{{ $fila['cantidad'] ?? '' }}"
                               placeholder="500"
                               @class(['campo', 'campo-error' => $errors->has("ingredientes.$i.cantidad")])>
                        @error("ingredientes.$i.cantidad") <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="sr-only" for="uni-{{ $i }}">Unidad</label>
                        <select id="uni-{{ $i }}" name="ingredientes[{{ $i }}][unidad]" required
                                @class(['campo', 'campo-error' => $errors->has("ingredientes.$i.unidad")])>
                            <option value="">Unidad</option>
                            @foreach ($unidades->groupBy('tip_uni') as $tipo => $grupo)
                                <optgroup label="{{ ucfirst($tipo) }}">
                                    @foreach ($grupo as $uni)
                                        <option value="{{ $uni->cod_unidad }}" @selected(($fila['unidad'] ?? '') == $uni->cod_unidad)>
                                            {{ $uni->nom_uni }} ({{ $uni->abr_uni }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error("ingredientes.$i.unidad") <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p> @enderror
                    </div>

                    <button type="button" data-quitar="ingrediente"
                            class="mt-1 rounded-full px-3 py-2 text-xs text-error-400 transition hover:bg-error-400/12"
                            aria-label="Quitar este ingrediente">
                        Quitar
                    </button>

                    <div class="sm:col-span-4">
                        <div class="flex flex-wrap items-center gap-4">
                            <input type="text" name="ingredientes[{{ $i }}][nota]" value="{{ $fila['nota'] ?? '' }}"
                                   placeholder="Nota: picado fino, sin semillas..." class="campo max-w-sm text-xs">

                            <label class="flex cursor-pointer items-center gap-2 text-xs text-crema-300">
                                <input type="checkbox" name="ingredientes[{{ $i }}][opcional]" value="1"
                                       @checked($fila['opcional'] ?? false)
                                       class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                                Opcional
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @error('ingredientes') <p class="mt-3 text-sm text-error-400">{{ $message }}</p> @enderror
    </section>

    {{-- ═══════════════ Pasos ═══════════════ --}}
    <section class="vidrio rounded-2xl p-7">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="titulo-serif text-xl">Pasos de preparación</h2>
                <p class="mt-1 text-xs text-crema-500">
                    Se numeran solos en el orden en que aparecen aquí.
                </p>
            </div>

            <button type="button" data-agregar="paso" class="boton-vidrio vidrio text-sm">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Agregar paso
            </button>
        </div>

        <div id="lista-pasos" class="space-y-3">
            @foreach ($pasosActuales as $i => $paso)
                <div class="fila-paso rounded-xl border border-oro-500/10 p-4">
                    <div class="flex items-start gap-3">
                        <span class="numero-paso titulo-serif mt-1 w-8 shrink-0 text-2xl text-oro-500/70">{{ $i + 1 }}</span>

                        <div class="flex-1 space-y-3">
                            <label class="sr-only" for="paso-{{ $i }}">Instrucción del paso</label>
                            <textarea id="paso-{{ $i }}" name="pasos[{{ $i }}][instruccion]" rows="2" required
                                      placeholder="Sazonar la carne y apanarla con pan molido."
                                      @class(['campo', 'campo-error' => $errors->has("pasos.$i.instruccion")])>{{ $paso['instruccion'] ?? '' }}</textarea>
                            @error("pasos.$i.instruccion") <p class="text-xs text-error-400">{{ $message }}</p> @enderror

                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <label for="tpaso-{{ $i }}" class="text-xs text-crema-500">Minutos</label>
                                    <input id="tpaso-{{ $i }}" type="number" min="0" max="1440"
                                           name="pasos[{{ $i }}][tiempo]" value="{{ $paso['tiempo'] ?? '' }}"
                                           class="campo w-24 text-xs" placeholder="10">
                                </div>

                                <label class="flex cursor-pointer items-center gap-2 text-xs text-crema-300">
                                    <input type="checkbox" name="pasos[{{ $i }}][critico]" value="1"
                                           @checked($paso['critico'] ?? false)
                                           class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                                    Paso crítico
                                </label>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-1">
                            <button type="button" data-mover="arriba" aria-label="Subir este paso"
                                    class="rounded-lg px-2 py-1 text-crema-500 transition hover:bg-oro-500/12 hover:text-crema-100">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path d="M18 15l-6-6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" data-mover="abajo" aria-label="Bajar este paso"
                                    class="rounded-lg px-2 py-1 text-crema-500 transition hover:bg-oro-500/12 hover:text-crema-100">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" data-quitar="paso" aria-label="Quitar este paso"
                                    class="rounded-lg px-2 py-1 text-error-400 transition hover:bg-error-400/12">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                                    <path d="M19 6.4L17.6 5 12 10.6 6.4 5 5 6.4l5.6 5.6L5 17.6 6.4 19l5.6-5.6 5.6 5.6 1.4-1.4-5.6-5.6z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @error('pasos') <p class="mt-3 text-sm text-error-400">{{ $message }}</p> @enderror
    </section>

    {{-- ═══════════════ Nutrición ═══════════════ --}}
    <section class="vidrio space-y-5 rounded-2xl p-7">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="titulo-serif text-xl">Información nutricional</h2>
                <p class="mt-1 text-xs text-crema-500">Por porción. El nutricionista puede corregirla al revisar.</p>
            </div>

            <button type="button" id="calcular-nutricion" class="boton-vidrio vidrio text-sm">
                Calcular desde los ingredientes
            </button>
        </div>

        <div class="grid gap-5 sm:grid-cols-4">
            <x-campo nombre="cal_por_rec" etiqueta="Calorías (kcal)" tipo="number" step="0.01" min="0"
                     :valor="$receta->cal_por_rec" />
            <x-campo nombre="pro_por_rec" etiqueta="Proteínas (g)" tipo="number" step="0.01" min="0"
                     :valor="$receta->pro_por_rec" />
            <x-campo nombre="car_por_rec" etiqueta="Carbohidratos (g)" tipo="number" step="0.01" min="0"
                     :valor="$receta->car_por_rec" />
            <x-campo nombre="gra_por_rec" etiqueta="Grasas (g)" tipo="number" step="0.01" min="0"
                     :valor="$receta->gra_por_rec" />
        </div>

        <p id="aviso-nutricion" class="hidden rounded-xl border border-alerta-400/25 bg-alerta-400/10 px-4 py-3 text-xs text-alerta-400"></p>
    </section>

    {{-- ═══════════════ Etiquetas ═══════════════ --}}
    <section class="vidrio rounded-2xl p-7">
        <h2 class="titulo-serif mb-1 text-xl">Etiquetas</h2>
        <p class="mb-4 text-xs text-crema-500">Ayudan al usuario a filtrar las recetas. Opcionales.</p>

        <div class="flex flex-wrap gap-2">
            @foreach ($etiquetas as $etiqueta)
                <label class="cursor-pointer">
                    <input type="checkbox" name="etiquetas[]" value="{{ $etiqueta->cod_etiqueta }}"
                           @checked(in_array($etiqueta->cod_etiqueta, $etiquetasElegidas))
                           class="peer sr-only">
                    <span class="vidrio inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition
                                 peer-checked:bg-oro-500 peer-checked:text-masa-900
                                 peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-oro-400">
                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $etiqueta->col_eti }}"></span>
                        {{ $etiqueta->nom_eti }}
                    </span>
                </label>
            @endforeach
        </div>
    </section>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="boton-oro">
            {{ $editando ? 'Guardar cambios' : 'Registrar receta' }}
        </button>
        <a href="{{ $editando ? route('admin.recetas.show', $receta) : route('admin.recetas.index') }}"
           class="boton-vidrio vidrio">Cancelar</a>
    </div>
</form>

@endif

{{-- Moldes para las filas nuevas. El JavaScript los clona. --}}
<template id="molde-ingrediente">
    <div class="fila-ingrediente grid items-start gap-3 rounded-xl border border-oro-500/10 p-4 sm:grid-cols-[1fr_7rem_9rem_auto]">
        <div>
            <label class="sr-only" for="ing-__i__">Ingrediente</label>
            <select id="ing-__i__" name="ingredientes[__i__][ingrediente]" class="campo" required>
                <option value="">Elige un ingrediente</option>
                @foreach ($ingredientes as $ing)
                    <option value="{{ $ing->cod_ingrediente }}">{{ $ing->nom_ing }} &middot; {{ $ing->categoria->nom_cat }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="sr-only" for="can-__i__">Cantidad</label>
            <input id="can-__i__" type="number" step="0.001" min="0.001" name="ingredientes[__i__][cantidad]"
                   class="campo" placeholder="500" required>
        </div>

        <div>
            <label class="sr-only" for="uni-__i__">Unidad</label>
            <select id="uni-__i__" name="ingredientes[__i__][unidad]" class="campo" required>
                <option value="">Unidad</option>
                @foreach ($unidades->groupBy('tip_uni') as $tipo => $grupo)
                    <optgroup label="{{ ucfirst($tipo) }}">
                        @foreach ($grupo as $uni)
                            <option value="{{ $uni->cod_unidad }}">{{ $uni->nom_uni }} ({{ $uni->abr_uni }})</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <button type="button" data-quitar="ingrediente"
                class="mt-1 rounded-full px-3 py-2 text-xs text-error-400 transition hover:bg-error-400/12"
                aria-label="Quitar este ingrediente">Quitar</button>

        <div class="sm:col-span-4">
            <div class="flex flex-wrap items-center gap-4">
                <input type="text" name="ingredientes[__i__][nota]" class="campo max-w-sm text-xs"
                       placeholder="Nota: picado fino, sin semillas...">
                <label class="flex cursor-pointer items-center gap-2 text-xs text-crema-300">
                    <input type="checkbox" name="ingredientes[__i__][opcional]" value="1"
                           class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                    Opcional
                </label>
            </div>
        </div>
    </div>
</template>

<template id="molde-paso">
    <div class="fila-paso rounded-xl border border-oro-500/10 p-4">
        <div class="flex items-start gap-3">
            <span class="numero-paso titulo-serif mt-1 w-8 shrink-0 text-2xl text-oro-500/70">1</span>

            <div class="flex-1 space-y-3">
                <label class="sr-only" for="paso-__i__">Instrucción del paso</label>
                <textarea id="paso-__i__" name="pasos[__i__][instruccion]" rows="2" class="campo" required
                          placeholder="Sazonar la carne y apanarla con pan molido."></textarea>

                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label for="tpaso-__i__" class="text-xs text-crema-500">Minutos</label>
                        <input id="tpaso-__i__" type="number" min="0" max="1440" name="pasos[__i__][tiempo]"
                               class="campo w-24 text-xs" placeholder="10">
                    </div>

                    <label class="flex cursor-pointer items-center gap-2 text-xs text-crema-300">
                        <input type="checkbox" name="pasos[__i__][critico]" value="1"
                               class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                        Paso crítico
                    </label>
                </div>
            </div>

            <div class="flex shrink-0 flex-col gap-1">
                <button type="button" data-mover="arriba" aria-label="Subir este paso"
                        class="rounded-lg px-2 py-1 text-crema-500 transition hover:bg-oro-500/12 hover:text-crema-100">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M18 15l-6-6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" data-mover="abajo" aria-label="Bajar este paso"
                        class="rounded-lg px-2 py-1 text-crema-500 transition hover:bg-oro-500/12 hover:text-crema-100">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" data-quitar="paso" aria-label="Quitar este paso"
                        class="rounded-lg px-2 py-1 text-error-400 transition hover:bg-error-400/12">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                        <path d="M19 6.4L17.6 5 12 10.6 6.4 5 5 6.4l5.6 5.6L5 17.6 6.4 19l5.6-5.6 5.6 5.6 1.4-1.4-5.6-5.6z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
@endsection
