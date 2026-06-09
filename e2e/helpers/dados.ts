import { protocoloDemonstracao } from '../../resources/js/publico/protocolo.js';

export const credenciais = {
    admin: {
        email: process.env.PLAYWRIGHT_ADMIN_EMAIL ?? 'admin@admin.com',
        password: process.env.PLAYWRIGHT_ADMIN_PASSWORD ?? 'password',
    },
    tecnico: {
        email: process.env.PLAYWRIGHT_TECNICO_EMAIL ?? 'marcos-silva@chamados.local',
        password: process.env.PLAYWRIGHT_TECNICO_PASSWORD ?? 'password',
    },
};

export function protocoloDemo(): string {
    return protocoloDemonstracao();
}

export function dadosChamadoPublico() {
    const sufixo = Date.now();

    return {
        nome_solicitante: 'Teste E2E Playwright',
        email_solicitante: `e2e.${sufixo}@example.com`,
        telefone_solicitante: '(11) 98888-7777',
        titulo: `Chamado E2E ${sufixo}`,
        descricao: 'Descrição automatizada para validar o fluxo público de abertura.',
        complexidade: 'media',
    };
}
