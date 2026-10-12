/**
 * Formulario de unidades de medida: botón que pide a la IA el tipo y el
 * factor a partir del nombre («Taza» → volumen, 240 ml).
 *
 * Solo rellena los campos; quien edita revisa y guarda. Sin este archivo
 * el formulario funciona igual, llenando los datos a mano.
 */
export default function iniciarFormularioUnidad() {
    const caja = document.getElementById('sugerencia-unidad');
    if (! caja || caja.dataset.iniciado === '1') return;
    caja.dataset.iniciado = '1';

    const formulario = caja.closest('form');
    const boton = caja.querySelector('[data-sugerir-ia]');
    const aviso = caja.querySelector('[data-aviso-ia]');

    const NOMBRES_BASE = { masa: 'g', volumen: 'ml', unidad: 'pieza(s)' };

    function avisar(texto, tono) {
        aviso.textContent = texto;
        aviso.className = 'w-full rounded-xl border px-4 py-3 text-base font-medium ' + ({
            cargando: 'text-oro-400 border-oro-500/30 bg-oro-500/10',
            exito:    'text-exito-400 border-exito-400/40 bg-exito-400/10',
            error:    'text-error-400 border-error-400/40 bg-error-400/10',
        }[tono] ?? 'hidden');
    }

    boton.addEventListener('click', async () => {
        const campoNombre = formulario.querySelector('#nom_uni');
        const nombre = campoNombre.value.trim();

        if (! nombre) {
            avisar('Escribe primero el nombre de la unidad.', 'error');
            campoNombre.focus();
            return;
        }

        boton.disabled = true;
        boton.classList.add('opacity-60');
        avisar(`Buscando a cuánto equivale «${nombre}»…`, 'cargando');

        try {
            const { data } = await window.axios.post(caja.dataset.urlIa, { nombre });

            const tipo = formulario.querySelector('[name="tip_uni"]');
            tipo.value = data.tipo;
            tipo.dispatchEvent(new Event('change', { bubbles: true }));
            formulario.querySelector('#fac_uni').value = data.factor;

            avisar(`Listo: «${nombre}» es de ${data.tipo} y equivale a ${data.factor} ${NOMBRES_BASE[data.tipo]}. `
                + 'Revisa los valores antes de guardar.', 'exito');
        } catch (error) {
            const datos = error.response?.data;
            avisar(error.response?.status === 429
                ? 'Hiciste muchas consultas seguidas. Espera un minuto.'
                : datos?.mensaje ?? Object.values(datos?.errors ?? {})[0]?.[0]
                    ?? 'No pudimos consultar a la IA. Intenta de nuevo.', 'error');
        } finally {
            boton.disabled = false;
            boton.classList.remove('opacity-60');
        }
    });
}
