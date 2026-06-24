import subprocess
import time
import sys

def check_url(url):
    try:
        result = subprocess.run(['curl', '-o', '/dev/null', '-s', '-w', '%{http_code}', url], capture_output=True, text=True)
        return result.stdout.strip()
    except Exception as e:
        return str(e)

def main():
    # Start PHP server
    server_process = subprocess.Popen(['php', '-S', 'localhost:8001'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    time.sleep(2)  # Wait for server to start

    urls = [
        "http://localhost:8001/index.php",
        "http://localhost:8001/menu.php",
        "http://localhost:8001/explore.php",
        "http://localhost:8001/categories.php",
        "http://localhost:8001/about.php",
        "http://localhost:8001/blog.php",
        "http://localhost:8001/gallery.php"
    ]

    all_passed = True
    for url in urls:
        status_code = check_url(url)
        print(f"Checking {url}: {status_code}")
        if status_code != "200":
            all_passed = False

    # Terminate PHP server
    server_process.terminate()
    server_process.wait()

    if all_passed:
        print("All regression tests passed!")
        sys.exit(0)
    else:
        print("Some regression tests failed!")
        sys.exit(1)

if __name__ == "__main__":
    main()
