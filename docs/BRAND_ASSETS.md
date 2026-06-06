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

## Logo

| File | Use |
|------|-----|
| `public/media/images/brand/roots-agency-logo.svg` | Full lockup (wordmark + tagline) — used in nav |
| `public/media/images/brand/roots-agency-logo-nav.svg` | Optional wordmark-only crop (not used by default) |

Source: client-provided `Clients/logo.svg` (light `#F5F5F5` paths on transparent — suited to dark nav chrome).

## Nav sizing

Vertically centered in the nav bar with **equal top/bottom gap** — `6rem` logo in `8.8rem` bar (desktop), `4rem` in `7.5rem` bar (mobile). Overrides template `margin-top: -0.5rem` which optically shifted the single-line Rhythm logo up.

## Theme overrides

Black → navy mapping lives in `public/features/roots-theme.css` (loaded after vendored template CSS). Do not edit `public/dist/style.min.css` directly — re-sync from template and keep overrides in `roots-theme.css`.

## Still template (phase 1)

Homepage body copy, case studies, and footer legal line remain **Rhythm Influence** template content until Roots copy replaces them in phase 2.
