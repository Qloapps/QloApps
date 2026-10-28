import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
	testDir: './playwright-tests',

    timeout: 120 * 1000,

    expect: { timeout: 20 * 1000 },

    fullyParallel: false,

    forbidOnly: !!process.env.CI,

    retries: 0,

    workers: 1,

    use: {
        baseURL: 'http://127.0.0.1/QloApps',
		screenshot: { mode: 'only-on-failure', fullPage: true },
        video: 'retain-on-failure',
        trace: 'retain-on-failure',
    },

    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});