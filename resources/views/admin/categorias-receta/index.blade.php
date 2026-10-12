@extends('layouts.app')

@section('titulo', 'Categorías de receta')

@section('contenido')
<x-cabecera titulo="Categorías de receta"
            subtitulo="{{ $categorias->total() }} categoría(s) registrada(s). Organizan las recetas por tipo de preparación."
            :accion="route('admin.categorias-receta.create')"
            textoAccion="Nueva categoría" />

<x-buscador :limpiar="route('admin.categorias-receta.index')" marcador="Buscar por nombre o descripción">
    <label class="sr-only" for="estado">Estado</label>
    <select id="estado" name="estado" class="campo max-w-[10rem]">
        <option value="">Todas</option>
        <option value="activos"   @selected(request('estado') === 'activos')>Activas</option>
        <option value="inactivos" @selected(request('estado') === 'inactivos')>Inactivas</option>
    </select>
</x-buscador>

<x-tabla :cabeceras="['Categoría', 'Descripción', 'Recetas', 'Estado', '>Acciones']"
         descripcion="Categorías que organizan las recetas"
         :hay="$categorias->isNotEmpty()"
         vacio="No se encontraron categorías.">

    @foreach ($categorias as $categoria)
        <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
            <td class="px-5 py-4 font-medium text-crema-100">{{ $categoria->nom_car }}</td>
            <td class="px-5 py-4 text-crema-300">{{ $categoria->des_car ?: '—' }}</td>
            <td class="px-5 py-4">
                <span class="vidrio rounded-full px-2.5 py-1 text-xs text-crema-300">{{ $categoria->recetas_count }}</span>
            </td>
            <td class="px-5 py-4"><x-estado :activo="$categoria->act_car" si="Activa" no="Inactiva" /></td>
            <td class="px-5 py-4">
                <x-fila-acciones :editar="route('admin.categorias-receta.edit', $categoria)"
                                 :eliminar="route('admin.categorias-receta.destroy', $categoria)"
                                 confirmacion="Vas a eliminar la categoría «{{ $categoria->nom_car }}»{{ $categoria->recetas_count > 0 ? ', que tiene ' . $categoria->recetas_count . ' receta(s). Si alguna está activa no se podrá; si ninguna lo está, solo se desactivará' : '' }}." />
            </td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $categorias->links() }}</div>
@endsection
