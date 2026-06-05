import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

test('verify menu page scrolls', async ({ page }) => {
  await page.goto('http://localhost:8000/menu.php');

  // Wait for font/assets
  await page.waitForTimeout(1000);

  const screenshotDir = '/home/jules/verification/screenshots';
  if (!fs.existsSync(screenshotDir)) {
    fs.mkdirSync(screenshotDir, { recursive: true });
  }

  // 1. Capture first scroll (Hero)
  await page.screenshot({ path: path.join(screenshotDir, 'menu_scroll_1_hero.png') });

  // 2. Scroll to 100vh (Featured Image)
  await page.evaluate(() => window.scrollTo(0, window.innerHeight));
  await page.waitForTimeout(500);
  await page.screenshot({ path: path.join(screenshotDir, 'menu_scroll_2_featured.png') });

  // 3. Scroll to 200vh (Menu Content)
  await page.evaluate(() => window.scrollTo(0, window.innerHeight * 2));
  await page.waitForTimeout(500);
  await page.screenshot({ path: path.join(screenshotDir, 'menu_scroll_3_content.png') });
});
