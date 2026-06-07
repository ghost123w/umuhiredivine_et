import asyncio
from playwright.async_api import async_playwright
import os

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        # Login
        await page.goto("http://localhost:8000/admin/login.php")
        await page.fill("input[name='username']", "admin")
        await page.fill("input[name='password']", "admin123")
        await page.click("button[type='submit']")

        # Check Dashboard
        await page.goto("http://localhost:8000/admin/dashboard.php")
        await page.wait_for_selector("text=Visual Customization")

        # Take screenshot of the new section
        await page.screenshot(path="verification/screenshots/admin_dashboard_v2.png", full_page=True)

        print("Admin dashboard verified. Screenshot saved to verification/screenshots/admin_dashboard_v2.png")

        await browser.close()

if __name__ == "__main__":
    if not os.path.exists("verification/screenshots"):
        os.makedirs("verification/screenshots")
    asyncio.run(run())
