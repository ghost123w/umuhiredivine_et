import asyncio
from playwright.async_api import async_playwright
import os

async def verify_journey():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        base_url = "http://localhost:8083"

        print(f"Navigating to {base_url}...")
        await page.goto(base_url)
        await page.wait_for_timeout(1000) # Wait for initial animations
        await page.screenshot(path="1_top.png")

        # Scroll down to reveal sections
        print("Scrolling to reveal sections...")
        await page.locator('#contact').scroll_into_view_if_needed()
        await page.wait_for_timeout(1000)
        await page.screenshot(path="2_scrolled.png")

        # Test Contact Form
        print("Testing contact form...")
        # Wait for the element to be visible/attached
        await page.wait_for_selector('input[name="name"]', state='visible')
        await page.fill('input[name="name"]', "Test User")
        await page.fill('input[name="email"]', "test@example.com")
        await page.select_option('select[name="subject"]', "Consultation")
        await page.fill('textarea[name="message"]', "This is a test message from automated verification.")
        await page.click('button[type="submit"]')
        await page.wait_for_load_state("networkidle")
        await page.screenshot(path="3_form_submitted.png")

        # Login to Admin
        print("Logging into Admin panel...")
        await page.goto(f"{base_url}/admin/login.php")
        await page.fill('input[name="username"]', "admin")
        await page.fill('input[name="password"]', "admin123")
        await page.click('button[type="submit"]')
        await page.wait_for_load_state("networkidle")

        # Verify Dashboard
        if "dashboard.php" in page.url:
            print("Login successful. Dashboard reached.")
            await page.screenshot(path="4_admin_dashboard.png")

            # Navigate to messages view
            print("Navigating to messages archive...")
            await page.goto(f"{base_url}/admin/dashboard.php?view=messages")
            await page.wait_for_load_state("networkidle")
            await page.screenshot(path="5_admin_messages.png")

            # Check for the message we just sent
            content = await page.content()
            if "test@example.com" in content:
                print("Contact message verified in dashboard.")
            else:
                print("Warning: Contact message not found in dashboard.")
        else:
            print(f"Login failed. Current URL: {page.url}")
            await page.screenshot(path="5_login_failed.png")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_journey())
