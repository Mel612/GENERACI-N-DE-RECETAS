{{--
    Preferencias alimentarias de la persona aplicadas a la búsqueda.

    Muestra las tres situaciones sin esconder ninguna:
      - las que se están aplicando,
      - las que están guardadas pero el sistema todavía no sabe aplicar,
      - no tener ninguna.

    <x-aviso-dieta :dieta="$dieta" :editable="true" />
--}}
@props(['dieta', 'editable' => false, 'aplicada' => true])

@php
    $aplicables = $dieta['aplicables'] ?? collect();
    $sinSoporte = $dieta['sin_soporte'] ?? collect();
@endphp

@if (! ($dieta['tiene'] ?? false))
    {{-- Sin preferencias guardadas: se ofrece, no se insiste --}}
    <div class="vidrio rounded-2xl px-5 py-4 text-sm text-crema-300">
        No tienes preferencias alimentarias guardadas.
        <a href="{{ route('perfil.edit') }}" class="text-oro-400 hover:underline">Elígelas en tu perfil</a>
        y las búsquedas las respetarán solas.
    </div>
@else
    <div class="vidrio rounded-2xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-sm text-crema-100">Tus preferencias alimentarias</p>

                @if ($aplicables->isNotEmpty())
                    <div class="mt-2.5 flex flex-wrap gap-2">
                        @foreach ($aplicables as $p)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-crema-100/6 px-3 py-1 text-xs font-medium text-crema-300">
                                <span class="h-2 w-2 rounded-full" style="background-color: {{ $p['color'] }}" aria-hidden="true"></span>
                                {{ $p['preferencia'] }}
                            </span>
                        @endforeach
                    </div>

                    <p class="mt-2.5 text-xs leading-relaxed text-crema-500">
                        @if ($aplicada)
                            Solo se muestran recetas que cumplen
                            {{ $aplicables->count() === 1 ? 'esta preferencia' : 'todas estas preferencias' }}.
                        @else
                            Esta búsqueda las ignoró: estás viendo todas las recetas.
                        @endif
                    </p>
                @endif

                {{--
                    Decirlo importa: una preferencia guardada que se ignora en
                    silencio hace creer que el filtro está funcionando.
                --}}
                @if ($sinSoporte->isNotEmpty())
                    <p class="mt-3 rounded-lg bg-alerta-400/10 px-3 py-2 text-xs leading-relaxed text-alerta-400">
                        Todavía no se puede filtrar por {{ $sinSoporte->implode(', ') }}:
                        ninguna receta está etiquetada así.
                        {{ $sinSoporte->count() === 1 ? 'Esa preferencia no se aplicó' : 'Esas preferencias no se aplicaron' }}
                        a esta búsqueda.
                    </p>
                @endif
            </div>

            @if ($editable && $aplicables->isNotEmpty())
                <label class="flex shrink-0 cursor-pointer items-center gap-2.5 text-sm text-crema-300">
                    <input type="checkbox" name="respetar_dieta" value="1"
                           @checked(old('respetar_dieta', true))
                           class="h-4 w-4 rounded border-oro-500/30 bg-crema-100/6 text-oro-500 focus:ring-oro-500/40 focus:ring-offset-0">
                    Respetarlas
                </label>
            @endif
        </div>

        @if ($editable)
            <p class="mt-3 text-xs text-crema-500">
                <a href="{{ route('perfil.edit') }}" class="text-oro-400 hover:underline">Cambiarlas en tu perfil</a>
            </p>
        @endif
    </div>
@endif
