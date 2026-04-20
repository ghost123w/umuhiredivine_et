import asyncio
from playwright.async_api import async_playwright
import os

async def verify_aura():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        context = await browser.new_context(viewport={'width': 1280, 'height': 720})
        page = await context.new_page()

        # 1. Reset and Install
        if os.path.exists('data/database.db'):
            os.remove('data/database.db')

        await page.goto('http://localhost:8083/install.php')
        await page.fill('input[name="username"]', 'admin')
        await page.fill('input[name="email"]', 'admin@aura.luxury')
        await page.fill('input[name="password"]', 'luxury123')
        await page.click('button[type="submit"]')
        await asyncio.sleep(2)

        # 2. Verify Home State (Nav Hidden)
        await page.goto('http://localhost:8083/index.php')
        await asyncio.sleep(2)
        await page.screenshot(path='v_home.png')

        opacity = await page.evaluate('getComputedStyle(document.querySelector(".main-nav")).opacity')
        visibility = await page.evaluate('getComputedStyle(document.querySelector(".main-nav")).visibility')
        print(f"Home Nav: Opacity={opacity}, Visibility={visibility}")

        # 3. Verify Contact State (Nav Revealed)
        await page.locator('#contact').scroll_into_view_if_needed()
        await asyncio.sleep(2)
        await page.screenshot(path='v_contact.png')

        opacity_c = await page.evaluate('getComputedStyle(document.querySelector(".main-nav")).opacity')
        visibility_c = await page.evaluate('getComputedStyle(document.querySelector(".main-nav")).visibility')
        print(f"Contact Nav: Opacity={opacity_c}, Visibility={visibility_c}")

        # 4. Verify Admin Panel
        await page.goto('http://localhost:8083/admin/dashboard.php')
        title = await page.title()
        print(f"Admin Dashboard Title: {title}")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_aura())
