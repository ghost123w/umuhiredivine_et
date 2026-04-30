import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        await page.goto('http://localhost:8000')
        await page.wait_for_timeout(2000)

        # Take a full page screenshot
        await page.screenshot(path='verification/screenshots/redesign_full.png', full_page=True)

        # Verify navigation visibility
        nav = page.locator('.aura-nav-bar')
        await nav.wait_for(state='visible')
        print("Navigation bar is visible.")

        # Check for 3D Brand Title
        brand_title = page.locator('.floating-brand-title')
        await brand_title.wait_for(state='visible')
        print("Floating brand title is visible.")

        # Verify Bento Grid (prism-grid)
        prism_grid = page.locator('.prism-grid')
        grid_count = await prism_grid.count()
        print(f"Found {grid_count} prism grids.")

        # Test Contact Modal
        await page.click('#contact-trigger')
        await page.wait_for_timeout(500)
        modal = page.locator('#contact-modal')
        if await modal.is_visible():
            print("Contact modal opened successfully.")
            await page.screenshot(path='verification/screenshots/contact_modal_redesign.png')
            await page.keyboard.press('Escape')
            await page.wait_for_timeout(500)
            if not await modal.is_visible():
                print("Contact modal closed with ESC key.")

        # Scroll and verify navbar 'scrolled' class
        await page.evaluate("window.scrollTo(0, 200)")
        await page.wait_for_timeout(500)
        if 'scrolled' in await nav.evaluate("el => el.className"):
            print("Navbar correctly applied 'scrolled' class.")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
