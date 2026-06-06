import asyncio
from playwright.async_api import async_playwright
import os

async def verify():
    async_playwright_instance = await async_playwright().start()
    browser = await async_playwright_instance.chromium.launch()
    page = await browser.new_page()

    # Base URL
    base_url = "http://localhost:8000"

    # 1. Login to Admin
    await page.goto(f"{base_url}/admin/login.php")
    await page.fill('input[name="username"]', 'admin')
    await page.fill('input[name="password"]', 'admin123')
    await page.click('button[type="submit"]')
    await page.wait_for_url(f"{base_url}/admin/dashboard.php")

    # 2. Capture Admin Settings Frame Section
    # Scroll to Visual Customization
    await page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
    await asyncio.sleep(1)
    await page.screenshot(path="/home/jules/verification/screenshots/admin_settings_visual_customization.png")
    print("Captured admin_settings_visual_customization.png")

    await browser.close()
    await async_playwright_instance.stop()

if __name__ == "__main__":
    asyncio.run(verify())
