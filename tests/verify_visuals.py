import asyncio
from playwright.async_api import async_playwright

async def verify_visuals():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        context = await browser.new_context(viewport={'width': 1280, 'height': 800})
        page = await context.new_page()

        # 1. Capture Hero Section
        print("Capturing hero section...")
        await page.goto('http://localhost:8083')
        await page.wait_for_timeout(2000)  # Wait for animations/Three.js
        await page.screenshot(path='hero_visual.png')

        # 2. Test Interactive Aura Glow (Move Mouse)
        print("Testing aura glow...")
        await page.mouse.move(100, 100)
        await page.wait_for_timeout(500)
        await page.screenshot(path='aura_glow_top_left.png')

        await page.mouse.move(1000, 700)
        await page.wait_for_timeout(500)
        await page.screenshot(path='aura_glow_bottom_right.png')

        # 3. Scroll to see parallax and reveal
        print("Testing scroll effects...")
        await page.evaluate("window.scrollTo(0, 500)")
        await page.wait_for_timeout(1000)
        await page.screenshot(path='scroll_reveal.png')

        await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_visuals())
