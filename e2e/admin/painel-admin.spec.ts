import { expect, test } from '@playwright/test';
import { protocoloDemo } from '../helpers/dados';

test.describe('Painel administrativo', () => {
    test('carrega dashboard autenticado', async ({ page }) => {
        await page.goto('/admin');

        await expect(page.getByRole('heading', { name: /painel/i })).toBeVisible();
        await expect(page.getByText('Resumo Geral')).toBeVisible();
    });

    test('exibe botão Novo Chamado no topbar', async ({ page }) => {
        await page.goto('/admin');

        await expect(page.locator('.fi-topbar-novo-chamado').getByRole('button', { name: 'Novo Chamado' })).toBeVisible();
    });

    test('abre modal de novo chamado', async ({ page }) => {
        await page.goto('/admin');
        await page.locator('.fi-header-actions-ctn').getByRole('button', { name: 'Novo Chamado' }).click();

        await expect(page.getByLabel('Nome do solicitante')).toBeVisible();
    });

    test('lista chamados e abre visualização', async ({ page }) => {
        const protocolo = protocoloDemo();

        await page.goto('/admin/chamados');

        await expect(page.getByRole('heading', { name: 'Chamados' })).toBeVisible();
        await expect(page.getByRole('cell', { name: protocolo })).toBeVisible();

        await page.getByRole('link', { name: protocolo }).click();

        await expect(page).toHaveURL(/\/admin\/chamados\/\d+$/);
        await expect(page.getByRole('heading', { name: new RegExp(protocolo) })).toBeVisible();
    });

    test('navega para configurações como administrador', async ({ page }) => {
        await page.goto('/admin/configuracoes');

        await expect(page.getByRole('heading', { name: 'Configurações' })).toBeVisible();
        await expect(page.getByText('Fila de processamento')).toBeVisible();
    });
});
