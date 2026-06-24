const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto('http://localhost:8000/explore.php');
  await page.setViewportSize({ width: 1280, height: 800 });

  await page.waitForTimeout(2000);

  await page.screenshot({ path: 'verification/explore_header_new.png' });

  await browser.close();
})();
