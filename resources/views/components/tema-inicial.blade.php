{{--
    Aplica el tema guardado antes de pintar la página.

    Va en el <head>, en línea y antes del CSS: si esperara al JavaScript
    del final, quien eligió la pizarra vería un destello blanco en cada
    carga. Por omisión el tema es claro.
--}}
<script>
    (function () {
        try {
            if (localStorage.getItem('tema') === 'oscuro') {
                document.documentElement.dataset.tema = 'oscuro';
            }
        } catch (e) {}
    })();
</script>
