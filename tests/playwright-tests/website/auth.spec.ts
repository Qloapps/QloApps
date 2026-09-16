import { test, expect } from '@playwright/test';

const QLOAPPS_URL = process.env.WEBSITE_BASE_URL || 'http://127.0.0.1/QloApps';

test('Home Page Check', async ({ page }) => {
  await page.goto(QLOAPPS_URL);

  await expect(page).toHaveTitle(/Hotel Prime/);
});

test('Sign In Page', async ({ page }) => {
  await page.goto(QLOAPPS_URL);

  // Click on the Sign In link.
  await page.getByRole('link', { name: 'Sign in' }).click();

  // Verify that it is an Authentication Page
  await expect(page.getByRole('heading', { name: 'Authentication' })).toBeVisible();
});

