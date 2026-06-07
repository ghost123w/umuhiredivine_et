import asyncio
from playwright.async_api import async_playwright
import os

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        # Menu Page - First Scroll (Frame)
        await page.goto("http://localhost:8000/menu.php")
        await page.set_viewport_size({"width": 1280, "height": 720})
        await page.screenshot(path="verification/screenshots/menu_scroll1_frame.png")

        # Menu Page - Second Scroll (Items)
        await page.evaluate("window.scrollTo(0, window.innerHeight)")
        await asyncio.sleep(1) # Wait for any transition
        await page.screenshot(path="verification/screenshots/menu_scroll2_items.png")

        # Admin Dashboard - Visual Customization
        await page.goto("http://localhost:8000/admin/login.php")
        await page.fill("input[name='username']", "admin")
        await page.fill("input[name='password']", "admin123")
        await page.click("button[type='submit']")
        await page.goto("http://localhost:8000/admin/dashboard.php?view=settings")

        # Scroll to Visual Customization
        await page.evaluate("document.querySelector('h3').scrollIntoView()")
        await page.screenshot(path="verification/screenshots/admin_visual_customization.png")

        print("Verification complete. Screenshots saved.")
        await browser.close()

if __name__ == "__main__":
    if not os.path.exists("verification/screenshots"):
        os.makedirs("verification/screenshots", exist_ok=True)
    asyncio.run(run())
