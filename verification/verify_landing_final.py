import asyncio
from playwright.async_api import async_playwright

async def verify_landing():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        context = await browser.new_context(viewport={'width': 1280, 'height': 1200})
        page = await context.new_page()

        await page.goto('http://localhost:8081/index.php')
        # Wait for animation/reveal
        await page.evaluate("window.scrollTo(0, 500)")
        await asyncio.sleep(1)

        await page.screenshot(path='verification/screenshots/landing_final_dark.png', full_page=True)
        await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_landing())
