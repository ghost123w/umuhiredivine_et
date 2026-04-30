import asyncio
from playwright.async_api import async_playwright
import os
import subprocess
import time
import sqlite3

async def verify():
    # Ensure database is initialized with admin
    db_path = 'data/database.db'
    if os.path.exists(db_path):
        os.remove(db_path)

    if not os.path.exists('data'):
        os.makedirs('data')

    # Initialize DB by hitting index.php (effectively)
    php_proc = subprocess.Popen(["php", "-S", "localhost:8000", "-t", "."])
    time.sleep(2)

    # We need to create an admin user manually. Since bcrypt isn't available, we'll use a known hash for 'admin123'
    # PHP's password_hash(PASSWORD_DEFAULT) uses bcrypt usually.
    # Let's generate one with PHP
    pwd_hash = subprocess.check_output(["php", "-r", "echo password_hash('admin123', PASSWORD_DEFAULT);"]).decode()

    # Hit index to create tables
    subprocess.run(["curl", "-s", "http://localhost:8000/index.php"], stdout=subprocess.DEVNULL)

    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    cursor.execute("INSERT INTO admins (username, password, email, is_verified) VALUES (?, ?, ?, ?)", ("admin", pwd_hash, "admin@example.com", 1))
    conn.commit()
    conn.close()

    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        try:
            # 1. Check Contact Modal on Landing Page
            print("Checking landing page contact modal...")
            await page.goto("http://localhost:8000/index.php")
            await page.click("#contact-trigger")
            await asyncio.sleep(1)
            await page.screenshot(path="verification/screenshots/landing_contact_modal.png")

            # Submit a message
            await page.fill('input[name="name"]', 'Test User')
            await page.fill('input[name="email"]', 'test@example.com')
            await page.select_option('select[name="subject"]', 'Aura Consultation')
            await page.fill('textarea[name="message"]', 'Hello, this is a test message.')
            await page.click('button[name="send_message"]')
            await asyncio.sleep(2)
            await page.screenshot(path="verification/screenshots/contact_submitted.png")

            # 2. Login to Admin
            print("Logging into admin...")
            await page.goto("http://localhost:8000/admin/login.php")
            await page.fill('input[name="username"]', 'admin')
            await page.fill('input[name="password"]', 'admin123')
            await page.click('button[type="submit"]')
            await asyncio.sleep(1)

            # 3. Check Messages View
            print("Checking admin messages...")
            await page.goto("http://localhost:8000/admin/dashboard.php?view=messages")
            await page.screenshot(path="verification/screenshots/admin_messages.png")

            # 4. Update Contact Settings
            print("Updating contact settings...")
            await page.goto("http://localhost:8000/admin/dashboard.php?view=settings")
            await page.fill('input[name="contact_title"]', 'TALK TO US')
            await page.fill('input[name="contact_subtitle"]', 'Updated Subtitle via Admin')
            await page.click('button[name="update_settings"]')
            await asyncio.sleep(1)

            # 5. Verify Updated Settings on Landing Page
            print("Verifying updated settings on landing page...")
            await page.goto("http://localhost:8000/index.php")
            await page.click("#contact-trigger")
            await asyncio.sleep(1)
            await page.screenshot(path="verification/screenshots/landing_updated_contact.png")

            print("Verification complete.")
        except Exception as e:
            print(f"Error during verification: {e}")
        finally:
            await browser.close()
            php_proc.terminate()

if __name__ == "__main__":
    asyncio.run(verify())
