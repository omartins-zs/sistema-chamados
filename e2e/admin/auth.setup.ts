import { test as setup, expect } from '@playwright/test';

const adminEmail = process.env.PLAYWRIGHT_ADMIN_EMAIL ?? 'admin@admin.com';
const adminPassword = process.env.PLAYWRIGHT_ADMIN_PASSWORD ?? 'password';

setup('autenticar administrador', async ({ page }) => {
    await page.goto('/admin/login');
    await page.locator('#form\\.email').fill(adminEmail);
    await page.locator('#form\\.password').fill(adminPassword);
    await page.getByRole('button', { name: 'Login' }).click();

    await expect(page).toHaveURL(/\/admin(\/)?$/);

    await page.context().storageState({ path: 'e2e/.auth/admin.json' });
});
