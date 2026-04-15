import asyncio
from playwright.async_api import async_playwright
import os

async def verify_admin():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        context = await browser.new_context(viewport={'width': 1280, 'height': 800})
        page = await context.new_page()

        # 1. Login Page
        await page.goto('http://localhost:8081/admin/login.php')
        await page.screenshot(path='verification/screenshots/admin_login_dark.png')

        # 2. Login and Dashboard Overview
        await page.fill('input[name="username"]', 'admin')
        await page.fill('input[name="password"]', 'admin123')
        await page.click('button[type="submit"]')
        await page.wait_for_url('**/admin/dashboard.php**')
        await page.screenshot(path='verification/screenshots/admin_dashboard_dark.png', full_page=True)

        # 3. Manage Points View
        await page.goto('http://localhost:8081/admin/dashboard.php?view=manage')
        await page.screenshot(path='verification/screenshots/admin_manage_dark.png', full_page=True)

        # 4. Edit Page
        # Find first edit link if exists
        edit_link = await page.query_selector('a[href*="edit.php"]')
        if edit_link:
            await edit_link.click()
            await page.wait_for_url('**/admin/edit.php**')
            await page.screenshot(path='verification/screenshots/admin_edit_dark.png', full_page=True)

        await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_admin())
