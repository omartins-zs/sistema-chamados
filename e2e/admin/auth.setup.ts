import { test as setup, expect } from '@playwright/test';

const adminEmail = process.env.PLAYWRIGHT_ADMIN_EMAIL ?? 'admin@admin.com';
const adminPassword = process.env.PLAYWRIGHT_ADMIN_PASSWORD ?? 'password';

setup('autenticar administrador', async ({ page }) => {
    setup.setTimeout(90_000);

    await page.goto('/admin/login', { timeout: 60_000 });
    await page.getByRole('textbox', { name: 'E-mail' }).fill(adminEmail);
    await page.getByRole('textbox', { name: 'Senha' }).fill(adminPassword);
    await page.getByRole('button', { name: 'Login' }).click();

    await expect(page).toHaveURL(/\/admin(\/)?$/, { timeout: 60_000 });

    await page.context().storageState({ path: 'e2e/.auth/admin.json' });
});
