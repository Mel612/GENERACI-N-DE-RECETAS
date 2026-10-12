@extends('layouts.app')

@section('titulo', 'Ingredientes')

@section('contenido')
<x-cabecera titulo="Catálogo de ingredientes"
            subtitulo="{{ $ingredientes->total() }} ingrediente(s) registrado(s)."
            :accion="route('admin.ingredientes.create')"
            textoAccion="Nuevo ingrediente" />

@if ($sinNutricion > 0)
    <div class="mb-6 flex items-start gap-3 rounded-xl border border-alerta-400/25 bg-alerta-400/10 px-4 py-3 text-sm text-alerta-400">
        <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <span>
            {{ $sinNutricion }} ingrediente(s) sin información nutricional. Sin esos datos, las recetas
            que los usen no podrán calcular calorías por porción.
            <a href="{{ route('admin.ingredientes.index', ['sin_nutricion' => 1]) }}" class="underline">Verlos</a>.
        </span>
    </div>
@endif

<x-buscador :limpiar="route('admin.ingredientes.index')" marcador="Buscar por nombre o sinónimo">
    <label class="sr-only" for="categoria">Categoría</label>
    <select id="categoria" name="categoria" class="campo max-w-[14rem]">
        <option value="">Todas las categorías</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->cod_categoria_ingrediente }}"
                    @selected(request('categoria') == $categoria->cod_categoria_ingrediente)>
                {{ $categoria->nom_cat }}
            </option>
        @endforeach
    </select>

    <label class="sr-only" for="estado">Estado</label>
    <select id="estado" name="estado" class="campo max-w-[10rem]">
        <option value="">Todos</option>
        <option value="activos"   @selected(request('estado') === 'activos')>Activos</option>
        <option value="inactivos" @selected(request('estado') === 'inactivos')>Inactivos</option>
    </select>

    <label class="flex items-center gap-2 text-sm text-crema-300">
        <input type="checkbox" name="alergenos" value="1" @checked(request()->boolean('alergenos'))
               class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
        Solo alérgenos
    </label>

    <label class="flex items-center gap-2 text-sm text-crema-300">
        <input type="checkbox" name="sin_densidad" value="1" @checked(request()->boolean('sin_densidad'))
               class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
        Sin densidad
    </label>

    <label class="flex items-center gap-2 text-sm text-crema-300">
        <input type="checkbox" name="sin_precio" value="1" @checked(request()->boolean('sin_precio'))
               class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
        Sin precio
    </label>
</x-buscador>

<x-tabla :cabeceras="['Ingrediente', 'Categoría', 'Nutrición por 100 g', 'Uso', 'Estado', '>Acciones']"
         descripcion="Catálogo general de ingredientes del sistema"
         :hay="$ingredientes->isNotEmpty()"
         vacio="No se encontraron ingredientes con esos criterios.">

    @foreach ($ingredientes as $ingrediente)
        <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
            <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                    <x-foto :url="$ingrediente->urlImagen()" icono="ingrediente"
                            class="h-9 w-9 shrink-0 rounded-lg" />
                    <span class="font-medium text-crema-100">{{ $ingrediente->nom_ing }}</span>
                    @if ($ingrediente->ale_ing)
                        <span class="rounded-full bg-alerta-400/12 px-2 py-0.5 text-[0.62rem] text-alerta-400"
                              title="Alérgeno común">Alérgeno</span>
                    @endif
                </div>
                @if ($ingrediente->sin_ing)
                    <p class="mt-0.5 text-xs text-crema-500">{{ $ingrediente->sin_ing }}</p>
                @endif
                @if ($ingrediente->den_ing !== null)
                    <p class="mt-0.5 text-[0.62rem] text-crema-500">
                        {{ rtrim(rtrim(number_format((float) $ingrediente->den_ing, 3, ',', ''), '0'), ',') }} g/ml
                    </p>
                @endif
                @if ($ingrediente->pre_ing !== null)
                    <p class="mt-0.5 text-[0.62rem] text-crema-500">
                        Bs {{ number_format((float) $ingrediente->pre_ing, 2, ',', '.') }}/kg
                    </p>
                @endif
            </td>

            <td class="px-5 py-4 text-crema-300">{{ $ingrediente->categoria->nom_cat }}</td>

            <td class="px-5 py-4">
                @if ($ingrediente->cal_ing !== null)
                    <span class="text-crema-100">{{ (float) $ingrediente->cal_ing }} kcal</span>
                    <p class="mt-0.5 text-xs text-crema-500">
                        P {{ (float) $ingrediente->pro_ing }} &middot;
                        C {{ (float) $ingrediente->car_ing }} &middot;
                        G {{ (float) $ingrediente->gra_ing }}
                    </p>
                @else
                    <span class="text-xs text-alerta-400">Sin datos</span>
                @endif
            </td>

            <td class="px-5 py-4 text-xs text-crema-500">
                {{ $ingrediente->recetas_count }} receta(s)<br>
                {{ $ingrediente->inventarios_count }} despensa(s)
            </td>

            <td class="px-5 py-4"><x-estado :activo="$ingrediente->act_ing" /></td>

            <td class="px-5 py-4">
                <x-fila-acciones :editar="route('admin.ingredientes.edit', $ingrediente)"
                                 :eliminar="route('admin.ingredientes.destroy', $ingrediente)"
                                 confirmacion="Vas a eliminar «{{ $ingrediente->nom_ing }}» del catálogo. Si alguna receta o despensa lo usa, solo se desactivará." />
            </td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $ingredientes->links() }}</div>
@endsection
