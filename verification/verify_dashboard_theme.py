import asyncio
from playwright.async_api import async_playwright
import os

async def verify_dashboard():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        context = await browser.new_context(viewport={'width': 1280, 'height': 800})
        page = await context.new_page()

        # Login first
        await page.goto('http://localhost:8081/admin/login.php')
        await page.fill('input[name="username"]', 'admin')
        await page.fill('input[name="password"]', 'admin123')
        await page.click('button[type="submit"]')

        # Wait for dashboard
        await page.wait_for_url('**/admin/dashboard.php**')

        # Take screenshot of dashboard
        await page.screenshot(path='verification/screenshots/dashboard_theme.png', full_page=True)

        # Check primary color in computed style
        primary_color = await page.evaluate("getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim()")
        print(f"Primary Color: {primary_color}")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_dashboard())
