import os
from playwright.sync_api import sync_playwright

def run_cuj(page, screenshot_dir):
    page.goto("http://localhost:8000")
    page.wait_for_timeout(1000)

    # Take screenshot of Hero
    page.screenshot(path=os.path.join(screenshot_dir, "hero.png"))

    # Scroll to FAQ
    page.locator("#faq").scroll_into_view_if_needed()
    page.wait_for_timeout(1000)
    page.screenshot(path=os.path.join(screenshot_dir, "faq_section.png"))

    # Click first accordion item
    page.locator(".accordion-header").first.click()
    page.wait_for_timeout(1000)
    page.screenshot(path=os.path.join(screenshot_dir, "accordion_expanded.png"))

    # Scroll to Footer
    page.locator("footer").scroll_into_view_if_needed()
    page.wait_for_timeout(1000)
    page.screenshot(path=os.path.join(screenshot_dir, "footer_section.png"))

if __name__ == "__main__":
    # Ensure verification directories exist
    screenshot_dir = "tests/verification/screenshots"
    video_dir = "tests/verification/videos"
    os.makedirs(screenshot_dir, exist_ok=True)
    os.makedirs(video_dir, exist_ok=True)

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            record_video_dir=video_dir
        )
        page = context.new_page()
        try:
            run_cuj(page, screenshot_dir)
        finally:
            context.close()
            browser.close()
