import { expect, test } from '@playwright/test';
import { dadosChamadoPublico } from '../helpers/dados';
import {
    aguardarTabelaChamados,
    aguardarVisualizacaoChamado,
    clicarCriarChamado,
    clicarPdfERespostaBinaria,
    preencherFormularioChamadoAdmin,
} from '../helpers/filament';

test.describe.configure({ mode: 'serial' });

test.describe('CRUD admin de chamados', () => {
    test.setTimeout(120_000);

    test('cria chamado pela página de criação', async ({ page }) => {
        const dados = dadosChamadoPublico();

        await page.goto('/admin/chamados/create');

        await expect(page.getByRole('heading', { name: 'Novo Chamado' })).toBeVisible();
        await preencherFormularioChamadoAdmin(page, dados);
        await clicarCriarChamado(page);
        await aguardarVisualizacaoChamado(page);
        await expect(page.locator('.fi-header-heading')).toHaveText(dados.titulo);
        await expect(page.getByText(dados.nome_solicitante).first()).toBeVisible();
    });

    test('edita e exclui chamado criado no admin', async ({ page }) => {
        const dados = dadosChamadoPublico();
        const tituloEditado = `${dados.titulo} — editado`;

        await page.goto('/admin/chamados/create');
        await preencherFormularioChamadoAdmin(page, dados);
        await clicarCriarChamado(page);
        await aguardarVisualizacaoChamado(page);

        await page.getByRole('link', { name: 'Editar' }).click();
        await expect(page).toHaveURL(/\/admin\/chamados\/\d+\/edit$/);

        await page.getByLabel('Título do chamado').fill(tituloEditado);
        await page.getByRole('button', { name: 'Salvar' }).click();

        await expect(page.getByLabel('Título do chamado')).toHaveValue(tituloEditado);

        await page.getByRole('button', { name: 'Excluir' }).click();
        await page.getByRole('button', { name: 'Excluir' }).nth(1).click({ force: true });

        await page.waitForURL(/\/admin\/chamados$/, { timeout: 30_000 });
        await expect(page.getByText(tituloEditado)).not.toBeVisible();
    });

    test('exporta chamados pelo painel', async ({ page }) => {
        await page.goto('/admin/chamados');
        await aguardarTabelaChamados(page);
        await expect(page.getByRole('button', { name: 'Exportar' })).toBeEnabled();

        await page.getByRole('button', { name: 'Exportar' }).click();
        await expect(page.getByRole('heading', { name: 'Exportar Chamados' })).toBeVisible({ timeout: 15_000 });
        await page.getByRole('button', { name: 'Exportar' }).last().click();

        const notificacoes = page.getByRole('button', { name: 'Abrir notificações' });
        await expect(notificacoes).toBeVisible({ timeout: 60_000 });
        await notificacoes.click();
        await expect(page.getByText(/exportação de chamados foi concluída/i).first()).toBeVisible({ timeout: 30_000 });
    });

    test('gera relatório PDF da listagem', async ({ page }) => {
        test.setTimeout(240_000);
        await page.goto('/admin/chamados');
        await aguardarTabelaChamados(page);

        const corpo = await clicarPdfERespostaBinaria(page, 'Relatório PDF');
        expect(corpo.slice(0, 4).toString()).toBe('%PDF');
    });

    test('gera PDF individual na visualização do chamado', async ({ page }) => {
        const dados = dadosChamadoPublico();

        await page.goto('/admin/chamados/create');
        await preencherFormularioChamadoAdmin(page, dados);
        await clicarCriarChamado(page);
        await aguardarVisualizacaoChamado(page);

        const corpo = await clicarPdfERespostaBinaria(page, 'PDF');
        expect(corpo.slice(0, 4).toString()).toBe('%PDF');
    });
});
