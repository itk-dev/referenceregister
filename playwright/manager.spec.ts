import { test, expect } from '@playwright/test';

test('Manager', async ({ page }) => {
  await page.goto('/');

  await page.getByRole('button', { name: 'Manager (Department 1)' }).click();

  // Expect a title "to contain" a substring.
  await expect(page).toHaveTitle(/Referenceregister/);

  await page.getByRole('link', { name: 'Add entry' }).click();
  await page.getByLabel("Identifier", { exact: true }).fill("invalid");
  await page.getByLabel("Department", { exact: true }).selectOption('Department 1');
  await page.getByRole('button', {name: "Add entry"}).click();
  await expect(page.getByRole('heading', {level: 1}))
    .toHaveText('Add entry')
  await expect(page.getByText('Invalid identifier')).toBeVisible()

  await page.getByRole('link', { name: 'Add entry' }).click();
  await page.getByLabel("Identifier", { exact: true }).fill("test-123");
  await page.getByLabel("Department", { exact: true }).selectOption('Department 1');
  await page.getByRole('button', {name: "Add entry"}).click();
  await expect(page.getByRole('heading', {level: 1}))
    .toHaveText('Entry added')

  await page.getByRole('link', { name: 'Look up' }).click();
  await page.getByLabel("Identifier", { exact: true }).fill("test-123");
  await page.getByRole('button', {name: "Look up entry"}).click();
  await expect(page.getByRole('heading', {level: 1}))
    .toHaveText('Entry found')

  await page.getByRole('link', { name: 'Look up' }).click();
  await page.getByLabel("Identifier", { exact: true }).fill("test-1234");
  await page.getByRole('button', {name: "Look up entry"}).click();
  await expect(page.getByRole('heading', {level: 1}))
    .toHaveText('Entry not found')

  await page.getByRole('link', { name: 'Remove entry' }).click();
  await page.getByLabel("Identifier", { exact: true }).fill("test-1234");
  await page.getByLabel("Department", { exact: true }).selectOption('Department 1');
  await page.getByRole('button', {name: "Remove entry"}).click();
  await expect(page.getByRole('heading', {level: 1}))
    .toHaveText('Entry removed')

  await page.getByRole('link', { name: 'Remove entry' }).click();
  await page.getByLabel("Identifier", { exact: true }).fill("test-123");
  await page.getByLabel("Department", { exact: true }).selectOption('Department 1');
  await page.getByRole('button', {name: "Remove entry"}).click();
  await expect(page.getByRole('heading', {level: 1}))
    .toHaveText('Entry removed')

  await page.getByRole('link', { name: 'Look up' }).click();
  await page.getByLabel("Identifier", { exact: true }).fill("test-123");
  await page.getByRole('button', {name: "Look up entry"}).click();
  await expect(page.getByRole('heading', {level: 1}))
    .toHaveText('Entry not found')

  await page.goto('/');
});
