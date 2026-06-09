import { describe, expect, it } from 'vitest';
import {
    normalizarProtocolo,
    protocoloDemonstracao,
    PROTOCOLO_REGEX,
    validarFormatoProtocolo,
} from '../../resources/js/publico/protocolo.js';

describe('protocolo público', () => {
    it('normaliza para maiúsculas e remove espaços', () => {
        expect(normalizarProtocolo('  chm-2026-000001  ')).toBe('CHM-2026-000001');
        expect(normalizarProtocolo(null)).toBe('');
        expect(normalizarProtocolo(undefined)).toBe('');
    });

    it('valida formato CHM-AAAA-NNNNNN', () => {
        expect(validarFormatoProtocolo('CHM-2026-000001')).toBe(true);
        expect(validarFormatoProtocolo('chm-2026-000001')).toBe(true);
        expect(validarFormatoProtocolo('INVALIDO')).toBe(false);
        expect(validarFormatoProtocolo('CHM-26-1')).toBe(false);
    });

    it('expõe regex alinhada ao backend', () => {
        expect(PROTOCOLO_REGEX.test('CHM-2026-000002')).toBe(true);
    });

    it('gera protocolo demo do seeder', () => {
        expect(protocoloDemonstracao(2026)).toBe('CHM-2026-000002');
    });
});
