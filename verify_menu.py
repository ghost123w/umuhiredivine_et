import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        await page.set_viewport_size({"width": 1280, "height": 1080})
        await page.goto("http://localhost:8000/menu.php")
        await asyncio.sleep(2)  # Wait for any animations
        await page.screenshot(path="verification/menu_screenshot.png", full_page=True)
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
