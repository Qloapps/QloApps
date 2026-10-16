import { test, expect } from '@playwright/test';

const QLOAPPS_ADMIN_URL = process.env.ADMIN_BASE_URL || 'http://127.0.0.1/QloApps/adminhtl';

const adminCredentials = {
    email: "admin@example.com",
    password: "admin1234",
};

test('Admin login and logout flow', async ({ page }) => {
    // Go to the admin login page (relative to baseURL if set)
    await page.goto(QLOAPPS_ADMIN_URL);

    // Verify that it is an Authentication Page
    await expect(page).toHaveTitle(/Administration panel/i);
    await expect(page.getByRole('heading', { name: 'QloApps' })).toBeVisible();

    // Fill in the login form
    await page.fill('#email', adminCredentials.email);
    await page.fill('#passwd', adminCredentials.password);
    await page.check('#stay_logged_in');
    await page.click('button[name="submitLogin"]');

    // Verify successful login check dashboard URL
    await expect(page).toHaveURL(/admin.*dashboard/i);

    // Click on "Admin User" to open the dropdown
    await page.getByRole('link', { name: /Admin User/i }).click();

    // Click on "Sign out"
    await page.locator('#header_logout').click();

    // Verify redirected back to the login screen
    await expect(page).toHaveTitle(/Administration panel/i);
    await expect(page.getByRole('heading', { name: 'QloApps' })).toBeVisible();
});
