import { normalizarProtocolo } from './protocolo.js';

/**
 * @param {HTMLInputElement} input
 */
export function aplicarMascaraProtocolo(input) {
    input.addEventListener('input', () => {
        input.value = normalizarProtocolo(input.value);
    });
}

/**
 * @param {Document|ParentNode} [root]
 */
export function inicializarFormularioConsulta(root = document) {
    const input = root.querySelector('#protocolo');

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    aplicarMascaraProtocolo(input);
}
