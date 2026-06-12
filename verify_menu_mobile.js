const { chromium, devices } = require('playwright');
const fs = require('fs');
const path = require('path');

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext(devices['iPhone 12']);
  const page = await context.newPage();

  const screenshotsDir = '/home/jules/verification/screenshots/mobile';
  if (!fs.existsSync(screenshotsDir)){
      fs.mkdirSync(screenshotsDir, { recursive: true });
  }

  await page.goto('http://localhost:8000/menu.php');
  await page.waitForTimeout(2000);

  // Take screenshots of each section
  for (let i = 1; i <= 5; i++) {
    await page.evaluate((index) => {
      window.scrollTo(0, (index - 1) * window.innerHeight);
    }, i);
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(screenshotsDir, `mobile_scroll_${i}.png`), fullPage: false });
  }

  await browser.close();
})();
