#!/usr/bin/env python3
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "dev/template-source/rhythm-influence-contact.raw.html"
req = urllib.request.Request(
    "https://www.rhythminfluence.com/contact",
    headers={"User-Agent": "Mozilla/5.0"},
)
html = urllib.request.urlopen(req, timeout=60).read().decode("utf-8", "replace")
OUT.write_text(html, encoding="utf-8")
print("saved", OUT, "len", len(html))
