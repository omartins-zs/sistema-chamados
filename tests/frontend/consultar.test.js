import { describe, expect, it, beforeEach } from 'vitest';
import { inicializarFormularioConsulta } from '../../resources/js/publico/consultar.js';

describe('formulário de consulta', () => {
    beforeEach(() => {
        document.body.innerHTML = `
            <form>
                <input id="protocolo" type="text" value="" />
            </form>
        `;
    });

    it('converte protocolo para maiúsculas durante digitação', () => {
        inicializarFormularioConsulta(document);
        const input = document.querySelector('#protocolo');

        input.value = 'chm-2026-000001';
        input.dispatchEvent(new Event('input', { bubbles: true }));

        expect(input.value).toBe('CHM-2026-000001');
    });

    it('ignora quando campo protocolo não existe', () => {
        document.body.innerHTML = '<form></form>';

        expect(() => inicializarFormularioConsulta(document)).not.toThrow();
    });
});
