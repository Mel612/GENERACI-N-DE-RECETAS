import axios from 'axios';
import iniciarFormularioRecetas from './recetas';
import iniciarBuscadorIngredientes from './despensa';
import iniciarFormularioIngrediente from './ingredientes';
import iniciarFormularioUnidad from './unidades';
import iniciarConfirmaciones from './confirmar';

// Ventana "¿Estás seguro?" en los formularios con data-confirmar.
iniciarConfirmaciones();

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/**
 * Aparicion suave de las secciones al entrar en pantalla.
 *
 * El contenido es visible por defecto en el CSS. Solo aqui, y solo si el
 * navegador soporta IntersectionObserver, se marca el documento para
 * ocultarlo y revelarlo con animacion. Si este script no corre, la pagina
 * se ve completa igual.
 */
// Formulario de recetas, solo si esa pantalla esta abierta.
document.addEventListener('DOMContentLoaded', iniciarFormularioRecetas);

// Buscador de ingredientes de la despensa, solo si esa pantalla esta abierta.
document.addEventListener('DOMContentLoaded', iniciarBuscadorIngredientes);

// Formulario del catálogo de ingredientes, solo si esa pantalla esta abierta.
document.addEventListener('DOMContentLoaded', iniciarFormularioIngrediente);

// Formulario de unidades de medida, solo si esa pantalla esta abierta.
document.addEventListener('DOMContentLoaded', iniciarFormularioUnidad);

document.addEventListener('DOMContentLoaded', () => {
    const elementos = document.querySelectorAll('.surgir-scroll');

    // El diseño actual no anima las secciones al hacer scroll: el CSS ya no
    // define el efecto. Se deja la función por si vuelve, pero no marca nada.
    if (! document.documentElement.dataset.animarSecciones) return;

    if (! elementos.length || ! ('IntersectionObserver' in window)) return;

    // Quien pidio menos movimiento en su sistema no recibe animaciones.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    document.documentElement.classList.add('con-animacion');

    const observador = new IntersectionObserver((entradas) => {
        entradas.forEach((entrada) => {
            if (! entrada.isIntersecting) return;
            entrada.target.classList.add('surgio');
            observador.unobserve(entrada.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

    elementos.forEach((el) => observador.observe(el));

    // Red de seguridad: si algo impide que el observador dispare,
    // a los 3 segundos se muestra todo de todas formas.
    setTimeout(() => elementos.forEach((el) => el.classList.add('surgio')), 3000);
});

/**
 * Cierra el menu desplegable de catalogos al hacer clic fuera o con Escape.
 * El desplegable en si es un <details> nativo: funciona aunque esto no corra.
 */
document.addEventListener('click', (evento) => {
    document.querySelectorAll('details.menu[open]').forEach((menu) => {
        if (! menu.contains(evento.target)) menu.open = false;
    });
});

document.addEventListener('keydown', (evento) => {
    if (evento.key !== 'Escape') return;
    document.querySelectorAll('details.menu[open]').forEach((menu) => { menu.open = false; });
});

/**
 * Campo de imagen: muestra el nombre del archivo elegido y su vista previa.
 *
 * Es una mejora, no un requisito. Sin esto el campo sigue funcionando: se
 * elige el archivo igual, solo que no se ve hasta guardar.
 *
 * El listener va en el documento y no en cada campo, porque asi funciona
 * tambien con campos que aparezcan despues.
 */
document.addEventListener('change', (evento) => {
    const entrada = evento.target.closest('[data-entrada]');
    if (! entrada) return;

    const campo = entrada.closest('[data-campo-imagen]');
    if (! campo) return;

    const archivo = entrada.files?.[0];
    const rotulo  = campo.querySelector('[data-nombre-archivo]');
    const imagen  = campo.querySelector('[data-vista-previa]');
    const hueco   = campo.querySelector('[data-hueco]');

    if (! archivo) {
        if (rotulo) rotulo.textContent = '';
        return;
    }

    // Un archivo de 40 KB no debe mostrarse como «0.0 MB»
    const peso = archivo.size >= 1048576
        ? `${(archivo.size / 1048576).toFixed(1)} MB`
        : `${Math.max(1, Math.round(archivo.size / 1024))} KB`;

    if (rotulo) rotulo.textContent = `${archivo.name} (${peso})`;

    if (! imagen || ! hueco) return;

    // La URL anterior se libera: cada createObjectURL retiene el archivo
    // en memoria hasta que se revoca.
    if (imagen.dataset.temporal) URL.revokeObjectURL(imagen.src);

    imagen.src = URL.createObjectURL(archivo);
    imagen.dataset.temporal = '1';
    imagen.classList.remove('hidden');
    hueco.classList.add('hidden');
});

/**
 * Tema claro (la hoja) u oscuro (la pizarra).
 *
 * El tema guardado ya lo aplicó el script en línea del <head>; aquí solo
 * se atiende el botón. La elección se guarda en este navegador.
 */
const sincronizarBotonesTema = () => {
    const oscuro = document.documentElement.dataset.tema === 'oscuro';
    document.querySelectorAll('[data-cambiar-tema]')
        .forEach((boton) => boton.setAttribute('aria-pressed', String(oscuro)));
};

document.addEventListener('DOMContentLoaded', sincronizarBotonesTema);

document.addEventListener('click', (evento) => {
    if (! evento.target.closest('[data-cambiar-tema]')) return;

    const raiz = document.documentElement;
    const oscuro = raiz.dataset.tema !== 'oscuro';

    // La transición de colores solo durante el cambio
    raiz.classList.add('cambiando-tema');
    setTimeout(() => raiz.classList.remove('cambiando-tema'), 450);

    if (oscuro) raiz.dataset.tema = 'oscuro';
    else delete raiz.dataset.tema;

    try { localStorage.setItem('tema', oscuro ? 'oscuro' : 'claro'); } catch (e) {}

    sincronizarBotonesTema();
});

/**
 * La foto de fondo del acceso se desplaza unos píxeles siguiendo al
 * puntero. Es adorno: sin esto la foto queda quieta y todo funciona igual.
 * No corre con pantallas táctiles ni para quien pidió menos movimiento.
 */
document.addEventListener('DOMContentLoaded', () => {
    const fotos = document.querySelectorAll('[data-paralaje]');
    if (! fotos.length) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (! window.matchMedia('(pointer: fine)').matches) return;

    let pendiente = false;
    document.addEventListener('pointermove', (evento) => {
        if (pendiente) return;
        pendiente = true;
        requestAnimationFrame(() => {
            const x = (evento.clientX / window.innerWidth - 0.5) * -24;
            const y = (evento.clientY / window.innerHeight - 0.5) * -16;
            fotos.forEach((foto) => {
                foto.style.setProperty('--px', `${x.toFixed(1)}px`);
                foto.style.setProperty('--py', `${y.toFixed(1)}px`);
            });
            pendiente = false;
        });
    });
});


/** Botones de «marcar todo» y «desmarcar todo» en el generador. */
document.addEventListener('click', (evento) => {
    const boton = evento.target.closest('[data-marcar]');
    if (! boton) return;

    const marcar = boton.dataset.marcar === 'todos';
    document.querySelectorAll('#lista-despensa input[type="checkbox"]')
        .forEach((casilla) => { casilla.checked = marcar; });
});
