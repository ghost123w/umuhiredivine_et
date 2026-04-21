import asyncio
from playwright.async_api import async_playwright

async def verify_bg():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        try:
            await page.goto("http://localhost:8083")

            # Check body background
            body_bg = await page.evaluate("window.getComputedStyle(document.body).backgroundColor")
            print(f"Body background color: {body_bg}")

            # Check if stroll-bg-container exists and is visible
            bg_container = await page.query_selector(".stroll-bg-container")
            if bg_container:
                is_visible = await bg_container.is_visible()
                z_index = await page.evaluate("window.getComputedStyle(document.querySelector('.stroll-bg-container')).zIndex")
                print(f"Background container visible: {is_visible}, z-index: {z_index}")
            else:
                print("Background container not found!")

            # Check padding of portrait-viewport
            viewport_padding = await page.evaluate("window.getComputedStyle(document.querySelector('.portrait-viewport')).paddingTop")
            print(f"Viewport padding-top: {viewport_padding}")

            # Capture a screenshot to verify visually
            await page.screenshot(path="verify_bg_initial.png")
            print("Captured verify_bg_initial.png")

        except Exception as e:
            print(f"Error: {e}")
        finally:
            await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_bg())
