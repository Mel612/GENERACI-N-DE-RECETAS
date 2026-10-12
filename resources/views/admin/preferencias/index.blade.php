@extends('layouts.app')

@section('titulo', 'Preferencias alimentarias')

@section('contenido')
<x-cabecera titulo="Preferencias alimentarias"
            subtitulo="{{ $preferencias->total() }} preferencia(s) registrada(s). Son las que cada usuario elige en su perfil."
            :accion="route('admin.preferencias.create')"
            textoAccion="Nueva preferencia" />

<x-buscador :limpiar="route('admin.preferencias.index')" marcador="Buscar por nombre o descripción">
    <label class="sr-only" for="estado">Estado</label>
    <select id="estado" name="estado" class="campo max-w-[10rem]">
        <option value="">Todas</option>
        <option value="activos"   @selected(request('estado') === 'activos')>Activas</option>
        <option value="inactivos" @selected(request('estado') === 'inactivos')>Inactivas</option>
    </select>
</x-buscador>

<x-tabla :cabeceras="['Preferencia', 'Descripción', 'Usuarios', 'Estado', '>Acciones']"
         descripcion="Preferencias alimentarias disponibles para los usuarios"
         :hay="$preferencias->isNotEmpty()"
         vacio="No se encontraron preferencias.">

    @foreach ($preferencias as $preferencia)
        <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
            <td class="px-5 py-4 font-medium text-crema-100">{{ $preferencia->nom_pre }}</td>
            <td class="px-5 py-4 text-crema-300">{{ $preferencia->des_pre ?: '—' }}</td>
            <td class="px-5 py-4">
                <span class="vidrio rounded-full px-2.5 py-1 text-xs text-crema-300">{{ $preferencia->usuarios_count }}</span>
            </td>
            <td class="px-5 py-4"><x-estado :activo="$preferencia->act_pre" si="Activa" no="Inactiva" /></td>
            <td class="px-5 py-4">
                <x-fila-acciones :editar="route('admin.preferencias.edit', $preferencia)"
                                 :eliminar="route('admin.preferencias.destroy', $preferencia)"
                                 confirmacion="¿Confirmas eliminar {{ $preferencia->nom_pre }}? Si hay usuarios que la eligieron, solo se desactivará." />
            </td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $preferencias->links() }}</div>
@endsection
