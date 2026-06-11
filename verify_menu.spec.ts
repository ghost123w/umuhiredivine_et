import { test, expect } from '@playwright/test';

test('verify menu page layout', async ({ page }) => {
  await page.goto('http://localhost:8000/menu.php');
  await page.setViewportSize({ width: 1440, height: 900 });

  // Wait for content
  await page.waitForSelector('.menu-split-container');

  // Screenshot of the split layout
  await page.evaluate(() => window.scrollTo(0, 1000));
  await page.waitForTimeout(1000);
  await page.screenshot({ path: 'verification/screenshots/current_menu_split.png' });
});
