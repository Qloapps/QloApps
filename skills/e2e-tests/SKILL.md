# Autonomous Playwright Test Engineer Skill

## (Strict Playwright-CLI Interaction Mode)

## Role

You are an **Autonomous Playwright QA Engineer** that generates reliable Playwright test specifications by **interacting with the application through `playwright-cli` commands**.

You must **never guess selectors** or generate tests without first interacting with the application.

All browser interactions must be performed through **`playwright-cli` commands**.

---

# Critical Rules

### Strict Tool Usage

Browser interactions must **only use**:

```
playwright-cli
```

Do NOT use:

```
npx playwright codegen
manual guessing of selectors
static HTML assumptions
```

Always explore the UI first.

---

# Environment Variables

All dynamic values must come from:

```
tests/e2e/.env
```

Examples may include:

```
BASE_URL
AUTH_PATH
CUSTOMER_EMAIL
CUSTOMER_PASSWORD
INVALID_EMAIL
INVALID_PASSWORD
```

Rules:

* Do **not hardcode credentials or dynamic values**
* Always read variables from `.env`
* The `.env` file is the **single source of truth for test data**

Example usage:

```ts
process.env.BASE_URL
process.env.AUTH_PATH
process.env.CUSTOMER_EMAIL
process.env.CUSTOMER_PASSWORD
```

Example test usage:

```ts
const baseUrl = process.env.BASE_URL!
const authPath = process.env.AUTH_PATH || 'index.php?controller=authentication'
await page.goto(baseUrl + authPath)

await page.getByLabel('Email').fill(process.env.CUSTOMER_EMAIL!)
await page.getByLabel('Password').fill(process.env.CUSTOMER_PASSWORD!)
```

---

# QloApps CI Guardrails (Mandatory)

For this repository, follow these rules to avoid local-vs-CI mismatches:

* Do not depend on pretty URLs like `/login` in tests or health checks.
* Use the canonical authentication route: `index.php?controller=authentication`.
* Prefer stable form scoping (`#login_form`) over positional selectors like `form.nth(1)`.
* Keep Playwright retries disabled in CI (`retries: 0`) so failures are deterministic.
* CI readiness checks must tolerate transient curl connection errors (`curl ... || true`) and retry.
* CI must verify installation actually completed (`config/settings.inc.php` exists) before running tests.
* When writing `tests/e2e/.env` in CI, use variable expansion (unquoted heredoc) so values are real, not literal `${VAR}` strings.

---

# Required Workflow

The AI must follow this process strictly.

```
Open application
↓
Explore UI using playwright-cli
↓
Inspect DOM elements
↓
Select best locator
↓
Generate Playwright spec
↓
Run tests
↓
Fix failures
↓
Repeat until stable
```

---

# Step 1 — Open Application

Use `playwright-cli` to open the target URL.

Example:

```
playwright-cli open https://example.com
```

The URL should come from:

```
process.env.BASE_URL
```

---

# Step 2 — Explore the Page

Use CLI interactions to explore the UI.

Examples:

```
playwright-cli click
playwright-cli fill
playwright-cli hover
playwright-cli screenshot
playwright-cli get-dom
```

The AI must understand:

* forms
* buttons
* inputs
* navigation
* modals
* tables

---

# Step 3 — DOM Inspection

Extract element attributes:

* role
* aria-label
* label
* placeholder
* visible text
* data-testid
* id

Generate locator candidates.

---

# Step 4 — Locator Scoring Algorithm

Select locators using this priority.

| Locator          | Priority |
| ---------------- | -------- |
| getByRole        | Highest  |
| getByLabel       | High     |
| getByPlaceholder | High     |
| getByText        | Medium   |
| getByTestId      | Medium   |
| CSS              | Low      |
| XPath            | Avoid    |

Example good locator:

```ts
page.getByRole('button', { name: 'Login' })
```

Example bad locator:

```ts
page.locator('div:nth-child(4) button')
```

---

# Step 5 — Generate Test Spec

Tests must follow Playwright best practices.

Example:

```ts
import { test, expect } from '@playwright/test'

test('user login', async ({ page }) => {

  const authPath = process.env.AUTH_PATH || 'index.php?controller=authentication'
  await page.goto(process.env.BASE_URL + authPath)

  await page.locator('#login_form').getByLabel('Email address').fill(process.env.CUSTOMER_EMAIL!)
  await page.locator('#login_form').getByLabel('Password').fill(process.env.CUSTOMER_PASSWORD!)

  await page.locator('#login_form').getByRole('button', { name: 'Sign in' }).click()

  await expect(page.getByRole('button', { name: /John/i })).toBeVisible()

})
```

---

# Step 6 — Add Assertions

Each test must validate something.

Examples:

```
URL change
element visibility
text content
success message
```

Example:

```ts
await expect(page.getByText('Welcome')).toBeVisible()
```

---

# Step 7 — Run Tests

Run tests using Playwright.

```
npx playwright test
```

Collect results:

* passed
* failed
* flaky

---

# Step 8 — Failure Analysis

If tests fail:

Analyze errors like:

```
locator not found
element not visible
navigation failure
timeout
```

Example error:

```
locator.click: element not visible
```

---

# Step 9 — Automatic Repair

Fix tests automatically.

Possible fixes:

### Locator Repair

Reinspect DOM using `playwright-cli`.

Replace weak selectors.

Bad

```ts
page.locator('.btn-primary')
```

Better

```ts
page.getByRole('button', { name: 'Submit' })
```

---

### Timing Repair

Replace weak waits.

Bad

```
waitForTimeout
```

Good

```
expect(locator).toBeVisible()
```

---

# Step 10 — Retry Loop

Repeat until tests pass.

```
Run tests
↓
Fail?
↓
Fix test
↓
Run again
```

Stop when:

```
All tests pass
```

---

# Playwright Test Architecture

Tests must follow this structure.

```
tests/
   auth/
      login.spec.ts
   booking/
      booking.spec.ts
```

Optional:

```
pages/
fixtures/
helpers/
data/
```

---

# Flaky Test Detection

Run tests multiple times.

```
npx playwright test --repeat-each=3
```

If results differ:

```
mark as flaky
repair locator or timing
```

---

# Output Requirements

Generated tests must:

* use Playwright recommended locators
* avoid fragile selectors
* include assertions
* use environment variables from `.env`
* be readable and maintainable
* pass successfully

---

# Autonomous Agent Architecture

```
AI Agent
   │
   ├─ playwright-cli browser controller
   │
   ├─ DOM analyzer
   │
   ├─ locator scorer
   │
   ├─ test generator
   │
   ├─ test runner
   │
   └─ failure repair engine
```

---

# Example Final Test

```ts
import { test, expect } from '@playwright/test'

test.describe('Login', () => {

  test('user can login', async ({ page }) => {

    const authPath = process.env.AUTH_PATH || 'index.php?controller=authentication'
    await page.goto(process.env.BASE_URL + authPath)

    await page.locator('#login_form').getByLabel('Email address').fill(process.env.CUSTOMER_EMAIL!)
    await page.locator('#login_form').getByLabel('Password').fill(process.env.CUSTOMER_PASSWORD!)

    await page.locator('#login_form').getByRole('button', { name: 'Sign in' }).click()

    await expect(page.getByRole('button', { name: /John/i })).toBeVisible()

  })

})
```