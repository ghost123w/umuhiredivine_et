import asyncio
import os
from playwright.async_api import async_playwright

async def verify_redesign():
    async with async_playwright() as p:
        # Launch browser
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={'width': 1280, 'height': 800})
        page = await context.new_page()

        try:
            # Navigate to the landing page
            print("Navigating to http://localhost:8000...")
            await page.goto("http://localhost:8000", timeout=60000)

            # 1. Verify Navigation Bar
            print("Checking navigation bar...")
            navbar = page.locator(".aura-nav-bar")
            await navbar.wait_for(state="visible")

            nav_links = page.locator(".nav-item")
            count = await nav_links.count()
            print(f"Found {count} navigation links.")
            if count < 3:
                raise Exception(f"Expected at least 3 nav links, found {count}")

            # 2. Verify Hero Section
            print("Checking hero section...")
            hero = page.locator(".hero-section")
            await hero.wait_for(state="visible")

            title = page.locator(".hero-content h1")
            await title.wait_for(state="visible")
            title_text = await title.inner_text()
            print(f"Hero Title: {title_text}")

            # 3. Verify Products Section
            print("Checking products section...")
            products_section = page.locator("#products")
            await products_section.scroll_into_view_if_needed()
            await products_section.wait_for(state="visible")

            product_cards = page.locator(".product-card")
            p_count = await product_cards.count()
            print(f"Found {p_count} product cards.")

            # 4. Verify Gallery Section
            print("Checking gallery section...")
            gallery_section = page.locator("#gallery")
            await gallery_section.scroll_into_view_if_needed()
            await gallery_section.wait_for(state="visible")

            # 5. Test Navigation Click (Smooth Scroll)
            print("Testing navigation click (Gallery)...")
            await page.evaluate("window.scrollTo(0, 0)")
            await page.click("text=Gallery")
            await asyncio.sleep(2) # Wait for smooth scroll

            # Check if gallery is in viewport (roughly)
            is_visible = await gallery_section.is_visible()
            print(f"Gallery visible after click: {is_visible}")

            # 6. Capture Screenshots
            os.makedirs("verification", exist_ok=True)
            await page.evaluate("window.scrollTo(0, 0)")
            await page.screenshot(path="verification/redesign_top.png")

            await gallery_section.scroll_into_view_if_needed()
            await page.screenshot(path="verification/redesign_gallery.png")
            print("Screenshots captured in 'verification/' directory.")

            # 7. Check responsive behavior (Mobile)
            print("Checking mobile responsive view...")
            await page.set_viewport_size({"width": 375, "height": 667})
            await asyncio.sleep(1)
            # Nav links should be hidden per CSS
            nav_links_container = page.locator(".nav-links")
            is_hidden = await nav_links_container.is_hidden()
            print(f"Nav links hidden on mobile: {is_hidden}")
            await page.screenshot(path="verification/redesign_mobile.png")

        except Exception as e:
            print(f"Verification failed: {e}")
            raise e
        finally:
            await browser.close()

if __name__ == "__main__":
    asyncio.run(verify_redesign())
