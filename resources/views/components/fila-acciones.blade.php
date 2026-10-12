{{-- Botones de editar y eliminar al final de una fila. --}}
@props(['editar', 'eliminar' => null, 'confirmacion' => '¿Confirmas eliminar este registro?'])

<div class="flex justify-end gap-2">
    <a href="{{ $editar }}"
       class="rounded-full px-3.5 py-1.5 text-xs text-crema-300 transition hover:bg-oro-500/12 hover:text-crema-100">
        Editar
    </a>

    @if ($eliminar)
        <form method="POST" action="{{ $eliminar }}" data-confirmar="{{ $confirmacion }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="rounded-full px-3.5 py-1.5 text-xs text-error-400 transition hover:bg-error-400/12">
                Eliminar
            </button>
        </form>
    @endif
</div>
