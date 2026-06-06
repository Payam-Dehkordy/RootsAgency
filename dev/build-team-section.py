#!/usr/bin/env python3
"""Download Roots team photos and emit clientLogos (team) HTML fragment."""
from __future__ import annotations

import re
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
OUT_DIR = ROOT / "public/media/images/team"
BODY = ROOT / "app/Views/pages/home/rhythm-influence-body.html"
BASE_URL = "https://rootsagency.am/storage/team"

TEAM: list[dict[str, str]] = [
    {"slug": "anahit-voskanyan", "name": "Anahit Voskanyan", "role": "CEO", "id": "69b99d49857f0"},
    {"slug": "anahit-matevosyan", "name": "Anahit Matevosyan", "role": "Team Lead", "id": "69b99db279cfd"},
    {"slug": "anjela-khachatryan", "name": "Anjela Khachatryan", "role": "Art Director", "id": "69b99dbb57709"},
    {"slug": "anna-hayrunts", "name": "Anna Hayrunts", "role": "Senior Graphic Designer", "id": "69b99dc215d1c"},
    {"slug": "armenuhi-harutyunyan", "name": "Armenuhi Harutyunyan", "role": "Project Manager", "id": "69b99d5ab6f1d"},
    {"slug": "hasmik-khachatryan", "name": "Hasmik Khachatryan", "role": "Reel Maker", "id": "69b99d6a0c37f"},
    {"slug": "vika-khachatryan", "name": "Vika Khachatryan", "role": "Project Manager", "id": "69b99daa6b85e"},
    {"slug": "milena-hovsepyan", "name": "Milena Hovsepyan", "role": "Project Manager", "id": "69b99d8fdccd4"},
    {"slug": "gohar-ghazaryan", "name": "Gohar Ghazaryan", "role": "Graphic Designer", "id": "69b99d5300656"},
    {"slug": "mane-hambardzumyan", "name": "Mane Hambardzumyan", "role": "Project Manager", "id": "69b99d86645c1"},
    {"slug": "janna-taroyan", "name": "Janna Taroyan", "role": "Project Manager", "id": "69b99d7475983"},
]


def download(member: dict[str, str]) -> Path:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    dest = OUT_DIR / f"roots-agency-team-{member['slug']}.webp"
    if not dest.exists():
        url = f"{BASE_URL}/{member['id']}.webp"
        print(f"Downloading {url} -> {dest.name}")
        req = urllib.request.Request(url, headers={"User-Agent": "RootsAgency-build/1.0"})
        with urllib.request.urlopen(req, timeout=60) as resp:
            dest.write_bytes(resp.read())
    return dest


def team_card(member: dict[str, str], *, banner: bool = False) -> str:
    src = f"/media/images/team/roots-agency-team-{member['slug']}.webp"
    alt = member["name"]
    if banner:
        return f"""                    <div class="logoBanner__logo roots-team-banner">
                        <img
          src="{src}"
          class="media img roots-team-card__photo"
          alt="{alt}"
        />
                        <div class="roots-team-card__bar">
                            <p class="roots-team-card__name">{member['name']}</p>
                            <p class="roots-team-card__role">{member['role']}</p>
                        </div>
                        <span></span>
                    </div>"""
    return f"""                    <div class="clientLogos__logo logo roots-team-card">
                        <img
          src="{src}"
          class="media img roots-team-card__photo"
          alt="{alt}"
        />
                        <div class="roots-team-card__bar">
                            <p class="roots-team-card__name">{member['name']}</p>
                            <p class="roots-team-card__role">{member['role']}</p>
                        </div>
                <span></span>
            </div>"""


def build_section() -> str:
    cards = "\n".join(team_card(m) for m in TEAM)
    banner = "\n".join(team_card(m, banner=True) for m in TEAM)
    return f"""<section class="clientLogos" id="team">
    <div class="clientLogos__content">
        <div class="clientLogos__contentInner scroll">
            <p class="subheading anima text-fade">Our Team</p>
                              <h2 class="h0 clientLogos__heading h-anim anima" data-spanner="w" ><p>Meet</p><p>our</p><p><i>T</i><strong>EAM</strong></p></h2>
  
        </div>
    </div>
    <div class="clientLogos__logos scroll anima fade" id="logos">
{cards}
            </div>
    <div class="logoBanner__row">
        <div class="logoBanner__rail" data-looper=".clientLogos" data-looper-speed="-1">
{banner}
                    
                    </div>
    </div>
</section>"""


def patch_body(section: str) -> None:
    text = BODY.read_text(encoding="utf-8")
    pattern = r'<section class="clientLogos">.*?</section>\s*'
    if not re.search(pattern, text, flags=re.DOTALL):
        raise SystemExit("clientLogos section not found in body HTML")
    updated = re.sub(pattern, section + "\n\n", text, count=1, flags=re.DOTALL)
    BODY.write_text(updated, encoding="utf-8")
    print(f"Patched {BODY.relative_to(ROOT)}")


def main() -> None:
    for member in TEAM:
        download(member)
    patch_body(build_section())
    print(f"Team section: {len(TEAM)} members")


if __name__ == "__main__":
    main()
