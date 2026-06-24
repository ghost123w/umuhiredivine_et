const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto('http://localhost:8000/explore.php');
  await page.setViewportSize({ width: 1280, height: 800 });

  // Wait for the background image to load if possible
  await page.waitForTimeout(2000);

  // Capture the first section
  await page.screenshot({ path: 'verification/explore_updated_section1.png' });

  // Scroll to the second section
  await page.mouse.wheel(0, 800);
  await page.waitForTimeout(1000);
  await page.screenshot({ path: 'verification/explore_updated_section2.png' });

  // Scroll to the third section (video)
  await page.mouse.wheel(0, 800);
  await page.waitForTimeout(2000); // Wait for video to potentially start
  await page.screenshot({ path: 'verification/explore_updated_section3.png' });

  await browser.close();
})();
