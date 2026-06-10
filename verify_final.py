import asyncio
from playwright.async_api import async_playwright
import os

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page(viewport={'width': 1920, 'height': 1080})

        # Go to menu page
        await page.goto('http://localhost:8000/menu.php')
        await page.wait_for_timeout(2000)

        # Screenshot first scroll
        await page.screenshot(path='verification/screenshots/menu_hero.png')

        # Scroll to second section
        await page.evaluate("window.scrollTo(0, window.innerHeight)")
        await page.wait_for_timeout(2000)

        # Screenshot split section
        await page.screenshot(path='verification/screenshots/menu_split_final.png')

        await browser.close()

if __name__ == "__main__":
    # Ensure server is running
    # (Assuming it is started in background or I should start it)
    os.system("php -S localhost:8000 > php_server.log 2>&1 &")
    import time
    time.sleep(2)
    asyncio.run(run())
