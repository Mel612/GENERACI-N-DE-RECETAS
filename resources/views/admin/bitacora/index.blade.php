@extends('layouts.app')

@section('titulo', 'Monitoreo')

@section('contenido')
<x-cabecera titulo="Monitoreo del sistema"
            subtitulo="Registro de accesos, modificaciones y consultas. Es de solo lectura: no se edita ni se borra." />

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ficha titulo="Acciones registradas" :valor="$resumen['total']" />
    <x-ficha titulo="Hoy" :valor="$resumen['hoy']" />
    <x-ficha titulo="Últimos 7 días" :valor="$resumen['semana']" />
    <x-ficha titulo="Usuarios activos esta semana" :valor="$resumen['activos']" tono="neutro" />
</div>

<div class="mb-6">
    <x-grafico-barras titulo="Acciones más frecuentes" :datos="$masFrecuentes" unidad="registros"
                      vacio="Todavía no hay actividad registrada." />
</div>

<form method="GET" class="vidrio mb-6 grid gap-3 rounded-2xl p-4 sm:grid-cols-2 lg:grid-cols-6">
    <div class="lg:col-span-2">
        <label for="usuario" class="etiqueta-campo">Usuario</label>
        <select id="usuario" name="usuario" class="campo">
            <option value="">Todos</option>
            @foreach ($usuarios as $u)
                <option value="{{ $u->cod_usuario }}" @selected(request('usuario') == $u->cod_usuario)>{{ $u->nom_usu }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="accion" class="etiqueta-campo">Acción</label>
        <select id="accion" name="accion" class="campo">
            <option value="">Todas</option>
            @foreach ($acciones as $a)
                <option value="{{ $a }}" @selected(request('accion') === $a)>{{ $etiquetas[$a][0] ?? $a }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="tabla" class="etiqueta-campo">Tabla</label>
        <select id="tabla" name="tabla" class="campo">
            <option value="">Todas</option>
            @foreach ($tablas as $t)
                <option value="{{ $t }}" @selected(request('tabla') === $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="desde" class="etiqueta-campo">Desde</label>
        <input id="desde" type="date" name="desde" value="{{ request('desde') }}" class="campo">
    </div>

    <div>
        <label for="hasta" class="etiqueta-campo">Hasta</label>
        <input id="hasta" type="date" name="hasta" value="{{ request('hasta') }}" class="campo">
    </div>

    <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-6">
        <button type="submit" class="boton-vidrio vidrio">Filtrar</button>
        @if (collect(request()->query())->filter()->isNotEmpty())
            <a href="{{ route('admin.bitacora.index') }}" class="boton-vidrio">Limpiar</a>
        @endif
    </div>
</form>

<x-tabla :cabeceras="['Fecha y hora', 'Usuario', 'Acción', 'Registro afectado', 'Detalle', 'Origen']"
         descripcion="Registro de acciones realizadas en el sistema"
         :hay="$registros->isNotEmpty()"
         vacio="No hay registros que coincidan con esos filtros.">

    @foreach ($registros as $registro)
        @php
            [$texto, $tono] = $etiquetas[$registro->acc_bit] ?? [$registro->acc_bit, 'neutro'];
            $color = ['exito' => 'text-exito-400', 'alerta' => 'text-alerta-400',
                      'error' => 'text-error-400', 'oro' => 'text-oro-400'][$tono] ?? 'text-crema-300';
        @endphp

        <tr class="border-b border-oro-500/8 align-top transition last:border-0 hover:bg-oro-500/5">
            <td class="whitespace-nowrap px-5 py-3.5 text-xs tabular-nums text-crema-300">
                {{ $registro->fec_cre_bit?->format('d/m/Y H:i:s') }}
            </td>

            <td class="px-5 py-3.5 text-crema-100">
                {{ $registro->usuario?->nom_usu ?? 'Cuenta eliminada' }}
            </td>

            <td class="px-5 py-3.5">
                <span class="text-xs font-medium {{ $color }}">{{ $texto }}</span>
            </td>

            <td class="px-5 py-3.5 text-xs text-crema-500">
                @if ($registro->tab_bit)
                    {{ $registro->tab_bit }}@if ($registro->reg_bit) <span class="text-crema-300">#{{ $registro->reg_bit }}</span>@endif
                @else
                    &mdash;
                @endif
            </td>

            <td class="px-5 py-3.5 text-xs text-crema-300">
                @if ($registro->det_bit)
                    <dl class="space-y-0.5">
                        @foreach ($registro->det_bit as $clave => $valor)
                            <div class="flex gap-1.5">
                                <dt class="text-crema-500">{{ $clave }}:</dt>
                                <dd>{{ is_scalar($valor) ? $valor : json_encode($valor, JSON_UNESCAPED_UNICODE) }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    &mdash;
                @endif
            </td>

            <td class="px-5 py-3.5 font-mono text-xs text-crema-500">{{ $registro->ip_bit ?? '—' }}</td>
        </tr>
    @endforeach
</x-tabla>

<div class="mt-6">{{ $registros->links() }}</div>
@endsection
