const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.setViewportSize({ width: 1280, height: 800 });

  await page.goto('http://localhost:8000/menu.php');

  // Scroll to the menu items section
  await page.evaluate(() => {
    const el = document.querySelector('.menu-items-section');
    el.scrollIntoView();
  });

  await page.waitForTimeout(1000);
  await page.screenshot({ path: 'menu_transparent_verify.png' });

  await browser.close();
})();
