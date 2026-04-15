import asyncio
from playwright.async_api import async_playwright
import os
import random
import string

def random_string(length=8):
    return ''.join(random.choices(string.ascii_letters + string.digits, k=length))

async def run_regression():
    test_id = random_string()
    test_title = f"Regression Test {test_id}"
    test_title_updated = f"Regression Test {test_id} Updated"

    async with async_playwright() as p:
        browser = await p.chromium.launch()
        context = await browser.new_context()
        page = await context.new_page()

        # Login
        await page.goto("http://localhost:8081/admin/login.php")
        await page.fill('input[name="username"]', "admin")
        await page.fill('input[name="password"]', "admin123")
        await page.click('button[type="submit"]')
        await page.wait_for_url("**/admin/dashboard.php**")
        print("Login successful.")

        # Add New Point
        await page.goto("http://localhost:8081/admin/dashboard.php?view=add")
        await page.fill('input[name="section_title"]', test_title)
        await page.fill('textarea[name="description"]', "Testing functionality after color change.")
        await page.click('button[type="submit"]')
        print(f"Added new point: {test_title}")

        # Manage Points (Find the one we just added)
        await page.goto("http://localhost:8081/admin/dashboard.php?view=manage")
        # Find the row with exact test_title
        row = page.locator('tr').filter(has_text=test_title).first
        await row.wait_for()
        print(f"Found regression point: {test_title}")

        # Edit Point
        edit_btn = row.locator('a:has-text("Edit / View Text")')
        await edit_btn.click()
        await page.wait_for_url("**/admin/edit.php**")
        await page.fill('input[name="section_title"]', test_title_updated)
        await page.click('button[type="submit"]')
        print(f"Edited point to: {test_title_updated}")

        # Delete Point
        await page.goto("http://localhost:8081/admin/dashboard.php?view=manage")
        row = page.locator('tr').filter(has_text=test_title_updated).first
        delete_btn = row.locator('button[name="delete_section"]')

        # Handle dialog if present
        page.on("dialog", lambda dialog: dialog.accept())
        await delete_btn.click()
        print(f"Deleted point: {test_title_updated}")

        # Verify deletion
        await page.wait_for_selector(f'tr:has-text("{test_title_updated}")', state="hidden")
        print("Verified deletion.")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run_regression())
