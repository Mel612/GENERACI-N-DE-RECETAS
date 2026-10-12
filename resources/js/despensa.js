/**
 * Buscador de ingredientes del formulario "Agregar a la despensa".
 *
 * Filtra el catálogo mientras se escribe (por nombre o sinónimo, sin importar
 * tildes). Si lo que se busca no está, ofrece consultarlo con la IA: el
 * servidor solo lo da de alta si es comestible.
 *
 * El código elegido viaja en un campo oculto; el texto visible es solo para
 * buscar. Si no se eligió nada de la lista, el formulario no se envía.
 */

import { quitarNumeros } from './ingredientes';

const MAX_RESULTADOS = 8;

/** Segundos que la notificación queda en pantalla antes de irse sola. */
const DURACION_NOTIFICACION = 7000;

const ICONOS = {
    cargando:  '<path d="M12 3a9 9 0 109 9h-2.2A6.8 6.8 0 1112 5.2V3z"/>',
    aprobado:  '<path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1.3 14.3l-4-4 1.4-1.4 2.6 2.6 5.6-5.6L17.7 9l-7 7.3z"/>',
    rechazado: '<path d="M12 2a10 10 0 100 20 10 10 0 000-20zm4.2 12.8l-1.4 1.4L12 13.4l-2.8 2.8-1.4-1.4 2.8-2.8-2.8-2.8 1.4-1.4 2.8 2.8 2.8-2.8 1.4 1.4-2.8 2.8 2.8 2.8z"/>',
    error:     '<path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
};

const TONOS = {
    cargando:  'text-oro-400',
    aprobado:  'text-exito-400',
    rechazado: 'text-error-400',
    error:     'text-alerta-400',
};

let temporizador = null;
let temporizadorSalida = null;

/**
 * Notificación emergente en la esquina de la pantalla.
 *
 * Hay una sola: la de "Verificando…" se transforma en el resultado en lugar
 * de apilar una nueva encima.
 */
function notificar(estado, titulo, detalle) {
    let caja = document.getElementById('notificacion-despensa');

    if (! caja) {
        caja = document.createElement('div');
        caja.id = 'notificacion-despensa';
        caja.className = 'fixed inset-x-4 bottom-4 z-50 flex items-start gap-3 rounded-2xl border border-linea-fuerte '
            + 'bg-masa-850 p-5 shadow-xl transition duration-200 sm:inset-x-auto sm:right-6 sm:bottom-6 sm:w-[30rem] '
            + 'opacity-0 translate-y-2 motion-reduce:transition-none';
        caja.innerHTML = `
            <svg viewBox="0 0 24 24" class="mt-0.5 h-7 w-7 shrink-0" fill="currentColor" aria-hidden="true"></svg>
            <div class="min-w-0 flex-1">
                <p data-titulo class="text-lg font-semibold leading-snug text-crema-50"></p>
                <p data-detalle class="mt-1.5 text-base leading-relaxed text-crema-300"></p>
            </div>
            <button type="button" class="-m-1 rounded-full p-1 text-crema-500 transition hover:text-crema-100" aria-label="Cerrar notificación">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                    <path d="M18.3 5.7l-1.4-1.4L12 9.2 7.1 4.3 5.7 5.7 10.6 10.6 5.7 15.5l1.4 1.4 4.9-4.9 4.9 4.9 1.4-1.4-4.9-4.9z"/>
                </svg>
            </button>`;
        caja.querySelector('button').addEventListener('click', () => ocultarNotificacion(caja));
        document.body.appendChild(caja);
    }

    const icono = caja.querySelector('svg');
    icono.innerHTML = ICONOS[estado];
    icono.setAttribute('class', `mt-0.5 h-7 w-7 shrink-0 ${TONOS[estado]}`);
    icono.classList.toggle('animate-spin', estado === 'cargando');

    caja.classList.toggle('border-error-400', estado === 'rechazado');
    caja.classList.toggle('border-2', estado === 'rechazado');
    caja.classList.toggle('border-linea-fuerte', estado !== 'rechazado');

    caja.querySelector('[data-titulo]').textContent = titulo;
    caja.querySelector('[data-detalle]').textContent = detalle;

    // Un rechazo se anuncia de inmediato a los lectores de pantalla
    caja.setAttribute('role', estado === 'rechazado' || estado === 'error' ? 'alert' : 'status');
    caja.setAttribute('aria-live', estado === 'rechazado' || estado === 'error' ? 'assertive' : 'polite');

    clearTimeout(temporizadorSalida);
    caja.hidden = false;
    requestAnimationFrame(() => caja.classList.remove('opacity-0', 'translate-y-2'));

    clearTimeout(temporizador);
    if (estado !== 'cargando') {
        const duracion = estado === 'rechazado' ? DURACION_NOTIFICACION * 2 : DURACION_NOTIFICACION;
        temporizador = setTimeout(() => ocultarNotificacion(caja), duracion);
    }
}

function ocultarNotificacion(caja) {
    clearTimeout(temporizador);
    caja.classList.add('opacity-0', 'translate-y-2');
    temporizadorSalida = setTimeout(() => { caja.hidden = true; }, 200);
}

/** Minúsculas, sin tildes y sin espacios de más, para comparar. */
function normalizar(texto) {
    return texto.normalize('NFD').replace(/[̀-ͯ]/g, '')
        .toLowerCase().replace(/\s+/g, ' ').trim();
}

export default function iniciarBuscadorIngredientes() {
    const caja = document.querySelector('[data-buscador-ingredientes]');
    if (! caja || caja.dataset.iniciado === '1') return;
    caja.dataset.iniciado = '1';

    const entrada    = caja.querySelector('input[role="combobox"]');
    const oculto     = caja.querySelector('input[type="hidden"]');
    const lista      = caja.querySelector('[role="listbox"]');
    const aviso      = caja.querySelector('[data-aviso]');
    const formulario = caja.closest('form');
    const urlIa      = caja.dataset.urlIa;

    const catalogo = JSON.parse(caja.querySelector('[data-catalogo]').textContent)
        .map((item) => ({
            ...item,
            claves: [item.nombre, ...item.sinonimos.split(',')].map(normalizar).filter(Boolean),
        }));

    let opciones = [];   // lo que se ve en la lista: ingredientes o la acción de la IA
    let activa   = -1;
    let consultando = false;

    // Lo que la IA ya dijo que no es comida: texto normalizado => motivo.
    // Así no se vuelve a ofrecer la consulta ni se puede enviar.
    const rechazados = new Map();

    // ------------------------------------------------------------ avisos

    function avisar(texto, tono = 'neutro') {
        aviso.textContent = texto;
        aviso.classList.toggle('text-error-400', tono === 'error');
        aviso.classList.toggle('text-exito-400', tono === 'exito');
        aviso.classList.toggle('text-oro-400', tono === 'cargando');
        aviso.classList.toggle('text-crema-500', tono === 'neutro');
        aviso.classList.toggle('flex', tono === 'cargando');
        aviso.classList.toggle('items-center', tono === 'cargando');
        aviso.classList.toggle('gap-1.5', tono === 'cargando');

        const esError = tono === 'error' && texto !== '';
        aviso.classList.toggle('text-xs', ! esError);
        ['text-base', 'font-medium', 'leading-snug', 'rounded-xl', 'border', 'border-error-400/40',
         'bg-error-400/10', 'px-4', 'py-3'].forEach((clase) => aviso.classList.toggle(clase, esError));

        if (tono === 'cargando') {
            const giro = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            giro.setAttribute('viewBox', '0 0 24 24');
            giro.setAttribute('fill', 'currentColor');
            giro.setAttribute('aria-hidden', 'true');
            giro.setAttribute('class', 'h-3.5 w-3.5 shrink-0 animate-spin');
            giro.innerHTML = ICONOS.cargando;
            aviso.prepend(giro);
        }
    }

    /** Deja a la vista por qué no se puede agregar lo que se escribió. */
    function mostrarRechazo(texto) {
        entrada.classList.add('campo-error');
        avisar(`No se puede agregar «${texto}» a la despensa porque no es un alimento. `
            + rechazados.get(normalizar(texto)), 'error');
    }

    // ------------------------------------------------------------- lista

    function buscar(texto) {
        const consulta = normalizar(texto);
        if (! consulta) return [];

        // Orden: el nombre exacto, el nombre que empieza igual, el nombre que
        // lo contiene y recién después lo que coincide solo por sinónimo.
        const puntaje = (item) => {
            const nombre = item.claves[0];
            if (nombre === consulta) return 0;
            if (nombre.startsWith(consulta)) return 1;
            if (nombre.includes(consulta)) return 2;
            if (item.claves.some((c) => c.startsWith(consulta))) return 3;
            if (item.claves.some((c) => c.includes(consulta))) return 4;
            return null;
        };

        return catalogo
            .map((item) => ({ item, puntos: puntaje(item) }))
            .filter(({ puntos }) => puntos !== null)
            .sort((a, b) => a.puntos - b.puntos || a.item.nombre.localeCompare(b.item.nombre, 'es'))
            .slice(0, MAX_RESULTADOS)
            .map(({ item }) => item);
    }

    function exacto(texto) {
        const consulta = normalizar(texto);
        return catalogo.find((item) => item.claves.includes(consulta));
    }

    function abrir() {
        lista.hidden = opciones.length === 0;
        entrada.setAttribute('aria-expanded', String(! lista.hidden));
    }

    function cerrar() {
        lista.hidden = true;
        activa = -1;
        entrada.setAttribute('aria-expanded', 'false');
        entrada.removeAttribute('aria-activedescendant');
    }

    function crearOpcion(id, principal, secundario, esIa = false) {
        const li = document.createElement('li');
        li.id = id;
        li.setAttribute('role', 'option');
        li.className = 'flex cursor-pointer items-baseline justify-between gap-3 px-3.5 py-2 text-sm';
        if (esIa) li.classList.add('border-t', 'border-linea', 'text-oro-400');

        const nombre = document.createElement('span');
        nombre.textContent = principal;
        nombre.className = esIa ? 'font-medium' : 'text-crema-100';

        const detalle = document.createElement('span');
        detalle.textContent = secundario;
        detalle.className = 'shrink-0 text-xs text-crema-500';

        li.append(nombre, detalle);
        return li;
    }

    function dibujar() {
        const texto = entrada.value.trim();
        const resultados = buscar(texto);

        opciones = resultados.map((item) => ({ tipo: 'ingrediente', item }));

        const rechazado = rechazados.has(normalizar(texto));

        if (urlIa && normalizar(texto).length >= 2 && ! exacto(texto) && ! rechazado) {
            opciones.push({ tipo: 'ia', texto });
        }

        lista.replaceChildren(...opciones.map((opcion, i) => opcion.tipo === 'ia'
            ? crearOpcion(`opcion-ingrediente-${i}`, `Buscar «${opcion.texto}» con IA`,
                resultados.length ? '¿No es ninguno?' : 'No está en el catálogo', true)
            : crearOpcion(`opcion-ingrediente-${i}`, opcion.item.nombre, opcion.item.categoria)));

        if (rechazado) {
            mostrarRechazo(texto);
        } else if (texto && ! opciones.length) {
            avisar('No está en el catálogo.', 'neutro');
        }

        activa = opciones.length ? 0 : -1;
        marcarActiva();
        abrir();
    }

    function marcarActiva() {
        [...lista.children].forEach((li, i) => {
            const es = i === activa;
            li.setAttribute('aria-selected', String(es));
            li.classList.toggle('bg-oro-500/12', es);
        });

        if (activa >= 0) {
            entrada.setAttribute('aria-activedescendant', `opcion-ingrediente-${activa}`);
            lista.children[activa]?.scrollIntoView({ block: 'nearest' });
        } else {
            entrada.removeAttribute('aria-activedescendant');
        }
    }

    // ------------------------------------------------------------ elegir

    function elegir(item) {
        entrada.value = item.nombre;
        oculto.value = item.id;
        entrada.classList.remove('campo-error');
        cerrar();
    }

    function elegirOpcion(indice) {
        const opcion = opciones[indice];
        if (! opcion) return;

        if (opcion.tipo === 'ia') {
            consultarIa(opcion.texto);
        } else {
            elegir(opcion.item);
            avisar('');
            formulario.querySelector('#can_inv')?.focus();
        }
    }

    async function consultarIa(texto) {
        if (consultando) return;
        consultando = true;
        cerrar();
        entrada.disabled = true;
        avisar(`Buscando «${texto}»… verificando si es un alimento`, 'cargando');
        notificar('cargando', 'Verificando…', `Revisando si «${texto}» está aprobado como alimento.`);

        try {
            const { data } = await window.axios.post(urlIa, { texto });

            // Queda en el catálogo local para no volver a consultarlo
            if (! catalogo.some((item) => item.id === data.id)) {
                catalogo.push({
                    ...data,
                    sinonimos: '',
                    claves: [normalizar(data.nombre), normalizar(texto)],
                });
            }

            elegir(data);
            avisar('');
            notificar('aprobado', 'Aprobado como alimento', data.nuevo
                ? `«${data.nombre}» (${data.categoria}) se agregó al catálogo. Ahora indica la cantidad.`
                : `«${data.nombre}» (${data.categoria}) ya estaba en el catálogo. Ahora indica la cantidad.`);
            entrada.disabled = false;
            formulario.querySelector('#can_inv')?.focus();
        } catch (error) {
            const estado = error.response?.status;
            const datos  = error.response?.data;
            const mensaje = estado === 429
                ? 'Hiciste muchas consultas seguidas. Espera un minuto.'
                : datos?.mensaje
                    ?? datos?.errors?.texto?.[0]
                    ?? 'No pudimos consultar a la IA. Intenta de nuevo.';

            oculto.value = '';
            entrada.disabled = false;

            if (datos?.rechazado) {
                rechazados.set(normalizar(texto), mensaje);
                notificar('rechazado', `«${texto}» no está aprobado como alimento`, mensaje);
                mostrarRechazo(texto);
            } else {
                notificar('error', 'No se pudo verificar', mensaje);
                avisar('');
            }

            entrada.select();
            entrada.focus();
            cerrar();   // el focus vuelve a abrir la lista; tras un rechazo no hace falta
        } finally {
            consultando = false;
        }
    }

    // ----------------------------------------------------------- eventos

    entrada.addEventListener('input', () => {
        oculto.value = '';
        const teniaNumeros = quitarNumeros(entrada);
        entrada.classList.remove('campo-error');
        avisar('');
        dibujar();

        if (teniaNumeros) {
            avisar('El nombre del ingrediente no puede llevar números.', 'error');
        }
    });

    entrada.addEventListener('focus', () => {
        if (! oculto.value && entrada.value.trim()) dibujar();
    });

    entrada.addEventListener('keydown', (evento) => {
        if (evento.key === 'ArrowDown' || evento.key === 'ArrowUp') {
            evento.preventDefault();
            if (lista.hidden) { dibujar(); return; }
            if (! opciones.length) return;

            const paso = evento.key === 'ArrowDown' ? 1 : -1;
            activa = (activa + paso + opciones.length) % opciones.length;
            marcarActiva();
        } else if (evento.key === 'Enter' && ! lista.hidden && activa >= 0) {
            evento.preventDefault();
            elegirOpcion(activa);
        } else if (evento.key === 'Escape') {
            cerrar();
        }
    });

    // mousedown y no click: el click llega después del blur, cuando la lista ya se cerró
    lista.addEventListener('mousedown', (evento) => {
        const li = evento.target.closest('[role="option"]');
        if (! li) return;
        evento.preventDefault();
        elegirOpcion([...lista.children].indexOf(li));
    });

    entrada.addEventListener('blur', () => {
        // Si escribió el nombre completo sin elegirlo de la lista, se toma igual
        const coincide = ! oculto.value && exacto(entrada.value);
        if (coincide) elegir(coincide);
        cerrar();
    });

    formulario.addEventListener('submit', (evento) => {
        if (oculto.value) return;

        const coincide = exacto(entrada.value);
        if (coincide) { elegir(coincide); return; }

        evento.preventDefault();
        entrada.focus();

        if (rechazados.has(normalizar(entrada.value))) {
            cerrar();
            mostrarRechazo(entrada.value.trim());
            notificar('rechazado', `«${entrada.value.trim()}» no está aprobado como alimento`,
                rechazados.get(normalizar(entrada.value)));
            return;
        }

        entrada.classList.add('campo-error');
        avisar(urlIa
            ? 'Elige un ingrediente de la lista o búscalo con la IA.'
            : 'Elige un ingrediente de la lista.', 'error');
        dibujar();
    });
}
