@extends('layouts.app')

@php $editando = $preferencia->exists; @endphp

@section('titulo', $editando ? 'Editar preferencia' : 'Nueva preferencia')

@section('contenido')
<x-cabecera :titulo="$editando ? 'Editar preferencia alimentaria' : 'Nueva preferencia alimentaria'" />

<x-formulario :accion="$editando ? route('admin.preferencias.update', $preferencia) : route('admin.preferencias.store')"
              :metodo="$editando ? 'PUT' : 'POST'"
              :volver="route('admin.preferencias.index')"
              :textoEnviar="$editando ? 'Guardar cambios' : 'Registrar preferencia'">

    <x-campo nombre="nom_pre" etiqueta="Nombre" :valor="$preferencia->nom_pre" requerido placeholder="Sin gluten"
             maxlength="60" minlength="3" pattern="\s*\p{L}[\p{L}\s,\-]*" title="Solo letras y espacios, sin números ni símbolos" data-sin-numeros
             ayuda="Solo letras y espacios, sin números ni símbolos." />

    <x-campo nombre="des_pre" etiqueta="Descripción" :valor="$preferencia->des_pre"
             placeholder="Evita el trigo, la cebada y el centeno"
             ayuda="Se muestra al usuario cuando elige sus preferencias." />

    <x-interruptor nombre="act_pre" etiqueta="Preferencia activa" :valor="$preferencia->act_pre ?? true" />
</x-formulario>
@endsection
