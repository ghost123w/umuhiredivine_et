import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        # Set viewport to a common desktop size
        await page.set_viewport_size({"width": 1280, "height": 800})

        # Go to the home page
        await page.goto("http://localhost:8000/index.php")

        # Wait for initial load
        await asyncio.sleep(1)

        # Screenshot Hero
        await page.screenshot(path="final_v2_hero.png")
        print("Hero screenshot (v2) saved.")

        # Scroll down to reveal first section
        await page.evaluate("window.scrollTo(0, 500)")
        await asyncio.sleep(1)
        await page.screenshot(path="final_v2_scroll1.png")
        print("Scroll 1 screenshot (v2) saved.")

        # Scroll more
        await page.evaluate("window.scrollTo(0, 1200)")
        await asyncio.sleep(1)
        await page.screenshot(path="final_v2_scroll2.png")
        print("Scroll 2 screenshot (v2) saved.")

        # Check if sidebar highlight is working (ScrollSpy)
        # We'll check the class of the first sidebar link
        is_active = await page.eval_on_selector(".sidebar-nav a:nth-child(1)", "el => el.classList.contains('active-link')")
        print(f"First sidebar link active: {is_active}")

        # Go to Admin General Settings and update CTA
        await page.goto("http://localhost:8000/admin/login.php")
        await page.fill('input[name="username"]', "admin")
        await page.fill('input[name="password"]', "luxury123")
        await page.click('button[type="submit"]')

        # Wait for redirect to dashboard
        await page.wait_for_url("**/admin/dashboard.php**")
        print("Logged in successfully.")

        await page.goto("http://localhost:8000/admin/dashboard.php?view=settings")
        # Wait for the input to be available
        await page.wait_for_selector('input[name="cta_text"]')
        await page.fill('input[name="cta_text"]', "RESERVE NOW")
        await page.click('button[name="update_settings"]')
        print("Updated CTA text in Admin.")

        # Check landing page again
        await page.goto("http://localhost:8000/index.php")
        cta_text = await page.inner_text(".sidebar-cta .cta")
        print(f"CTA text on landing page: {cta_text}")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
