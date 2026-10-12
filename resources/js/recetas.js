/**
 * Formulario de recetas: filas dinámicas de ingredientes y pasos.
 *
 * Sin dependencias. Si este archivo no carga, el formulario sigue siendo
 * utilizable: se envía con las filas que Blade ya dibujó.
 */

export default function iniciarFormularioRecetas() {
    const formulario = document.getElementById('formulario-receta');
    if (! formulario) return;

    // Seguro contra una segunda llamada: sin esto, cada botón quedaría con
    // dos escuchadores y un clic agregaría dos filas.
    if (formulario.dataset.iniciado === '1') return;
    formulario.dataset.iniciado = '1';

    const listaIngredientes = document.getElementById('lista-ingredientes');
    const listaPasos        = document.getElementById('lista-pasos');

    // Contador para los nombres de campo. Arranca después de las filas
    // existentes para no pisar índices ya usados.
    let siguiente = Math.max(
        listaIngredientes?.children.length ?? 0,
        listaPasos?.children.length ?? 0,
    );

    /** Clona un molde reemplazando __i__ por un índice libre. */
    function crearFila(molde) {
        const html = document.getElementById(molde).innerHTML.replaceAll('__i__', siguiente++);
        const caja = document.createElement('div');
        caja.innerHTML = html.trim();
        return caja.firstElementChild;
    }

    /** Renumera los pasos según el orden en que están en pantalla. */
    function renumerarPasos() {
        listaPasos?.querySelectorAll('.fila-paso').forEach((fila, i) => {
            const numero = fila.querySelector('.numero-paso');
            if (numero) numero.textContent = i + 1;
        });
    }

    // ------------------------------------------------------------ agregar

    document.querySelectorAll('[data-agregar]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const tipo  = boton.dataset.agregar;
            const lista = tipo === 'ingrediente' ? listaIngredientes : listaPasos;
            const fila  = crearFila(`molde-${tipo}`);

            lista.appendChild(fila);
            renumerarPasos();

            // El foco va al primer campo de la fila nueva.
            fila.querySelector('select, textarea, input')?.focus();
        });
    });

    // ------------------------------------- quitar y mover (delegación)

    formulario.addEventListener('click', (evento) => {
        const quitar = evento.target.closest('[data-quitar]');
        const mover  = evento.target.closest('[data-mover]');

        if (quitar) {
            const tipo  = quitar.dataset.quitar;
            const lista = tipo === 'ingrediente' ? listaIngredientes : listaPasos;
            const fila  = quitar.closest(tipo === 'ingrediente' ? '.fila-ingrediente' : '.fila-paso');

            // Siempre queda al menos una fila: el servidor exige mínimo uno
            // de cada cosa, y una lista vacía deja al usuario sin dónde escribir.
            if (lista.children.length <= 1) {
                fila.querySelectorAll('input, textarea, select').forEach((campo) => {
                    if (campo.type === 'checkbox') campo.checked = false;
                    else campo.value = '';
                });
                return;
            }

            fila.remove();
            renumerarPasos();
            return;
        }

        if (mover) {
            const fila = mover.closest('.fila-paso');
            const otra = mover.dataset.mover === 'arriba'
                ? fila.previousElementSibling
                : fila.nextElementSibling;

            if (! otra) return;

            mover.dataset.mover === 'arriba'
                ? listaPasos.insertBefore(fila, otra)
                : listaPasos.insertBefore(otra, fila);

            renumerarPasos();
            mover.focus();
        }
    });

    // ------------------------------------------- cálculo nutricional

    const botonCalcular = document.getElementById('calcular-nutricion');
    const aviso         = document.getElementById('aviso-nutricion');

    botonCalcular?.addEventListener('click', () => {
        let datos;
        try {
            datos = JSON.parse(formulario.dataset.nutricion);
        } catch {
            return;
        }

        const porciones = parseFloat(formulario.querySelector('[name="por_rec"]')?.value) || 1;

        const suma = { cal: 0, pro: 0, car: 0, gra: 0 };
        const ignorados = [];
        let contados = 0;

        listaIngredientes.querySelectorAll('.fila-ingrediente').forEach((fila) => {
            const idIng  = fila.querySelector('[name$="[ingrediente]"]')?.value;
            const idUni  = fila.querySelector('[name$="[unidad]"]')?.value;
            const cant   = parseFloat(fila.querySelector('[name$="[cantidad]"]')?.value);

            if (! idIng || ! idUni || ! cant) return;

            const ing = datos.ingredientes[idIng];
            const uni = datos.unidades[idUni];
            if (! ing || ! uni) return;

            // Los valores nutricionales están por cada 100 g, así que solo se
            // puede calcular sobre unidades de masa. Convertir una taza o una
            // pieza a gramos exigiría conocer la densidad de cada alimento.
            if (uni.tipo !== 'masa') {
                ignorados.push(`${ing.nombre} (medido en ${uni.tipo})`);
                return;
            }

            if (ing.cal === null) {
                ignorados.push(`${ing.nombre} (sin datos nutricionales)`);
                return;
            }

            const gramos = cant * uni.factor;
            const factor = gramos / 100;

            suma.cal += (ing.cal || 0) * factor;
            suma.pro += (ing.pro || 0) * factor;
            suma.car += (ing.car || 0) * factor;
            suma.gra += (ing.gra || 0) * factor;
            contados++;
        });

        const redondear = (v) => Math.round((v / porciones) * 100) / 100;

        formulario.querySelector('[name="cal_por_rec"]').value = redondear(suma.cal);
        formulario.querySelector('[name="pro_por_rec"]').value = redondear(suma.pro);
        formulario.querySelector('[name="car_por_rec"]').value = redondear(suma.car);
        formulario.querySelector('[name="gra_por_rec"]').value = redondear(suma.gra);

        // Decir siempre qué quedó fuera: un número incompleto presentado como
        // completo es peor que no tener número.
        let texto = `Calculado sobre ${contados} ingrediente(s), dividido entre ${porciones} porción(es).`;

        if (ignorados.length) {
            texto += ` Quedaron fuera ${ignorados.length}: ${ignorados.join(', ')}.`
                   + ' Revisa el resultado antes de guardarlo.';
        }

        aviso.textContent = texto;
        aviso.classList.remove('hidden');
    });
}
