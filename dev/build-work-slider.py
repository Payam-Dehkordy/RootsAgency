#!/usr/bin/env python3
"""Copy client work MP4s, generate WebP posters, emit work slider HTML."""
from __future__ import annotations

import re
import shutil
import subprocess
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CLIENTS = ROOT.parent
VIDEO_OUT = ROOT / "public/media/video/work"
POSTER_OUT = ROOT / "public/media/images/work"
INITIAL_VISIBLE = 5

WORK_ITEMS: list[dict[str, str]] = [
    {
        "slug": "j-space",
        "source_glob": "Among our numerous creative partnerships is J Space*",
        "heading": '<p><i>J</i> Space — catchy video content partnership</p>',
        "hashtag": "#creative",
    },
    {
        "slug": "pitara",
        "source_glob": "Another Indian restaurant has opened its doors in Yerevan- Pitara*",
        "heading": '<p><i>P</i>itara — traditional Indian cuisine in Yerevan</p>',
        "hashtag": "#horeca",
    },
    {
        "slug": "sia-resort-spa",
        "source_glob": "From branding to social media, Roots has been with Sia Resort*",
        "heading": '<p><i>S</i>ia Resort and Spa — branding to social since opening</p>',
        "hashtag": "#horeca",
    },
    {
        "slug": "mos-cafe",
        "source_glob": "In the heart of apricot season, we produced a conceptual shoot for Mos Cafe*",
        "heading": '<p><i>M</i>os Cafe — conceptual apricot season shoot</p>',
        "hashtag": "#creative",
    },
    {
        "slug": "maura",
        "source_glob": "Maura continues to be one of our strongest partners*",
        "heading": '<p><i>M</i>aura — premium sushi partner in food</p>',
        "hashtag": "#foodandbev",
    },
    {
        "slug": "persona",
        "source_glob": "Partnership at a Higher Level- Roots x Persona*",
        "heading": '<p><i>P</i>ersona — Roots x top Yerevan restaurant</p>',
        "hashtag": "#horeca",
    },
    {
        "slug": "extra-virgin",
        "source_glob": "Partnership for Excellence- Roots and Extra Virgin*",
        "heading": '<p><i>E</i>xtra Virgin — restaurant partnership for excellence</p>',
        "hashtag": "#horeca",
    },
    {
        "slug": "beyond-horeca",
        "source_glob": "Roots Marketing Agency has expanded beyond the HoReCa sector*",
        "heading": '<p><i>R</i>oots — expanding beyond HoReCa into product</p>',
        "hashtag": "#branding",
    },
    {
        "slug": "kids-city",
        "source_glob": "Roots x Kids City- A Partnership from Day One*",
        "heading": '<p><i>K</i>ids City — brand brought to life from day one</p>',
        "hashtag": "#branding",
    },
    {
        "slug": "vici-cigar-lounge",
        "source_glob": "Vici Cigar Lounge is one of the finest concepts*",
        "heading": '<p><i>V</i>ici Cigar Lounge — curated concept at Trio Yerevan</p>',
        "hashtag": "#lifestyle",
    },
]


def find_source(glob_pattern: str) -> Path:
    matches = sorted(CLIENTS.glob(glob_pattern + ".mp4"))
    if not matches:
        raise FileNotFoundError(f"No MP4 for pattern: {glob_pattern}")
    return matches[0]


def make_poster(video: Path, poster: Path) -> None:
    poster.parent.mkdir(parents=True, exist_ok=True)
    ffmpeg = shutil.which("ffmpeg") or r"C:\Users\payam\AppData\Local\Microsoft\WinGet\Links\ffmpeg.exe"
    cmd = [
        ffmpeg,
        "-y",
        "-ss",
        "0.5",
        "-i",
        str(video),
        "-vframes",
        "1",
        "-vf",
        "scale=320:560:force_original_aspect_ratio=increase,crop=320:560",
        str(poster),
    ]
    result = subprocess.run(cmd, capture_output=True, text=True)
    if result.returncode != 0:
        raise RuntimeError(result.stderr.strip() or "poster generation failed")


def card_html(index: int, item: dict[str, str], video_url: str, poster_url: str) -> str:
    return f"""
<div class="workCard sliderCard">
    <div class="workCard__media" data-vid="{video_url}" data-poster="{poster_url}">
            </div>
    <div class="workCard__content">
        <p class="subheading">We partnered with</p>
        <h3 class="workCard__heading h0">{item["heading"]}</h3>
        <p class="workCard__hashtag">{item["hashtag"]}</p>
    </div>
</div>"""


def slider_block(cards: list[str]) -> str:
    visible = cards[:INITIAL_VISIBLE]
    pending = cards[INITIAL_VISIBLE:]
    cta = """
<div class="workCard sliderCard workCard--cta workCard--seeMore">
                            <button type="button" class="workCard__media buttonHover roots-work-seeMore" aria-label="See more previous work">
                                <span class="arrowButton" >
                                    <span class="arrowButton__arrow"><img src="/ui/button_arrow.svg" /><img src="/ui/button_arrow.svg" /></span><span class="arrowButton__label" data-content="See More"><span>See More</span></span>
                                </span>
                            </button>
                        </div>"""
    pending_block = ""
    if pending:
        pending_block = (
            '\n            <div id="roots-work-pending" hidden aria-hidden="true">\n'
            + "\n".join(pending)
            + "\n            </div>"
        )
    return "\n".join(visible) + cta + pending_block


def main() -> None:
    VIDEO_OUT.mkdir(parents=True, exist_ok=True)
    POSTER_OUT.mkdir(parents=True, exist_ok=True)

    cards: list[str] = []
    for i, item in enumerate(WORK_ITEMS, start=1):
        n = f"{i:02d}"
        src = find_source(item["source_glob"])
        dest_video = VIDEO_OUT / f"roots-agency-work-{n}.mp4"
        dest_poster = POSTER_OUT / f"roots-agency-work-{n}.webp"

        print(f"Copy {src.name} -> {dest_video.name}")
        shutil.copy2(src, dest_video)
        print(f"Poster {dest_poster.name}")
        make_poster(dest_video, dest_poster)

        video_url = f"/media/video/work/roots-agency-work-{n}.mp4"
        poster_url = f"/media/images/work/roots-agency-work-{n}.webp"
        cards.append(card_html(i, item, video_url, poster_url))

    block = slider_block(cards)
    out = ROOT / "dev/work-slider-cards.fragment.html"
    out.write_text(block + "\n", encoding="utf-8")
    print(f"Wrote {out} ({len(WORK_ITEMS)} cards, {INITIAL_VISIBLE} visible)")

    body_path = ROOT / "app/Views/pages/home/rhythm-influence-body.html"
    body = body_path.read_text(encoding="utf-8")
    body = re.sub(r"workSlider__height--\d+", "workSlider__height--6", body, count=1)
    pattern = re.compile(
        r'<div class="workCard sliderCard">.*?'
        r'(?:<div id="roots-work-pending" hidden aria-hidden="true">.*?</div>\s*)?'
        r'</div>\s*</div>\s*</div>\s*</div>\s*</section>\s*\n\s*<section class="companyData">',
        re.DOTALL,
    )
    replacement = block + "\n                                    </div>\n            </div>\n        </div>\n    </div>\n</section>                        \n\n<section class=\"companyData\">"
    new_body, count = pattern.subn(replacement, body, count=1)
    if count != 1:
        raise SystemExit(f"Expected 1 body replacement, got {count}")
    body_path.write_text(new_body, encoding="utf-8")
    print(f"Updated {body_path.name}")


if __name__ == "__main__":
    main()
