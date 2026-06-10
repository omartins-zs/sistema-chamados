import { readFileSync } from 'node:fs';
import { expect, type Page } from '@playwright/test';
import type { dadosChamadoPublico } from './dados';

export async function aguardarTabelaChamados(page: Page): Promise<void> {
    await page.getByRole('table').waitFor();
    await page.getByRole('button', { name: 'Exportar' }).waitFor({ state: 'visible' });
}

export async function selecionarSetorAdmin(page: Page, nomeSetor = 'Desenvolvimento'): Promise<void> {
    const campo = page.locator('[data-field-wrapper]').filter({ hasText: 'Setor responsável' });
    await campo.scrollIntoViewIfNeeded();
    await campo.locator('button').first().click();
    await page.getByRole('option', { name: nomeSetor }).click();
}

export async function preencherFormularioChamadoAdmin(
    page: Page,
    dados: ReturnType<typeof dadosChamadoPublico>,
): Promise<void> {
    await page.getByLabel('Nome do solicitante').fill(dados.nome_solicitante);
    await page.getByLabel('E-mail').fill(dados.email_solicitante);
    await page.getByLabel(/telefone/i).fill(dados.telefone_solicitante);
    await page.getByLabel('Título do chamado').fill(dados.titulo);
    await page.getByLabel('Descrição detalhada').fill(dados.descricao);
    await page.getByLabel('Complexidade').selectOption('Média');
    await selecionarSetorAdmin(page);
}

export async function clicarCriarChamado(page: Page): Promise<void> {
    const botao = page.getByRole('button', { name: 'Criar', exact: true });
    await expect(botao).toBeEnabled();
    await botao.click();
}

export async function aguardarVisualizacaoChamado(page: Page): Promise<void> {
    await page.waitForURL(/\/admin\/chamados\/\d+$/, { timeout: 60_000 });
}

export async function clicarPdfERespostaBinaria(page: Page, nomeBotao: string): Promise<Buffer> {
    const alvo = page.getByRole('link', { name: nomeBotao }).or(page.getByRole('button', { name: nomeBotao }));

    const [download] = await Promise.all([
        page.waitForEvent('download', { timeout: 180_000 }),
        alvo.click(),
    ]);

    const caminho = await download.path();

    if (! caminho) {
        throw new Error('Download do PDF não gerou arquivo.');
    }

    return readFileSync(caminho);
}
