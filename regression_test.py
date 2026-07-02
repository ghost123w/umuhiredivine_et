import subprocess
import time
import urllib.request

def run_regression_tests():
    # Start PHP built-in server
    php_server = subprocess.Popen(['php', '-S', 'localhost:8001'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    time.sleep(2)  # Give server time to start

    pages = [
        'index.php',
        'explore.php',
        'about.php',
        'menu.php',
        'blog.php',
        'gallery.php',
        'categories.php'
    ]

    base_url = 'http://localhost:8001/'
    all_passed = True

    try:
        for page in pages:
            url = base_url + page
            try:
                response = urllib.request.urlopen(url)
                if response.getcode() == 200:
                    print(f"SUCCESS: {url} returned HTTP 200")
                else:
                    print(f"FAILURE: {url} returned HTTP {response.getcode()}")
                    all_passed = False
            except urllib.error.HTTPError as e:
                print(f"FAILURE: {url} returned HTTP {e.code}")
                all_passed = False
            except Exception as e:
                print(f"ERROR: Could not reach {url}. Exception: {e}")
                all_passed = False
    finally:
        php_server.terminate()

    if all_passed:
        print("\nAll regression tests passed!")
    else:
        print("\nSome regression tests failed.")
        exit(1)

if __name__ == "__main__":
    run_regression_tests()
