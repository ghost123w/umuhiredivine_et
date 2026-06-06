const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.setViewportSize({ width: 1280, height: 800 });

  await page.goto('http://localhost:8000/menu.php');

  // Screenshot 1: First Scroll (Hero)
  await page.screenshot({ path: '/home/jules/verification/screenshots/menu_3_scroll_1.png' });

  // Screenshot 2: Second Scroll (Full Image)
  await page.evaluate(() => window.scrollTo(0, window.innerHeight));
  await page.waitForTimeout(500); // Wait for scroll/reveal
  await page.screenshot({ path: '/home/jules/verification/screenshots/menu_3_scroll_2.png' });

  // Screenshot 3: Third Scroll (Product List)
  await page.evaluate(() => window.scrollTo(0, window.innerHeight * 2));
  await page.waitForTimeout(1000); // Wait for reveal animations
  await page.screenshot({ path: '/home/jules/verification/screenshots/menu_3_scroll_3.png' });

  // Verify Admin Frame
  await page.goto('http://localhost:8000/admin/login.php');
  await page.fill('input[name="username"]', 'admin');
  await page.fill('input[name="password"]', 'admin123');
  await page.click('button[type="submit"]');
  await page.goto('http://localhost:8000/admin/dashboard.php?view=settings');
  await page.screenshot({ path: '/home/jules/verification/screenshots/admin_menu_frame.png' });

  await browser.close();
})();
