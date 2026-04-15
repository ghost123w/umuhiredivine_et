import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        await page.set_viewport_size({"width": 1280, "height": 800})

        await page.goto("http://localhost:8000/index.php")
        await asyncio.sleep(2)

        await page.screenshot(path="final_v3_hero.png")
        print("Hero screenshot (v3) saved.")

        # Scroll down to reveal first section
        await page.evaluate("window.scrollTo(0, 800)")
        await asyncio.sleep(2)
        await page.screenshot(path="final_v3_scroll1.png")
        print("Scroll 1 screenshot (v3) saved.")

        # Scroll more
        await page.evaluate("window.scrollTo(0, 1600)")
        await asyncio.sleep(2)
        await page.screenshot(path="final_v3_scroll2.png")
        print("Scroll 2 screenshot (v3) saved.")

        # Check CTA text
        cta_text = await page.inner_text(".sidebar-cta .cta")
        print(f"CTA text on landing page: {cta_text}")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
