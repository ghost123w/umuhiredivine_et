
import asyncio
from playwright.async_api import async_playwright

async def verify():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        # 1. Check Frontend Bottom
        await page.goto('http://localhost:8000/index.php')
        await page.wait_for_timeout(1000)
        await page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        await page.wait_for_timeout(1000)
        await page.screenshot(path='final_v3_bottom.png')

        # 2. Check Admin Dashboard (already logged in via previous session? No, need to login)
        await page.goto('http://localhost:8000/admin/login.php')
        await page.fill('input[name="username"]', 'admin')
        await page.fill('input[name="password"]', 'admin123')
        await page.click('button[type="submit"]')
        await page.wait_for_url('**/dashboard.php')
        await page.screenshot(path='final_v3_admin_dash.png')

        await browser.close()

if __name__ == "__main__":
    asyncio.run(verify())
