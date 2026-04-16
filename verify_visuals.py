import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        # Set viewport to a common desktop size
        await page.set_viewport_size({"width": 1280, "height": 800})

        # Go to the home page
        await page.goto("http://localhost:8000/index.php")

        # Take a screenshot of the hero section
        await page.screenshot(path="final_landing_hero.png")
        print("Hero screenshot saved.")

        # Click on the first sidebar link to reveal the first section
        await page.click(".sidebar-nav a:nth-child(1)")
        await asyncio.sleep(1) # wait for animation
        await page.screenshot(path="final_landing_section1.png")
        print("Section 1 screenshot saved.")

        # Mobile view
        await page.set_viewport_size({"width": 375, "height": 667})
        await page.goto("http://localhost:8000/index.php")
        await page.screenshot(path="final_landing_mobile.png")
        print("Mobile screenshot saved.")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
