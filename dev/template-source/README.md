# Sync Rhythm Influence template assets (capture workflow)

Downloads the template homepage, vendored CSS/JS bundles, UI SVGs, and optional media URLs into gitignored staging.

## Quick capture (homepage mirror)

```powershell
cd RootsAgency
.\dev\scripts\sync\sync-rhythm-influence-assets.ps1
php test\smoke.php
python serve.py
```

Open **http://127.0.0.1:8013/** and compare side-by-side with [rhythminfluence.com](https://www.rhythminfluence.com/).

## What gets committed

| Path | Purpose |
|------|---------|
| `public/dist/style.min.css` | Vendored template stylesheet (exact bundle) |
| `public/dist/scripts.min.js` | Vendored template motion/interaction bundle |
| `public/ui/` | Local UI SVGs (`button_arrow.svg`, `footer_bg_light.svg`) |
| `app/Views/pages/home/home-body.php` | Homepage markup (PHP view; bundle BEM classes preserved) |
| `dev/template-source/*.raw.html` | Reference snapshots (not deployed) |

## Media (phase 2)

Hero videos and case-study images still load from the template CDN in v1 so motion parity is immediate. Run `-DownloadMedia` to mirror into `public/media/` and rewrite URLs in the body fragment.

```powershell
.\dev\scripts\sync\sync-rhythm-influence-assets.ps1 -DownloadMedia
```

After download, run `dev/scripts/build/rewrite-template-media-paths.php` (when added) before deploy.

## Re-capture after template updates

1. Re-run this script.
2. Diff `dev/template-source/rhythm-influence-home.raw.html` vs previous capture.
3. Re-extract body fragment into `app/Views/pages/home/home-body.php`.
4. Bump `bundle_asset_version` in `app/Config/site-config.php` if dist hashes change.
