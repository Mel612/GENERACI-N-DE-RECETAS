/**
 * Formulario de ingredientes del catálogo (administrador y nutricionista).
 *
 * - Las calorías se calculan solas a partir de los macronutrientes
 *   (4 kcal por gramo de proteína y de carbohidrato, 9 por gramo de grasa).
 *   El servidor hace la misma cuenta al guardar.
 * - El botón de la IA llena proteínas, carbohidratos, grasas, densidad y
 *   alérgeno a partir del nombre.
 *
 * Sin este archivo el formulario sigue funcionando: se escriben los macros
 * a mano y el servidor calcula las calorías.
 */

/**
 * Quita los dígitos de un campo de texto.
 *
 * @returns {boolean} true si había alguno
 */
export function quitarNumeros(campo) {
    if (! /\d/.test(campo.value)) return false;

    const posicion = campo.selectionStart - (campo.value.slice(0, campo.selectionStart).match(/\d/g) ?? []).length;
    campo.value = campo.value.replace(/\d/g, '');
    campo.setSelectionRange(posicion, posicion);
    return true;
}

/** Calorías por 100 g según los factores de Atwater. */
function calcularCalorias(proteinas, carbohidratos, grasas) {
    return Math.round((4 * proteinas + 4 * carbohidratos + 9 * grasas) * 100) / 100;
}

export default function iniciarFormularioIngrediente() {
    // Campos de nombre: no aceptan números (el buscador de la despensa lo hace por su cuenta)
    document.querySelectorAll('input[data-sin-numeros]').forEach((campo) => {
        if (campo.dataset.iniciado === '1') return;
        campo.dataset.iniciado = '1';

        campo.addEventListener('input', () => {
            if (! quitarNumeros(campo)) return;
            campo.setCustomValidity('');
            mostrarAyuda(campo, 'El nombre no puede llevar números.');
        });
    });

    const caja = document.getElementById('nutricion-ingrediente');
    if (! caja || caja.dataset.iniciado === '1') return;
    caja.dataset.iniciado = '1';

    const formulario = caja.closest('form');
    const campos = {
        pro_ing: formulario.querySelector('#pro_ing'),
        car_ing: formulario.querySelector('#car_ing'),
        gra_ing: formulario.querySelector('#gra_ing'),
        den_ing: formulario.querySelector('#den_ing'),
    };
    const calorias = caja.querySelector('#cal_ing');
    const alergeno = formulario.querySelector('input[type="checkbox"][name="ale_ing"]');
    const boton    = caja.querySelector('[data-completar-ia]');
    const aviso    = caja.querySelector('[data-aviso-ia]');
    const urlIa    = caja.dataset.urlIa;

    // ----------------------------------------------------------- calorías

    function actualizarCalorias() {
        const valores = [campos.pro_ing, campos.car_ing, campos.gra_ing].map((c) => c.value.trim());

        if (valores.every((v) => v === '')) {
            calorias.value = '';
            return;
        }

        const [p, c, g] = valores.map((v) => Math.max(0, parseFloat(v) || 0));
        calorias.value = calcularCalorias(p, c, g);
    }

    [campos.pro_ing, campos.car_ing, campos.gra_ing].forEach((campo) =>
        campo.addEventListener('input', actualizarCalorias));
    actualizarCalorias();

    // ------------------------------------------------------------- la IA

    if (! urlIa) {
        caja.querySelector('[data-solo-con-ia]')?.remove();
        return;
    }

    function avisar(texto, tono) {
        aviso.textContent = texto;
        aviso.classList.toggle('hidden', ! texto);
        aviso.className = aviso.className.replace(/\b(text|border|bg)-(exito|error|oro)-\S+/g, '').trim();
        if (! texto) return;

        const colores = {
            cargando: ['text-oro-400', 'border-oro-500/30', 'bg-oro-500/10'],
            exito:    ['text-exito-400', 'border-exito-400/40', 'bg-exito-400/10'],
            error:    ['text-error-400', 'border-error-400/40', 'bg-error-400/10'],
        }[tono];
        aviso.classList.add('rounded-xl', 'border', 'px-4', 'py-3', 'text-base', 'font-medium', ...colores);
    }

    boton.addEventListener('click', async () => {
        const nombre = formulario.querySelector('#nom_ing').value.trim();
        const categoria = formulario.querySelector('[name="cod_categoria_ingrediente_ing"]')?.value || null;

        if (! nombre) {
            avisar('Escribe primero el nombre del ingrediente.', 'error');
            formulario.querySelector('#nom_ing').focus();
            return;
        }

        boton.disabled = true;
        boton.classList.add('opacity-60');
        avisar(`Calculando los valores nutricionales de «${nombre}»…`, 'cargando');

        try {
            const { data } = await window.axios.post(urlIa, { nombre, categoria });

            campos.pro_ing.value = data.pro_ing;
            campos.car_ing.value = data.car_ing;
            campos.gra_ing.value = data.gra_ing;
            if (campos.den_ing) campos.den_ing.value = data.den_ing ?? '';
            if (alergeno) {
                alergeno.checked = data.ale_ing;
                alergeno.dispatchEvent(new Event('change', { bubbles: true }));
            }
            actualizarCalorias();

            avisar(`Listo: ${data.cal_ing} kcal por 100 g. Revisa los valores antes de guardar.`, 'exito');
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

/** Muestra un aviso breve debajo de un campo, en el lugar del texto de ayuda. */
function mostrarAyuda(campo, texto) {
    let ayuda = campo.parentElement.parentElement.querySelector('[data-aviso-campo]');

    if (! ayuda) {
        ayuda = document.createElement('p');
        ayuda.dataset.avisoCampo = '';
        ayuda.setAttribute('role', 'status');
        ayuda.className = 'mt-1.5 text-sm font-medium text-error-400';
        campo.parentElement.after(ayuda);
    }

    ayuda.textContent = texto;
    clearTimeout(ayuda.temporizador);
    ayuda.temporizador = setTimeout(() => { ayuda.textContent = ''; }, 3000);
}
