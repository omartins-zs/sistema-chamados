import { expect, test } from '@playwright/test';
import { protocoloDemo } from '../helpers/dados';

test.describe('Painel administrativo', () => {
    test('carrega dashboard autenticado', async ({ page }) => {
        await page.goto('/admin');

        await expect(page.getByRole('heading', { name: /painel/i })).toBeVisible();
        await expect(page.getByText('Resumo Geral').first()).toBeVisible({ timeout: 30_000 });
    });

    test('exibe botão Novo Chamado no topbar', async ({ page }) => {
        await page.goto('/admin');

        await expect(page.locator('.fi-topbar-novo-chamado').getByRole('button', { name: 'Novo Chamado' })).toBeVisible();
    });

    test('abre modal de novo chamado', async ({ page }) => {
        await page.goto('/admin');
        await page.locator('.fi-topbar-novo-chamado').getByRole('button', { name: 'Novo Chamado' }).click();

        await expect(page.getByLabel('Nome do solicitante')).toBeVisible({ timeout: 15_000 });
    });

    test('lista chamados e abre visualização', async ({ page }) => {
        const protocolo = protocoloDemo();

        await page.goto('/admin/chamados');

        await expect(page.getByRole('heading', { name: 'Chamados' })).toBeVisible();
        await page.getByRole('searchbox', { name: 'Pesquisar' }).fill(protocolo);
        await expect(page.getByRole('cell', { name: protocolo })).toBeVisible({ timeout: 20_000 });

        await page.getByRole('link', { name: protocolo }).click();

        await expect(page).toHaveURL(/\/admin\/chamados\/\d+$/);
        await expect(page.locator('.fi-header-subheading')).toContainText(protocolo);
    });

    test('navega para configurações como administrador', async ({ page }) => {
        await page.goto('/admin/configuracoes');

        await expect(page.getByRole('heading', { name: 'Configurações' })).toBeVisible();
        await expect(page.getByText('Fila de processamento')).toBeVisible();
    });
});
