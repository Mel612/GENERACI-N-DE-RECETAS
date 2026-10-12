{{--
    Cambia entre la hoja (claro) y la pizarra (oscuro).

    Es un <button> real: entra en el orden del tabulador y se activa con
    Enter o Espacio. Muestra el destino, no el estado actual. Sin
    JavaScript el botón no hace nada, pero la página sigue en tema claro.
--}}
<button type="button" data-cambiar-tema aria-pressed="false"
        {{ $attributes->class(['boton-tema']) }}>
    <svg class="si-claro" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12.3 2a9 9 0 108.7 11.3A7.5 7.5 0 0112.3 2z"/>
    </svg>
    <svg class="si-oscuro" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12 7a5 5 0 100 10 5 5 0 000-10zm0-5l1.5 3h-3L12 2zm0 20l-1.5-3h3L12 22zM2 12l3-1.5v3L2 12zm20 0l-3 1.5v-3L22 12zM4.9 4.9l3.2 1.1-2.1 2.1-1.1-3.2zm14.2 14.2l-3.2-1.1 2.1-2.1 1.1 3.2zM4.9 19.1L6 15.9l2.1 2.1-3.2 1.1zM19.1 4.9L18 8.1 15.9 6l3.2-1.1z"/>
    </svg>
    {{-- En el teléfono queda solo el icono; el nombre sigue para el lector de pantalla --}}
    <span class="max-sm:sr-only">
        <span class="si-claro">Modo oscuro</span>
        <span class="si-oscuro">Modo claro</span>
    </span>
</button>
