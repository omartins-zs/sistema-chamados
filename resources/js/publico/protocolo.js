export const PROTOCOLO_REGEX = /^CHM-\d{4}-\d{6}$/;

/**
 * @param {string} valor
 * @returns {string}
 */
export function normalizarProtocolo(valor) {
    return String(valor ?? '').trim().toUpperCase();
}

/**
 * @param {string} valor
 * @returns {boolean}
 */
export function validarFormatoProtocolo(valor) {
    return PROTOCOLO_REGEX.test(normalizarProtocolo(valor));
}

/**
 * @param {number} [ano]
 * @returns {string}
 */
export function protocoloDemonstracao(ano = new Date().getFullYear()) {
    return `CHM-${ano}-000002`;
}
