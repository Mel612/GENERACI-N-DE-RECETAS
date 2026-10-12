@extends('layouts.app')

@section('titulo', 'Etiquetas')

@section('contenido')
<x-cabecera titulo="Etiquetas de receta"
            subtitulo="{{ $etiquetas->total() }} etiqueta(s) registrada(s). Permiten filtrar las recetas por características."
            :accion="route('admin.etiquetas.create')"
            textoAccion="Nueva etiqueta" />

<x-buscador :limpiar="route('admin.etiquetas.index')" marcador="Buscar por nombre">
    <label class="sr-only" for="estado">Estado</label>
    <select id="estado" name="estado" class="campo max-w-[10rem]">
        <option value="">Todas</option>
        <option value="activos"   @selected(request('estado') === 'activos')>Activas</option>
        <option value="inactivos" @selected(request('estado') === 'inactivos')>Inactivas</option>
    </select>
</x-buscador>

<x-tabla :cabeceras="['Etiqueta', 'Color', 'Recetas', 'Estado', '>Acciones']"
         descripcion="Etiquetas que clasifican las recetas"
         :hay="$etiquetas->isNotEmpty()"
         vacio="No se encontraron etiquetas.">

    @foreach ($etiquetas as $etiqueta)
        <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
            <td class="px-5 py-4">
                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium"
                      style="background-color: {{ $etiqueta->col_eti }}22; color: {{ $etiqueta->col_eti }};">
                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $etiqueta->col_eti }};"></span>
                    {{ $etiqueta->nom_eti }}
                </span>
            </td>
            <td class="px-5 py-4 font-mono text-xs text-crema-300">{{ strtoupper($etiqueta->col_eti) }}</td>
            <td class="px-5 py-4">
                <span class="vidrio rounded-full px-2.5 py-1 text-xs text-crema-300">{{ $etiqueta->recetas_count }}</span>
            </td>
            <td class="px-5 py-4"><x-estado :activo="$etiqueta->act_eti" si="Activa" no="Inactiva" /></td>
            <td class="px-5 py-4">
                <x-fila-acciones :editar="route('admin.etiquetas.edit', $etiqueta)"
                                 :eliminar="route('admin.etiquetas.destroy', $etiqueta)"
                                 confirmacion="¿Confirmas eliminar {{ $etiqueta->nom_eti }}? Si está en uso, solo se desactivará." />
            </td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $etiquetas->links() }}</div>
@endsection
