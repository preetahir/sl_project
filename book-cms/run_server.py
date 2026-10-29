#!/usr/bin/env python3
"""
LibrisCMS - Python Universal Server Runner
Starts the PHP built-in server using the local portable PHP executable,
and automatically opens the default web browser.
"""

import os
import sys
import subprocess
import webbrowser
import time

def main():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    portable_php = os.path.abspath(os.path.join(base_dir, '..', 'php-portable', 'php.exe'))
    
    php_bin = None
    if os.path.exists(portable_php):
        php_bin = portable_php
    else:
        # Check system PATH
        import shutil
        if shutil.which('php'):
            php_bin = 'php'

    if not php_bin:
        print("[ERROR] Could not find portable PHP or system PHP binary.")
        print(f"Checked: {portable_php}")
        sys.exit(1)

    public_dir = os.path.join(base_dir, 'public')
    router_file = os.path.join(public_dir, 'index.php')

    host = '127.0.0.1'
    port = '8000'
    url = f"http://{host}:{port}"

    print("=" * 70)
    print(" LibrisCMS - Responsive Book Content Management System")
    print(" HTML5 | CSS3 | JavaScript | PHP 8.1 MVC | Laravel Architecture | XML")
    print("=" * 70)
    print(f" PHP Executable : {php_bin}")
    print(f" Public Webroot : {public_dir}")
    print(f" Server URL     : {url}")
    print("=" * 70)
    print(" Press Ctrl+C in this console to stop the server.\n")

    # Open browser in a separate thread/timer
    def open_browser():
        time.sleep(1.5)
        print(f"[*] Opening {url} in your default browser...")
        webbrowser.open(url)

    import threading
    threading.Thread(target=open_browser, daemon=True).start()

    cmd = [php_bin, '-S', f"{host}:{port}", '-t', public_dir, router_file]
    try:
        subprocess.run(cmd, cwd=base_dir)
    except KeyboardInterrupt:
        print("\n[*] LibrisCMS server shut down gracefully.")

if __name__ == '__main__':
    main()

