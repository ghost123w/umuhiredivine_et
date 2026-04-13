const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto('http://localhost:8000/');
  await page.setViewportSize({ width: 1280, height: 2000 });
  await page.screenshot({ path: 'verification_screenshot.png', fullPage: true });
  await browser.close();
})();
