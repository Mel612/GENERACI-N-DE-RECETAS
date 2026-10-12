@extends('layouts.app')

@php $editando = $categoria->exists; @endphp

@section('titulo', $editando ? 'Editar categoría' : 'Nueva categoría')

@section('contenido')
<x-cabecera :titulo="$editando ? 'Editar categoría de ingrediente' : 'Nueva categoría de ingrediente'" />

<x-formulario :accion="$editando ? route('admin.categorias-ingrediente.update', $categoria) : route('admin.categorias-ingrediente.store')"
              :metodo="$editando ? 'PUT' : 'POST'"
              :volver="route('admin.categorias-ingrediente.index')"
              :textoEnviar="$editando ? 'Guardar cambios' : 'Registrar categoría'">

    <x-campo nombre="nom_cat" etiqueta="Nombre" :valor="$categoria->nom_cat" requerido placeholder="Verduras"
             minlength="3" maxlength="60" pattern="\s*\p{L}[\p{L}\s,\-]*"
             title="Solo letras y espacios, entre 3 y 60 caracteres" data-sin-numeros
             ayuda="Solo letras y espacios, sin números ni símbolos. Entre 3 y 60 caracteres." />

    <x-campo nombre="des_cat" etiqueta="Descripción" :valor="$categoria->des_cat"
             maxlength="255" placeholder="Hortalizas y verduras frescas"
             ayuda="Opcional. Si la escribes, al menos 5 caracteres." />

    <x-interruptor nombre="act_cat" etiqueta="Categoría activa" :valor="$categoria->act_cat ?? true"
                   ayuda="Las inactivas no aparecen al registrar un ingrediente nuevo." />
</x-formulario>
@endsection
