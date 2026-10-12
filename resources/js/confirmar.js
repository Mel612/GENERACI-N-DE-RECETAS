/**
 * Ventana de confirmación antes de eliminar.
 *
 * Reemplaza al confirm() del navegador en cualquier formulario que tenga
 * data-confirmar="mensaje". Opcionales:
 *   data-confirmar-titulo  (por defecto "¿Estás seguro?")
 *   data-confirmar-boton   (por defecto "Sí, eliminar")
 *
 * Usa <dialog>: el navegador ya se encarga de atrapar el foco, cerrar con
 * Escape y bloquear lo de atrás. Si este script no carga, el formulario se
 * envía sin preguntar, igual que si no tuviera el atributo.
 */

let dialogo = null;
let formularioPendiente = null;
let botonPendiente = null;

function crearDialogo() {
    dialogo = document.createElement('dialog');
    dialogo.setAttribute('aria-labelledby', 'confirmar-titulo');
    dialogo.setAttribute('aria-describedby', 'confirmar-mensaje');
    dialogo.className = 'm-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl border-2 border-error-400 bg-masa-850 p-0 '
        + 'text-crema-100 shadow-2xl backdrop:bg-black/55';

    dialogo.innerHTML = `
        <div class="p-6 sm:p-8">
            <div class="flex items-start gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-error-400/12 text-error-400">
                    <svg viewBox="0 0 24 24" class="h-8 w-8" fill="currentColor" aria-hidden="true">
                        <path d="M12 2L1 21h22L12 2zm1 15h-2v-2h2v2zm0-4h-2V9h2v4z"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <h2 id="confirmar-titulo" class="text-2xl font-semibold leading-tight text-crema-50"></h2>
                    <p id="confirmar-mensaje" class="mt-2 text-lg leading-relaxed text-crema-300"></p>
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" data-cancelar
                        class="rounded-full border border-linea-fuerte px-6 py-3 text-base font-medium text-crema-100 transition hover:bg-masa-800">
                    Cancelar
                </button>
                <button type="button" data-aceptar
                        class="rounded-full bg-error-400 px-6 py-3 text-base font-semibold text-white transition hover:opacity-90"></button>
            </div>
        </div>`;

    dialogo.querySelector('[data-cancelar]').addEventListener('click', () => dialogo.close());

    dialogo.querySelector('[data-aceptar]').addEventListener('click', () => {
        const formulario = formularioPendiente;
        const boton = botonPendiente;
        formularioPendiente = null;
        dialogo.close();

        // submit() no envía el name/value del botón pulsado, y hay
        // formularios que lo usan (el motivo al vaciar la despensa)
        if (formulario && boton?.name) {
            const oculto = document.createElement('input');
            oculto.type  = 'hidden';
            oculto.name  = boton.name;
            oculto.value = boton.value;
            formulario.appendChild(oculto);
        }

        formulario?.submit();   // submit() no vuelve a disparar el evento: no se pregunta dos veces
    });

    // Clic en el fondo oscuro = cancelar
    dialogo.addEventListener('click', (evento) => {
        if (evento.target === dialogo) dialogo.close();
    });

    dialogo.addEventListener('close', () => { formularioPendiente = null; botonPendiente = null; });

    document.body.appendChild(dialogo);
}

function preguntar(formulario, boton) {
    if (! dialogo) crearDialogo();

    formularioPendiente = formulario;
    botonPendiente = boton;
    dialogo.querySelector('#confirmar-titulo').textContent = formulario.dataset.confirmarTitulo || '¿Estás seguro?';
    dialogo.querySelector('#confirmar-mensaje').textContent = formulario.dataset.confirmar;
    dialogo.querySelector('[data-aceptar]').textContent = formulario.dataset.confirmarBoton || 'Sí, eliminar';

    dialogo.showModal();
    // El foco empieza en Cancelar: un Enter apurado no borra nada
    dialogo.querySelector('[data-cancelar]').focus();
}

export default function iniciarConfirmaciones() {
    document.addEventListener('submit', (evento) => {
        const formulario = evento.target;
        if (! (formulario instanceof HTMLFormElement) || ! formulario.dataset.confirmar) return;

        evento.preventDefault();
        preguntar(formulario, evento.submitter);
    });
}
