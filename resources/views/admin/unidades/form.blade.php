@extends('layouts.app')

@php
    $editando = $unidad->exists;
    $esBase = $editando && $unidad->esBase();
    $iaDisponible = app(\App\Services\AsistenteIA::class)->disponible();
@endphp

@section('titulo', $editando ? 'Editar unidad' : 'Nueva unidad')

@section('contenido')
<x-cabecera :titulo="$editando ? 'Editar unidad de medida' : 'Nueva unidad de medida'" />

<x-formulario :accion="$editando ? route('admin.unidades.update', $unidad) : route('admin.unidades.store')"
              :metodo="$editando ? 'PUT' : 'POST'"
              :volver="route('admin.unidades.index')"
              :textoEnviar="$editando ? 'Guardar cambios' : 'Registrar unidad'">

    <div class="grid gap-5 sm:grid-cols-2">
        <x-campo nombre="nom_uni" etiqueta="Nombre" :valor="$unidad->nom_uni" requerido placeholder="Kilogramos"
                 maxlength="40" minlength="3" pattern="\s*\p{L}[\p{L}\s,\-]*" title="Solo letras y espacios, sin números ni símbolos" data-sin-numeros />
        <x-campo nombre="abr_uni" etiqueta="Abreviatura" :valor="$unidad->abr_uni" requerido placeholder="kg"
                 maxlength="6" data-sin-numeros
                 ayuda="Es la que se muestra en las recetas." />
    </div>

    @if ($esBase)
        <div class="flex items-start gap-3 rounded-xl border border-oro-500/30 bg-oro-500/10 px-4 py-3 text-base text-crema-100">
            <svg viewBox="0 0 24 24" class="mt-1 h-5 w-5 shrink-0 text-oro-400" fill="currentColor" aria-hidden="true">
                <path d="M12 1l9 4v6c0 5.5-3.8 10.7-9 12-5.2-1.3-9-6.5-9-12V5l9-4z"/>
            </svg>
            <span>
                <strong>Unidad base de {{ $unidad->tip_uni }}.</strong>
                Todas las demás unidades de {{ $unidad->tip_uni }} se convierten a esta, así que su tipo
                y su factor (1) no se pueden cambiar, ni se puede desactivar. Solo puedes editar el nombre y la abreviatura.
            </span>
        </div>
    @elseif ($iaDisponible)
        {{-- La lógica del botón está en resources/js/unidades.js --}}
        <div class="flex flex-wrap items-center gap-3" id="sugerencia-unidad"
             data-url-ia="{{ route('admin.unidades.sugerencia-ia') }}">
            <button type="button" class="boton-oro" data-sugerir-ia>
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="M12 3l1.9 5.9H20l-4.9 3.6 1.9 5.9L12 14.8 7 18.4l1.9-5.9L4 8.9h6.1L12 3z"/>
                </svg>
                Completar tipo y factor con IA
            </button>
            <p class="text-xs text-crema-500">A partir del nombre, por ejemplo «Taza» → volumen, 240 ml.</p>
            <p data-aviso-ia role="status" aria-live="polite" class="hidden w-full"></p>
        </div>
    @endif

    <x-selector nombre="tip_uni" etiqueta="Tipo de medida"
                :opciones="collect($tipos)->map(fn ($n, $c) => ['clave' => $c, 'nombre' => $n])->values()"
                clave="clave" texto="nombre"
                :valor="$unidad->tip_uni" requerido vacio="Elige un tipo"
                ayuda="Solo se comparan cantidades del mismo tipo: no se puede convertir masa a volumen sin saber la densidad." />

    <x-campo nombre="fac_uni" etiqueta="Factor de conversión" tipo="number" step="0.000001" min="0.001" max="100000"
             :valor="$unidad->fac_uni ?? 1" requerido placeholder="1000" :readonly="$esBase"
             ayuda="Cuántas unidades base equivale. Masa: de 0,001 a 100 000 g · Volumen: de 0,1 a 20 000 ml · Unidad: número entero de 1 a 1000 piezas." />

    @if ($errors->has('fac_uni') || $errors->has('tip_uni') || old('confirmar_factor'))
        <div class="rounded-xl border-2 border-alerta-400/50 bg-alerta-400/10 px-4 py-3">
            <label class="flex cursor-pointer items-start gap-3 text-base font-medium text-crema-100">
                <input type="checkbox" name="confirmar_factor" value="1" @checked(old('confirmar_factor'))
                       class="mt-1 h-5 w-5 rounded border-oro-500/30 text-oro-500">
                <span>
                    Confirmo el factor
                    <span class="mt-0.5 block text-sm font-normal text-crema-300">
                        Márcalo solo si revisaste el tipo y el factor y son correctos aunque el sistema los vea raros.
                    </span>
                </span>
            </label>
        </div>
    @endif

    @if ($editando && ! $esBase)
        <div class="flex items-start gap-3 rounded-xl border border-alerta-400/25 bg-alerta-400/10 px-4 py-3 text-sm text-alerta-400">
            <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <span>
                Cambiar el factor afecta a todas las recetas e inventarios que ya usan esta unidad.
                El cambio queda registrado en la bitácora.
            </span>
        </div>
    @endif
    <x-interruptor nombre="act_uni" etiqueta="Unidad activa" :valor="$unidad->act_uni ?? true" />
</x-formulario>
@endsection
