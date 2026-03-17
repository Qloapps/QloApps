---
name: e2e-tests
description: >-
  Autonomous Playwright QA workflow for QloApps. Generates reliable E2E test
  specs by exploring the live application with playwright-cli, discovering
  selectors from real DOM snapshots, and iterating until tests pass. Use for
  Playwright tests, browser automation, UI testing, login/checkout/reservation
  flows, regression tests, selector discovery, and flaky test repair.
---

# E2E Test Engineer — QloApps

Never guess selectors. Always explore the live UI with `playwright-cli` first.

## Supporting Skill

Before any browser interaction, scan the project for `**/playwright-cli/SKILL.md` and load the match closest to the project root.

## Workflow

1. Open the page with `playwright-cli open <url>`
2. Inspect with `playwright-cli snapshot`, explore with `click`/`fill`/`hover`
3. Pick the best locator using the priority table
4. Read `tests/e2e/playwright/.env` for available environment variables
5. Generate the Playwright spec in `tests/e2e/playwright/specs/`
6. Run with `npx playwright test --config=tests/e2e/playwright/playwright.config.ts`
7. If failures, re-inspect with `playwright-cli snapshot`, fix, and re-run until all pass

## Exploring the Page

```bash
playwright-cli open <BASE_URL from .env>
playwright-cli snapshot          # primary — returns accessible tree with element refs
playwright-cli click <ref>
playwright-cli fill <ref> "value"
playwright-cli hover <ref>
playwright-cli screenshot        # visual confirmation when needed
```

Do NOT use `npx playwright codegen` or static HTML assumptions.

## Locator Priority

| Locator          | Priority |
| ---------------- | -------- |
| getByRole        | Highest  |
| getByLabel       | High     |
| getByPlaceholder | High     |
| getByText        | Medium   |
| getByTestId      | Medium   |
| CSS (#id)        | Low      |
| XPath            | Avoid    |

## Environment Variables

All dynamic values come from `tests/e2e/playwright/.env`. Never hardcode credentials or URLs. Read the file to discover available variables before writing tests.

```ts
await page.goto(process.env.BASE_URL! + process.env.AUTH_PATH!);
```

## Test Spec Template

Place specs in `tests/e2e/playwright/specs/`. Replace `<discovered-*>` with values from `playwright-cli snapshot`:

```ts
import { test, expect } from '@playwright/test';

test.describe('<Feature>', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(process.env.BASE_URL! + (process.env.AUTH_PATH || '<discovered-path>'));
  });

  test('<test name>', async ({ page }) => {
    await page.locator('<discovered-form-scope>').getByLabel('<discovered-label>').fill(process.env.<ENV_VAR>!);
    await page.locator('<discovered-form-scope>').getByRole('button', { name: '<discovered-name>' }).click();
    await expect(page.getByRole('<discovered-role>', { name: /<discovered-pattern>/i })).toBeVisible();
  });
});
```

## Running Tests

```bash
npx playwright test --config=tests/e2e/playwright/playwright.config.ts
npx playwright test --config=tests/e2e/playwright/playwright.config.ts --repeat-each=3  # flaky detection
```

## Failure Repair

Re-inspect with `playwright-cli snapshot` and fix:

- **Locator broken?** Replace with higher-priority locator from snapshot.
- **Timeout?** Replace `waitForTimeout` with `expect(locator).toBeVisible()` or `expect.poll()`.
- **Navigation failed?** Verify URL uses `index.php?controller=` format, not pretty URLs.

## Gotchas

- Never use pretty URLs (`/login`). Use `index.php?controller=` routes.
- Scope form locators to stable containers (e.g. a form ID) not positional selectors.
- QloApps post-login UI varies — check for account button OR sign-out link, not just one.
- `retries: 0` in config — tests must be deterministic.
- CI writes `.env` via unquoted heredoc for variable expansion. Literal `${VAR}` strings break tests.
- CI must confirm `config/settings.inc.php` exists before running tests.
- `dotenv` is loaded by `playwright.config.ts` — do not add dotenv calls in specs.