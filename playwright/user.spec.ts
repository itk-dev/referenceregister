import { test, expect } from '@playwright/test';

test('User', async ({ page }) => {
  await page.goto('/');

  await page.getByRole('button', { name: 'User (Department 1)' }).click();

  // Expect a title "to contain" a substring.
  await expect(page).toHaveTitle(/Referenceregister/);
  await page.screenshot({ path: 'playwright-screenshot/user-front-page.png', fullPage: true });

  expect(await page.getByRole('link', { name: 'Add entry' }).count()).toEqual(0)
  expect(await page.getByRole('link', { name: 'Remove entry' }).count()).toEqual(0)

  await page.getByRole('link', { name: 'Look up' }).click();
  await page.screenshot({ path: 'playwright-screenshot/user-look-up.png', fullPage: true });
  // await page.getByLabel("Identifier", { exact: true }).fill("test-123");
  // await page.getByRole('button', {name: "Look up entry"}).click();
  // await expect(page.getByRole('heading', {level: 1}))
  //   .toHaveText('Entry found')
  //
  // await page.getByRole('link', { name: 'Look up' }).click();
  // await page.getByLabel("Identifier", { exact: true }).fill("test-1234");
  // await page.getByRole('button', {name: "Look up entry"}).click();
  // await expect(page.getByRole('heading', {level: 1}))
  //   .toHaveText('Entry not found')
  //
  // await page.getByRole('link', { name: 'Look up' }).click();
  // await page.getByLabel("Identifier", { exact: true }).fill("test-123");
  // await page.getByRole('button', {name: "Look up entry"}).click();
  // await expect(page.getByRole('heading', {level: 1}))
  //   .toHaveText('Entry not found')

  await page.goto('/');
});
