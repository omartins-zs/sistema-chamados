const STORAGE_KEY = 'tema-publico';

/**
 * @param {string | null} salvo
 * @param {boolean} prefersDark
 * @returns {'light' | 'dark'}
 */
export function resolverTemaPreferido(salvo, prefersDark) {
    if (salvo === 'dark') {
        return 'dark';
    }

    if (salvo === 'light') {
        return 'light';
    }

    return prefersDark ? 'dark' : 'light';
}

/**
 * @param {'light' | 'dark'} tema
 */
export function aplicarTema(tema) {
    document.documentElement.classList.toggle('dark', tema === 'dark');

    const meta = document.querySelector('meta[name="theme-color"]');

    if (meta) {
        meta.setAttribute('content', tema === 'dark' ? '#0f172a' : '#00468a');
    }
}

function atualizarIconesTema(tema) {
    const botao = document.getElementById('btn-alternar-tema');

    if (! botao) {
        return;
    }

    const iconeClaro = botao.querySelector('[data-icone="claro"]');
    const iconeEscuro = botao.querySelector('[data-icone="escuro"]');

    if (iconeClaro && iconeEscuro) {
        iconeClaro.classList.toggle('hidden', tema === 'dark');
        iconeEscuro.classList.toggle('hidden', tema !== 'dark');
    }

    const rotulo = tema === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro';
    botao.setAttribute('aria-label', rotulo);
    botao.setAttribute('title', rotulo);
}

/**
 * @returns {'light' | 'dark'}
 */
export function alternarTema() {
    const isDark = document.documentElement.classList.contains('dark');
    const novo = isDark ? 'light' : 'dark';

    localStorage.setItem(STORAGE_KEY, novo);
    aplicarTema(novo);
    atualizarIconesTema(novo);

    return novo;
}

export function inicializarTema() {
    let salvo = null;

    try {
        salvo = localStorage.getItem(STORAGE_KEY);
    } catch {
        salvo = null;
    }

    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const tema = resolverTemaPreferido(salvo, prefersDark);

    aplicarTema(tema);
    atualizarIconesTema(tema);

    const botao = document.getElementById('btn-alternar-tema');

    if (botao) {
        botao.addEventListener('click', alternarTema);
    }
}
