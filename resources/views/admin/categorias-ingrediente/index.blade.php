@extends('layouts.app')

@section('titulo', 'Categorías de ingrediente')

@section('contenido')
<x-cabecera titulo="Categorías de ingrediente"
            subtitulo="{{ $categorias->total() }} categoría(s) registrada(s). Clasifican el catálogo de alimentos."
            :accion="route('admin.categorias-ingrediente.create')"
            textoAccion="Nueva categoría" />

<x-buscador :limpiar="route('admin.categorias-ingrediente.index')" marcador="Buscar por nombre o descripción">
    <label class="sr-only" for="estado">Estado</label>
    <select id="estado" name="estado" class="campo max-w-[10rem]">
        <option value="">Todas</option>
        <option value="activos"   @selected(request('estado') === 'activos')>Activas</option>
        <option value="inactivos" @selected(request('estado') === 'inactivos')>Inactivas</option>
    </select>
</x-buscador>

<x-tabla :cabeceras="['Categoría', 'Descripción', 'Ingredientes', 'Estado', '>Acciones']"
         descripcion="Categorías que clasifican los ingredientes"
         :hay="$categorias->isNotEmpty()"
         vacio="No se encontraron categorías.">

    @foreach ($categorias as $categoria)
        <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
            <td class="px-5 py-4 font-medium text-crema-100">{{ $categoria->nom_cat }}</td>
            <td class="px-5 py-4 text-crema-300">{{ $categoria->des_cat ?: '—' }}</td>
            <td class="px-5 py-4">
                <span class="vidrio rounded-full px-2.5 py-1 text-xs text-crema-300">
                    {{ $categoria->ingredientes_count }}
                </span>
            </td>
            <td class="px-5 py-4"><x-estado :activo="$categoria->act_cat" si="Activa" no="Inactiva" /></td>
            <td class="px-5 py-4">
                <x-fila-acciones :editar="route('admin.categorias-ingrediente.edit', $categoria)"
                                 :eliminar="route('admin.categorias-ingrediente.destroy', $categoria)"
                                 confirmacion="¿Confirmas eliminar {{ $categoria->nom_cat }}? Si tiene ingredientes, solo se desactivará." />
            </td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $categorias->links() }}</div>
@endsection
