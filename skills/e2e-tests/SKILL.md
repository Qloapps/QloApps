---
name: e2e-tests
description: >-
  Autonomous Playwright QA workflow for QloApps. Generates reliable E2E test
  specs by exploring the live application with playwright-cli, discovering
  selectors from real DOM snapshots, and iterating until tests pass. Use for
  Playwright tests, browser automation, UI testing, login/checkout/reservation
  flows, regression tests, selector discovery, and flaky test repair.
compatibility: Requires Node.js, Playwright, and the playwright-cli supporting skill.
metadata:
  version: "2.0"
---

# E2E Test Engineer — QloApps

Never guess selectors. Always explore the live UI with `playwright-cli` first.

## Supporting Skill Dependency

Before any browser interaction, scan the project for `**/playwright-cli/SKILL.md` and load the match closest to the project root. That skill defines all valid `playwright-cli` commands.

## Workflow

Follow this sequence for every test task:

- [ ] Step 1: Open the page with `playwright-cli open <url>`
- [ ] Step 2: Explore and inspect with `playwright-cli snapshot` (primary) and `click`/`fill`/`hover`
- [ ] Step 3: Pick the best locator using the priority table below
- [ ] Step 4: Generate the Playwright spec
- [ ] Step 5: Run tests and verify
- [ ] Step 6: If failures, repair locators/timing via `playwright-cli snapshot`, then re-run
- [ ] Step 7: Repeat until all tests pass

## Step 1 — Open & Explore

```bash
playwright-cli open <BASE_URL from .env>
playwright-cli snapshot          # primary DOM inspection tool
playwright-cli click <ref>
playwright-cli fill <ref> "value"
playwright-cli hover <ref>
playwright-cli screenshot        # visual confirmation when needed
```

`playwright-cli snapshot` returns the accessible tree with element refs. Use it to discover roles, labels, placeholders, text, and IDs — then pick a locator.

Do NOT use `npx playwright codegen`, manual selector guessing, or static HTML assumptions.

## Step 2 — Locator Priority

| Locator          | Priority |
| ---------------- | -------- |
| getByRole        | Highest  |
| getByLabel       | High     |
| getByPlaceholder | High     |
| getByText        | Medium   |
| getByTestId      | Medium   |
| CSS (#id)        | Low      |
| XPath            | Avoid    |

Good: `page.getByRole('button', { name: 'Sign in' })`
Bad: `page.locator('div:nth-child(4) button')`

## Step 3 — Environment Variables

All dynamic values come from `tests/e2e/.env` (loaded automatically by `playwright.config.ts` via `dotenv`). Never hardcode credentials or URLs. Read `tests/e2e/.env` to discover available variables before writing tests.

Usage pattern:

```ts
await page.goto(process.env.BASE_URL! + process.env.AUTH_PATH!);
```

The `playwright.config.ts` also sets `baseURL`, so `page.goto('/')` resolves to `BASE_URL`. Use relative paths for simple navigation and explicit concatenation when building non-root paths.

## Step 4 — Generate Test Spec

Place specs in `tests/e2e/specs/`. Discover all selectors, labels, and button names from `playwright-cli snapshot` before writing assertions.

Template structure (replace `<discovered-*>` placeholders with real values from snapshot):

```ts
import { test, expect } from '@playwright/test';

test.describe('<Feature>', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(process.env.BASE_URL! + (process.env.AUTH_PATH || '<discovered-path>'));
  });

  test('<test name>', async ({ page }) => {
    // Use locators discovered via playwright-cli snapshot
    await page.locator('<discovered-form-scope>').getByLabel('<discovered-label>').fill(process.env.<ENV_VAR>!);
    await page.locator('<discovered-form-scope>').getByRole('button', { name: '<discovered-name>' }).click();

    // Assert using visible elements found in post-action snapshot
    await expect(page.getByRole('<discovered-role>', { name: /<discovered-pattern>/i })).toBeVisible();
  });
});
```

Every test must include at least one assertion (element visibility, URL change, text content, or error message). Never copy locators from this template literally — always discover them from the live UI.

## Step 5 — Run Tests

```bash
npx playwright test --config=tests/e2e/playwright.config.ts
```

For flaky test detection:

```bash
npx playwright test --config=tests/e2e/playwright.config.ts --repeat-each=3
```

## Step 6 — Failure Repair

When tests fail, re-inspect with `playwright-cli snapshot` and fix:

- **Locator broken?** Re-discover from snapshot, replace with higher-priority locator.
- **Timeout?** Replace `waitForTimeout` with `expect(locator).toBeVisible()` or `expect.poll()`.
- **Navigation failed?** Verify URL uses `index.php?controller=` format, not pretty URLs.

Re-run after each fix. Repeat until all tests pass.

## Gotchas

- Never use pretty URLs (`/login`). Always use `index.php?controller=` routes.
- Scope form locators to stable containers (e.g. a form ID) instead of positional selectors like `form.nth(1)`.
- QloApps post-login UI varies — check for account button OR sign-out link, not just one.
- `retries: 0` in config — tests must be deterministic; do not rely on retries.
- CI writes `.env` via unquoted heredoc for variable expansion. Literal `${VAR}` strings break tests.
- CI must confirm `config/settings.inc.php` exists before running tests.
- `dotenv` loading is handled in `playwright.config.ts` — do not add separate dotenv calls in specs.