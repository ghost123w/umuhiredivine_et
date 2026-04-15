from playwright.sync_api import sync_playwright
import os
import random
import string

def random_string(length=8):
    return ''.join(random.choices(string.ascii_letters + string.digits, k=length))

def run_cuj(page):
    test_id = random_string()
    test_title = f"Verified Feature {test_id}"

    # 1. Login
    page.goto("http://localhost:8081/admin/login.php")
    page.wait_for_timeout(1000)
    page.fill('input[name="username"]', "admin")
    page.wait_for_timeout(500)
    page.fill('input[name="password"]', "admin123")
    page.wait_for_timeout(500)
    page.click('button[type="submit"]')
    page.wait_for_timeout(1000)

    # 2. Dashboard Overview (Show the new theme)
    page.screenshot(path="/home/jules/verification/screenshots/dashboard_theme.png")
    page.wait_for_timeout(1000)

    # 3. Add a new selling point
    page.goto("http://localhost:8081/admin/dashboard.php?view=add")
    page.wait_for_timeout(1000)
    page.fill('input[name="section_title"]', test_title)
    page.wait_for_timeout(500)
    page.fill('textarea[name="description"]', "This is a verified entry demonstrating the Electric Orange theme.")
    page.wait_for_timeout(500)
    page.click('button[type="submit"]')
    page.wait_for_timeout(1000)

    # 4. Manage view (Verify entry exists)
    page.goto("http://localhost:8081/admin/dashboard.php?view=manage")
    page.wait_for_timeout(1000)
    page.screenshot(path="/home/jules/verification/screenshots/manage_view_theme.png")

    # 5. Landing Page (Verify theme on frontend)
    page.goto("http://localhost:8081/index.php")
    page.wait_for_timeout(1500)
    # Scroll a bit to trigger reveal animations
    page.evaluate("window.scrollTo(0, 800)")
    page.wait_for_timeout(1000)
    page.screenshot(path="/home/jules/verification/screenshots/landing_page_theme.png")
    page.wait_for_timeout(1000)

if __name__ == "__main__":
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            record_video_dir="/home/jules/verification/videos"
        )
        page = context.new_page()
        try:
            run_cuj(page)
        finally:
            context.close()
            browser.close()
