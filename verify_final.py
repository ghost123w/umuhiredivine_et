import asyncio
from playwright.async_api import async_playwright
import os
import subprocess
import time

async def verify():
    # Start PHP server
    server = subprocess.Popen(["php", "-S", "localhost:8080"], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    time.sleep(2) # Wait for server to start

    try:
        async with async_playwright() as p:
            browser = await p.chromium.launch()
            page = await browser.new_page()

            # Go to Explore page
            await page.goto("http://localhost:8080/explore.php")

            # Check Hero
            hero = page.locator('.hero-aura')
            if await hero.is_visible():
                print("SUCCESS: Hero visible.")

            await page.screenshot(path="final_top.png")

            # Scroll to video
            await page.evaluate("window.scrollTo(0, window.innerHeight)")
            time.sleep(1)

            # Check Player
            player = page.locator('.youtube-player')
            video_id = await player.get_attribute('data-video-id')
            if video_id == "tHEa6HHAdaI":
                print(f"SUCCESS: Correct Video ID {video_id} found.")

            await page.screenshot(path="final_video.png")

            await browser.close()
    finally:
        server.terminate()

if __name__ == "__main__":
    asyncio.run(verify())
