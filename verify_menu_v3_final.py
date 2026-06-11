
import asyncio
from playwright.async_api import async_playwright
import os

async def verify_v3_final():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        # Set viewport to a common desktop size
        await page.set_viewport_size({"width": 1920, "height": 1080})

        try:
            # Go to the menu page
            await page.goto("http://localhost:8000/menu.php")
            await page.wait_for_timeout(2000) # Wait for images/scroll

            # Scroll to the second section
            await page.evaluate("window.scrollTo(0, window.innerHeight);")
            await page.wait_for_timeout(1000)

            # Take a screenshot of the split layout
            screenshot_path = "/home/jules/verification/screenshots/menu_split_v3_final.png"
            await page.screenshot(path=screenshot_path, full_page=True)
            print(f"Final screenshot saved to {screenshot_path}")

        except Exception as e:
            print(f"Error during verification: {e}")
        finally:
            await browser.close()

if __name__ == "__main__":
    if not os.path.exists("/home/jules/verification/screenshots"):
        os.makedirs("/home/jules/verification/screenshots")
    asyncio.run(verify_v3_final())
