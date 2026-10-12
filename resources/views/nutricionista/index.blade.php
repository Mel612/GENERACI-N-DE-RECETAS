@extends('layouts.app')

@section('titulo', 'Validación de recetas')

@section('contenido')
@php
    $tonos = ['pendiente' => 'text-crema-300', 'aprobada' => 'text-exito-400',
              'observada' => 'text-alerta-400', 'rechazada' => 'text-error-400'];

    $nombres = ['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada',
                'observada' => 'Con observaciones', 'rechazada' => 'Rechazada'];

    // Pestañas de la bandeja. La primera es la que importa a diario.
    $pestanas = [
        'por_revisar'   => 'Por revisar',
        'sin_verificar' => 'Aprobadas sin verificar',
        'aprobada'      => 'Aprobadas',
        'rechazada'     => 'Rechazadas',
        'todas'         => 'Todas',
    ];
@endphp

<x-cabecera rotulo="Nutricionista"
            titulo="Validación de recetas"
            subtitulo="Revisa que los ingredientes, los pasos y la información nutricional sean correctos antes de que la receta se publique como verificada." />

<div class="mb-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ficha-estado estado="pendiente" :valor="$resumen['pendientes']"
                    detalle="Nunca revisadas" />
    <x-ficha-estado estado="observada" :valor="$resumen['observadas']"
                    detalle="Devueltas para corregir" />
    <x-ficha titulo="Verificadas"
             :valor="$resumen['verificadas']"
             tono="exito"
             detalle="de {{ $resumen['aprobadas'] }} aprobadas"
             icono="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z" />
    <x-ficha titulo="Sin información nutricional"
             :valor="$resumen['sin_nutricion']"
             :tono="$resumen['sin_nutricion'] > 0 ? 'alerta' : 'neutro'"
             detalle="Recetas a las que falta calcular calorías"
             icono="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
</div>

{{-- Pestañas por estado --}}
<div class="mb-5 flex flex-wrap gap-1.5">
    @foreach ($pestanas as $clave => $texto)
        <x-nav-link :href="route('nutricionista.index', ['estado' => $clave])"
                    :activo="$estado === $clave">
            {{ $texto }}
            @if ($clave === 'por_revisar' && $resumen['pendientes'] + $resumen['observadas'] > 0)
                <span class="ml-1.5 rounded-full bg-oro-500/20 px-1.5 py-0.5 text-[0.65rem] tabular-nums text-oro-300">
                    {{ $resumen['pendientes'] + $resumen['observadas'] }}
                </span>
            @endif
        </x-nav-link>
    @endforeach
</div>

<x-buscador :limpiar="route('nutricionista.index', ['estado' => $estado])"
            marcador="Buscar receta por nombre...">
    <input type="hidden" name="estado" value="{{ $estado }}">

    <label class="sr-only" for="categoria">Categoría</label>
    <select id="categoria" name="categoria" class="campo max-w-[14rem]">
        <option value="">Todas las categorías</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->cod_categoria_receta }}"
                    @selected(request('categoria') == $categoria->cod_categoria_receta)>
                {{ $categoria->nom_car }}
            </option>
        @endforeach
    </select>

    <label class="flex cursor-pointer items-center gap-2 text-sm text-crema-300">
        <input type="checkbox" name="sin_nutricion" value="1" @checked(request()->boolean('sin_nutricion'))
               class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
        Sin datos nutricionales
    </label>
</x-buscador>

<x-tabla :cabeceras="['Receta', 'Categoría', 'Contenido', 'Nutrición', 'Estado', '>Acción']"
         descripcion="Recetas en proceso de validación"
         :hay="$recetas->isNotEmpty()"
         vacio="No hay recetas en este estado.">

    @foreach ($recetas as $receta)
        <tr class="border-b border-oro-500/8 transition last:border-0 hover:bg-oro-500/5">

            <td class="px-5 py-4">
                <a href="{{ route('nutricionista.revisar', $receta) }}"
                   class="font-medium text-crema-100 transition hover:text-oro-400">
                    {{ $receta->nom_rec }}
                </a>

                <p class="mt-0.5 text-xs text-crema-500">
                    {{ $receta->tie_tot_rec }} min &middot; {{ $receta->por_rec }} porciones
                    @if ($receta->ori_rec === 'ia_generada')
                        &middot; <span class="text-alerta-400">generada por IA</span>
                    @endif
                    @if ($receta->validaciones_count > 0)
                        &middot; {{ $receta->validaciones_count }} revisión(es)
                    @endif
                </p>
            </td>

            <td class="px-5 py-4 text-crema-300">{{ $receta->categoria->nom_car }}</td>

            {{-- Si falta el contenido base, la revisión no puede avanzar --}}
            <td class="px-5 py-4 text-xs">
                <span @class([
                    'block',
                    'text-error-400' => $receta->ingredientes_count === 0,
                    'text-crema-300' => $receta->ingredientes_count > 0,
                ])>{{ $receta->ingredientes_count }} ingrediente(s)</span>

                <span @class([
                    'block',
                    'text-error-400' => $receta->pasos_count === 0,
                    'text-crema-500' => $receta->pasos_count > 0,
                ])>{{ $receta->pasos_count }} paso(s)</span>
            </td>

            <td class="px-5 py-4 text-xs">
                @if ($receta->cal_por_rec !== null)
                    <span class="tabular-nums text-crema-300">{{ (float) $receta->cal_por_rec }} kcal</span>
                @else
                    <span class="text-alerta-400">Sin datos</span>
                @endif
            </td>

            <td class="px-5 py-4">
                <span class="text-sm {{ $tonos[$receta->est_val_rec] ?? 'text-crema-300' }}">
                    {{ $nombres[$receta->est_val_rec] ?? $receta->est_val_rec }}
                </span>

                @if ($receta->ver_rec)
                    <span class="sello-verificada mt-1">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/>
                        </svg>
                        Verificada
                    </span>
                @elseif ($receta->est_val_rec === 'aprobada')
                    <span class="mt-1 block text-[0.62rem] text-crema-500">Falta verificar</span>
                @endif
            </td>

            <td class="px-5 py-4 text-right">
                <a href="{{ route('nutricionista.revisar', $receta) }}" class="boton-vidrio vidrio text-xs">
                    Revisar
                </a>
            </td>
        </tr>
    @endforeach
</x-tabla>

@if ($recetas->hasPages())
    <div class="mt-6">{{ $recetas->links() }}</div>
@endif

<p class="mt-6 text-xs text-crema-500">
    Has emitido {{ $misRevisiones }} revisión(es) en total.
    Una receta solo se publica como <strong class="text-crema-300">verificada</strong> después de aprobarla.
</p>
@endsection
