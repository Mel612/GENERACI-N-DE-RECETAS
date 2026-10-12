@extends('layouts.app')

@php $editando = $categoria->exists; @endphp

@section('titulo', $editando ? 'Editar categoría' : 'Nueva categoría')

@section('contenido')
<x-cabecera :titulo="$editando ? 'Editar categoría de receta' : 'Nueva categoría de receta'" />

<x-formulario :accion="$editando ? route('admin.categorias-receta.update', $categoria) : route('admin.categorias-receta.store')"
              :metodo="$editando ? 'PUT' : 'POST'"
              :volver="route('admin.categorias-receta.index')"
              :textoEnviar="$editando ? 'Guardar cambios' : 'Registrar categoría'">

    <x-campo nombre="nom_car" etiqueta="Nombre" :valor="$categoria->nom_car" requerido placeholder="Comida boliviana"
             maxlength="60" minlength="3" pattern="\s*\p{L}[\p{L}\s,\-]*" title="Solo letras y espacios, sin números ni símbolos" data-sin-numeros
             ayuda="Solo el grupo, sin la palabra «receta» ni «categoría». Solo letras y espacios." />

    {{-- Aparece cuando la IA cree que ya existe otra categoría con el mismo sentido --}}
    @if ($errors->has('nom_car') || old('confirmar_distinta'))
        <div class="rounded-xl border-2 border-alerta-400/50 bg-alerta-400/10 px-4 py-3">
            <label class="flex cursor-pointer items-start gap-3 text-base font-medium text-crema-100">
                <input type="checkbox" name="confirmar_distinta" value="1" @checked(old('confirmar_distinta'))
                       class="mt-1 h-5 w-5 rounded border-oro-500/30 text-oro-500">
                <span>
                    Es una categoría distinta
                    <span class="mt-0.5 block text-sm font-normal text-crema-300">
                        Márcalo solo si revisaste que no es la misma que la categoría parecida que te indicamos.
                    </span>
                </span>
            </label>
        </div>
    @endif

    <x-campo nombre="des_car" etiqueta="Descripción" :valor="$categoria->des_car"
             maxlength="255" placeholder="Platos tradicionales bolivianos"
             ayuda="Opcional. Si la escribes, al menos 5 caracteres y distinta del nombre." />

    @if ($editando && ($recetasActivas ?? 0) > 0)
        <div class="flex items-start gap-3 rounded-xl border border-oro-500/30 bg-oro-500/10 px-4 py-3 text-base text-crema-100">
            <svg viewBox="0 0 24 24" class="mt-1 h-5 w-5 shrink-0 text-oro-400" fill="currentColor" aria-hidden="true">
                <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
            </svg>
            <span>
                Esta categoría tiene <strong>{{ $recetasActivas }} receta(s) activa(s)</strong>.
                Para desactivarla, primero cámbialas de categoría o dalas de baja.
            </span>
        </div>
    @endif

    <x-interruptor nombre="act_car" etiqueta="Categoría activa" :valor="$categoria->act_car ?? true" />
</x-formulario>
@endsection
