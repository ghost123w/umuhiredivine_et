from playwright.sync_api import sync_playwright
import os

def run_cuj(page):
    page.goto("http://localhost:8000/index.php")
    page.wait_for_timeout(1000)
    page.screenshot(path="/home/jules/verification/screenshots/final_redesign_v3.png")
    page.wait_for_timeout(500)

    # Check if links work
    nav_links = page.locator(".nav-item")
    count = nav_links.count()
    print(f"Found {count} navigation links.")
    for i in range(count):
        text = nav_links.nth(i).inner_text()
        href = nav_links.nth(i).get_attribute("href")
        print(f"Link {i}: {text} -> {href}")

if __name__ == "__main__":
    os.makedirs("/home/jules/verification/videos", exist_ok=True)
    os.makedirs("/home/jules/verification/screenshots", exist_ok=True)
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            record_video_dir="/home/jules/verification/videos",
            viewport={'width': 1280, 'height': 720}
        )
        page = context.new_page()
        try:
            run_cuj(page)
        finally:
            context.close()
            browser.close()
