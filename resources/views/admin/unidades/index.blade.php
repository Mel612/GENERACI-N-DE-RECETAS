@extends('layouts.app')

@section('titulo', 'Unidades de medida')

@section('contenido')
<x-cabecera titulo="Unidades de medida"
            subtitulo="{{ $unidades->total() }} unidad(es) registrada(s)."
            :accion="route('admin.unidades.create')"
            textoAccion="Nueva unidad" />

<div class="vidrio mb-6 rounded-2xl p-5 text-sm leading-relaxed text-crema-300">
    <p class="mb-2 font-medium text-crema-100">Cómo funciona el factor de conversión</p>
    <p>
        Cada unidad se convierte a la unidad base de su tipo multiplicando por su factor:
        <strong class="text-crema-100">masa a gramo</strong>,
        <strong class="text-crema-100">volumen a mililitro</strong> y
        <strong class="text-crema-100">unidad a pieza</strong>.
        Así el sistema puede comparar 1 kg de tu despensa contra los 500 g que pide una receta.
        Un factor equivocado rompe todos los cálculos de compatibilidad.
    </p>
</div>

<x-buscador :limpiar="route('admin.unidades.index')" marcador="Buscar por nombre o abreviatura">
    <label class="sr-only" for="tipo">Tipo</label>
    <select id="tipo" name="tipo" class="campo max-w-[16rem]">
        <option value="">Todos los tipos</option>
        @foreach ($tipos as $clave => $nombre)
            <option value="{{ $clave }}" @selected(request('tipo') === $clave)>{{ $nombre }}</option>
        @endforeach
    </select>
</x-buscador>

<x-tabla :cabeceras="['Unidad', 'Abreviatura', 'Tipo', 'Factor', 'Equivale a', 'Estado', '>Acciones']"
         descripcion="Unidades de medida y sus factores de conversión"
         :hay="$unidades->isNotEmpty()"
         vacio="No se encontraron unidades.">

    @foreach ($unidades as $unidad)
        @php
            $base = ['masa' => 'g', 'volumen' => 'ml', 'unidad' => 'pz'][$unidad->tip_uni] ?? '';
        @endphp
        <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">
            <td class="px-5 py-4 font-medium text-crema-100">{{ $unidad->nom_uni }}</td>
            <td class="px-5 py-4">
                <span class="vidrio rounded-full px-2.5 py-1 text-xs text-crema-300">{{ $unidad->abr_uni }}</span>
            </td>
            <td class="px-5 py-4 text-crema-300">{{ ucfirst($unidad->tip_uni) }}</td>
            <td class="px-5 py-4 text-crema-300">{{ rtrim(rtrim(number_format((float) $unidad->fac_uni, 6, '.', ''), '0'), '.') }}</td>
            <td class="px-5 py-4 text-xs text-crema-500">
                1 {{ $unidad->abr_uni }} =
                {{ rtrim(rtrim(number_format((float) $unidad->fac_uni, 4, '.', ''), '0'), '.') }} {{ $base }}
            </td>
            <td class="px-5 py-4"><x-estado :activo="$unidad->act_uni" /></td>
            <td class="px-5 py-4">
                <x-fila-acciones :editar="route('admin.unidades.edit', $unidad)"
                                 :eliminar="route('admin.unidades.destroy', $unidad)"
                                 confirmacion="Vas a eliminar la unidad «{{ $unidad->nom_uni }}» ({{ $unidad->abr_uni }}). Si alguna receta o despensa la usa, solo se desactivará." />
            </td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $unidades->links() }}</div>
@endsection
