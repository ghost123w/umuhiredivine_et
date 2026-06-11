import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        # Set viewport to a common desktop size
        await page.set_viewport_size({"width": 1440, "height": 900})

        await page.goto("http://localhost:8000/menu.php")

        # Wait for the third section
        await page.wait_for_selector(".menu-grid-v3-section")

        # Scroll to the third section
        # First scroll is 100vh
        # Second scroll (split layout) is at least 100vh
        await page.evaluate("window.scrollTo(0, 2500)")
        await asyncio.sleep(1) # Wait for any animations

        await page.screenshot(path="verification/screenshots/menu_v3_grid.png", full_page=True)
        print("Screenshot saved to verification/screenshots/menu_v3_grid.png")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
