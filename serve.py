#!/usr/bin/env python3
"""Local dev server for RootsAgency template mirror.

Usage:  python serve.py          # default port 8013
        python serve.py 8014     # custom port
"""

import subprocess
import sys

port = int(sys.argv[1]) if len(sys.argv) > 1 else 8013

subprocess.run(
    ["php", "-S", f"127.0.0.1:{port}", "-t", "public", "public/router.php"],
    check=False,
)
