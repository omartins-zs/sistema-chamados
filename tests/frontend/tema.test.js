import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import {
    alternarTema,
    aplicarTema,
    inicializarTema,
    resolverTemaPreferido,
} from '../../resources/js/publico/tema.js';

describe('tema público', () => {
    beforeEach(() => {
        document.documentElement.classList.remove('dark');
        document.head.innerHTML = '<meta name="theme-color" content="#415A77">';
        document.body.innerHTML = `
            <button type="button" id="btn-alternar-tema" aria-label="Alternar tema">
                <svg data-icone="claro"></svg>
                <svg data-icone="escuro" class="hidden"></svg>
            </button>
        `;
        localStorage.clear();
        vi.spyOn(window, 'matchMedia').mockReturnValue({
            matches: false,
        });
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('resolve tema escuro quando salvo', () => {
        expect(resolverTemaPreferido('dark', false)).toBe('dark');
    });

    it('resolve tema claro quando salvo', () => {
        expect(resolverTemaPreferido('light', true)).toBe('light');
    });

    it('usa preferência do sistema quando não há valor salvo', () => {
        expect(resolverTemaPreferido(null, true)).toBe('dark');
        expect(resolverTemaPreferido(null, false)).toBe('light');
    });

    it('aplica classe dark e atualiza meta theme-color', () => {
        aplicarTema('dark');
        expect(document.documentElement.classList.contains('dark')).toBe(true);
        expect(document.querySelector('meta[name="theme-color"]').getAttribute('content')).toBe('#0f172a');

        aplicarTema('light');
        expect(document.documentElement.classList.contains('dark')).toBe(false);
        expect(document.querySelector('meta[name="theme-color"]').getAttribute('content')).toBe('#00468a');
    });

    it('alterna tema, persiste no localStorage e atualiza ícones', () => {
        aplicarTema('light');

        const novo = alternarTema();

        expect(novo).toBe('dark');
        expect(localStorage.getItem('tema-publico')).toBe('dark');
        expect(document.documentElement.classList.contains('dark')).toBe(true);

        const botao = document.getElementById('btn-alternar-tema');
        expect(botao.getAttribute('aria-label')).toBe('Ativar tema claro');
        expect(botao.querySelector('[data-icone="claro"]').classList.contains('hidden')).toBe(true);
        expect(botao.querySelector('[data-icone="escuro"]').classList.contains('hidden')).toBe(false);
    });

    it('inicializa tema salvo e registra clique no botão', () => {
        localStorage.setItem('tema-publico', 'dark');

        inicializarTema();

        expect(document.documentElement.classList.contains('dark')).toBe(true);

        const botao = document.getElementById('btn-alternar-tema');
        botao.click();

        expect(localStorage.getItem('tema-publico')).toBe('light');
        expect(document.documentElement.classList.contains('dark')).toBe(false);
    });

    it('inicializa com preferência do sistema quando localStorage falha', () => {
        vi.spyOn(window, 'matchMedia').mockReturnValue({
            matches: true,
        });
        vi.spyOn(Storage.prototype, 'getItem').mockImplementation(() => {
            throw new Error('storage indisponível');
        });

        inicializarTema();

        expect(document.documentElement.classList.contains('dark')).toBe(true);
    });
});
