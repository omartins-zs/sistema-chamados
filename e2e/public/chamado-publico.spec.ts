import { expect, test } from '@playwright/test';
import { dadosChamadoPublico, protocoloDemo } from '../helpers/dados';

test.describe('Área pública', () => {
    test('redireciona a home para abrir chamado', async ({ page }) => {
        await page.goto('/');

        await expect(page).toHaveURL(/\/chamados\/novo$/);
        await expect(page.getByRole('heading', { name: /abrir chamado/i })).toBeVisible();
    });

    test('exibe menu de navegação pública', async ({ page }) => {
        await page.goto('/chamados/novo');

        await expect(page.getByRole('link', { name: 'Abrir Chamado' }).first()).toBeVisible();
        await expect(page.getByRole('link', { name: 'Consultar Chamado' }).first()).toBeVisible();
    });

    test('valida descrição curta ao abrir chamado', async ({ page }) => {
        const dados = dadosChamadoPublico();

        await page.goto('/chamados/novo');
        await page.getByLabel('Nome do Solicitante').fill(dados.nome_solicitante);
        await page.getByLabel('E-mail', { exact: true }).fill(dados.email_solicitante);
        await page.getByLabel(/telefone/i).fill(dados.telefone_solicitante);
        await page.getByLabel('Título do Chamado').fill(dados.titulo);
        await page.getByLabel('Descrição Detalhada').fill('curta');
        await page.locator('#complexidade').selectOption(dados.complexidade);
        await page.locator('#setor_id').selectOption({ index: 1 });
        await page.getByTestId('btn-abrir-chamado').click();

        await expect(page).toHaveURL(/\/chamados\/novo$/);
        await expect(page.getByRole('alert')).toContainText(/10 caracteres/i);
    });

    test('cria chamado e exibe protocolo na tela de sucesso', async ({ page }) => {
        const dados = dadosChamadoPublico();

        await page.goto('/chamados/novo');
        await page.getByLabel('Nome do Solicitante').fill(dados.nome_solicitante);
        await page.getByLabel('E-mail', { exact: true }).fill(dados.email_solicitante);
        await page.getByLabel(/telefone/i).fill(dados.telefone_solicitante);
        await page.getByLabel('Título do Chamado').fill(dados.titulo);
        await page.getByLabel('Descrição Detalhada').fill(dados.descricao);
        await page.locator('#complexidade').selectOption(dados.complexidade);
        await page.locator('#setor_id').selectOption({ index: 1 });
        await page.getByTestId('btn-abrir-chamado').click();

        await expect(page.getByRole('heading', { name: /registrado com sucesso/i })).toBeVisible();
        await expect(page.getByTestId('protocolo-chamado')).toHaveText(/CHM-\d{4}-\d{6}/);
        await expect(page.getByText(dados.nome_solicitante)).toBeVisible();
    });

    test('consulta chamado demo do seeder', async ({ page }) => {
        const protocolo = protocoloDemo();

        await page.goto('/chamados/consultar');
        await page.getByLabel('Protocolo').fill(protocolo);
        await page.getByTestId('btn-consultar-chamado').click();

        await expect(page.getByText(protocolo)).toBeVisible();
        await expect(page.getByRole('heading', { name: /situação atual do chamado/i })).toBeVisible();
    });

    test('rejeita protocolo inválido na consulta', async ({ page }) => {
        await page.goto('/chamados/consultar');
        await page.getByLabel('Protocolo').fill('INVALIDO');
        await page.getByTestId('btn-consultar-chamado').click();

        await expect(page).toHaveURL(/\/chamados\/consultar$/);
        await expect(page.getByRole('alert')).toBeVisible();
    });
});
