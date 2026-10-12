@extends('layouts.app')

@php $editando = $etiqueta->exists; @endphp

@section('titulo', $editando ? 'Editar etiqueta' : 'Nueva etiqueta')

@section('contenido')
<x-cabecera :titulo="$editando ? 'Editar etiqueta' : 'Nueva etiqueta'" />

<x-formulario :accion="$editando ? route('admin.etiquetas.update', $etiqueta) : route('admin.etiquetas.store')"
              :metodo="$editando ? 'PUT' : 'POST'"
              :volver="route('admin.etiquetas.index')"
              :textoEnviar="$editando ? 'Guardar cambios' : 'Registrar etiqueta'">

    <x-campo nombre="nom_eti" etiqueta="Nombre" :valor="$etiqueta->nom_eti" requerido placeholder="Saludable"
             maxlength="40" minlength="3" pattern="\s*\p{L}[\p{L}\s,\-]*" title="Solo letras y espacios, sin números ni símbolos" data-sin-numeros
             ayuda="Solo letras y espacios, sin números ni símbolos." />

    <div>
        <label for="col_eti" class="etiqueta-campo">
            Color <span class="text-oro-400" aria-hidden="true">*</span>
        </label>
        <div class="flex items-center gap-3">
            <input type="color" id="col_eti_selector" value="{{ old('col_eti', $etiqueta->col_eti ?? '#16A34A') }}"
                   class="h-11 w-14 cursor-pointer rounded-lg border border-oro-500/20 bg-transparent p-1"
                   aria-label="Elegir color visualmente"
                   oninput="document.getElementById('col_eti').value = this.value.toUpperCase();">

            <input type="text" id="col_eti" name="col_eti"
                   value="{{ old('col_eti', $etiqueta->col_eti ?? '#16A34A') }}" required
                   pattern="#[0-9A-Fa-f]{6}" placeholder="#16A34A"
                   @class(['campo font-mono', 'campo-error' => $errors->has('col_eti')])
                   oninput="document.getElementById('col_eti_selector').value = this.value;">
        </div>
        @error('col_eti')
            <p class="mt-1.5 text-xs text-error-400">{{ $message }}</p>
        @else
            <p class="mt-1.5 text-xs text-crema-500">Formato hexadecimal, por ejemplo #16A34A.</p>
        @enderror
    </div>

    <x-interruptor nombre="act_eti" etiqueta="Etiqueta activa" :valor="$etiqueta->act_eti ?? true" />
</x-formulario>
@endsection
