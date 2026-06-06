# Roots Agency — brand assets

## Colors

| Token | Hex | Use |
|-------|-----|-----|
| **Brand navy (primary)** | `#011F39` | Page background, footer, dark sections, media cards, overlays — replaces template black (`#000`) and near-black (`#202020`) |
| Logo mark / wordmark | `#F5F5F5` | Light paths on logo SVG (on dark nav) |

CSS variables (in `public/features/roots-brand.css` and `roots-theme.css`):

- `--roots-brand-navy: #011F39`
- `--roots-brand-navy-rgb: 1, 31, 57`

Template accent colors (Rhythm Influence — unchanged in phase 1): `#bb70ad`, `#8ec6b5`, `#d6d6d6` menu overlay, etc.

## Typography

| File | Family | Use |
|------|--------|-----|
| `public/fonts/WorkhorseScriptTest-Display.woff2` | Workhorse | Stylized first letters — `<i>M</i>atch`, `<i>m</i>arketing` inside `.h0` |
| `public/fonts/aeonik-*.woff2` | Aeonik | Body + headline sans (template default) |

Display caps use `<i>` inside `.h0` headings (not semantic emphasis). `roots-brand.css` sets `font-style: normal` so Workhorse is not browser-italicized.

## Slogan & hero copy

| Key | Text | Use |
|-----|------|-----|
| **Slogan** | Your Business Next Level | Hero `h1` |
| **Detailed** | Our agency prides itself on helping you take your business to the next level. | Hero subcopy, `meta_description` |
| Logo tagline | Marketing and Creative | Nav logo SVG only |

Canonical doc: `content/COPY.md`. Config keys: `tagline_slogan`, `tagline_detailed` in `app/Config/site-config.php`.

## Logo

| File | Use |
|------|-----|
| `public/media/images/brand/roots-agency-logo.svg` | Full lockup (wordmark + tagline) — nav |
| `public/media/images/brand/roots-agency-favicon.svg` | O-root mark — transparent, `#F5F5F5` fill |

### Hero video (local)

| File | Use |
|------|-----|
| `public/media/video/hero/roots-agency-hero-01.mp4` … `04.mp4` | Homepage hero column videos |

Source: client Facebook exports (originals kept outside repo until needed for phase 2).

### Media sentence images (WebP)

| File | Use |
|------|-----|
| `public/media/images/home/roots-agency-media-sentence-01.webp` … `05.webp` | Inline scroll sentence (1×) |
| `public/media/images/home/roots-agency-media-sentence-01@2x.webp` … `05@2x.webp` | Retina (2×) |

Source: client JPG exports → ImageMagick (`124×88` / `248×176`, quality 86).

Logo source: client-provided `Clients/logo.svg` (light `#F5F5F5` paths on transparent — suited to dark nav chrome).

### Previous work slider (local)

| File | Use |
|------|-----|
| `public/media/video/work/roots-agency-work-01.mp4` … `10.mp4` | Case study scroll videos |
| `public/media/images/work/roots-agency-work-01.webp` … `10.webp` | Card posters (320×560 WebP) |

Source: client Facebook exports → `dev/build-work-slider.py` (copy + ffmpeg poster frame).

## Nav sizing

Vertically centered in the nav bar with **equal top/bottom gap** — `6rem` logo in `8.8rem` bar (desktop), `4rem` in `7.5rem` bar (mobile). Overrides template `margin-top: -0.5rem` which optically shifted the single-line Rhythm logo up.

## Theme overrides

Black → navy mapping lives in `public/features/roots-theme.css` (loaded after vendored template CSS). Do not edit `public/dist/style.min.css` directly — re-sync from template and keep overrides in `roots-theme.css`.

## Still template (phase 1)

Hero headline, subcopy, hero videos, **media sentence**, and **previous work slider** are **Roots Agency**. Services blocks and footer legal line remain **Rhythm Influence** template content until phase 2.
