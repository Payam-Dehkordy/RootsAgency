#!/usr/bin/env python3
"""Download Rhythm contact page assets for Roots homepage contact section."""
from pathlib import Path
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "public/media/images/contact"
UI = ROOT / "public/ui"

ASSETS = [
    (
        "https://rhythm-influence.transforms.svdcdn.com/staging/Contact-Image.png?w=760&h=560&q=85&auto=format&fit=crop&dm=1730748411&s=57ce5824ce3d9d904689baa69e8df0ac",
        OUT / "roots-agency-contact-hero@2x.webp",
        True,
    ),
    (
        "https://rhythm-influence.transforms.svdcdn.com/staging/Contact-Image.png?w=380&h=280&q=85&auto=format&fit=crop&dm=1730748411&s=0a920e52451f82725c5020f03570d7a4",
        OUT / "roots-agency-contact-hero.webp",
        True,
    ),
    (
        "https://www.rhythminfluence.com/ui/time_icon.svg",
        UI / "time_icon.svg",
        False,
    ),
]


def download(url: str, dest: Path) -> None:
    dest.parent.mkdir(parents=True, exist_ok=True)
    if dest.exists() and dest.stat().st_size > 0:
        print("skip", dest.name)
        return
    print("get", url)
    req = urllib.request.Request(
        url,
        headers={
            "User-Agent": "Mozilla/5.0",
            "Referer": "https://www.rhythminfluence.com/contact",
        },
    )
    data = urllib.request.urlopen(req, timeout=60).read()
    if dest.suffix == ".webp":
        tmp = dest.with_suffix(".png")
        tmp.write_bytes(data)
        import subprocess

        subprocess.run(
            ["magick", str(tmp), "-quality", "86", str(dest)],
            check=True,
            capture_output=True,
        )
        tmp.unlink(missing_ok=True)
    else:
        dest.write_bytes(data)
    print("saved", dest.relative_to(ROOT))


def main() -> None:
    for url, dest, _ in ASSETS:
        download(url, dest)
    print("done")


if __name__ == "__main__":
    main()
