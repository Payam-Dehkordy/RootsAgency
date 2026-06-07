# Template reference workflow (Rhythm Influence → Roots Agency)

Reference URL: **https://www.rhythminfluence.com/**

We **do not guess** layout or motion. Capture first, then mirror or decompose.

---

## 0. Captured artifacts (2026-06-04)

| Output | Location |
|--------|----------|
| Raw homepage HTML | `dev/template-source/rhythm-influence-home.raw.html` |
| Vendored CSS bundle | `public/dist/style.min.css` |
| Vendored JS bundle | `public/dist/scripts.min.js` |
| Body fragment | `app/Views/pages/home/home-body.php` |
| Re-run script | `dev/scripts/sync/sync-rhythm-influence-assets.ps1` |

Template uses **Archivo** (Google Fonts), **Lenis** smooth scroll, and a custom Craft/Servd build — not Webflow.

---

## 1. Capture checklist

1. Full-page screenshots at 1440 / 1024 / 768 / 390 → `dev/scratch/screenshots/`
2. Update `docs/TEMPLATE_SECTION_INVENTORY.md` top-to-bottom
3. Network tab export for Img + Media → optional `-DownloadMedia` sync
4. Note animation hooks: `.anima`, `.flipLink`, `.scroll`, hero video grid, case-study slider

---

## 2. Parity verification

Side-by-side in Chrome:

1. Open template and local mirror at same viewport width.
2. Scroll through hero → case studies → services → brands marquee → footer.
3. Toggle mobile menu.
4. Compare hover states on nav flip links and arrow buttons.

Any drift → diff `home-body.php` against fresh raw capture; re-vend dist if template bumped asset hash.

---

## 3. Phase 2 decomposition order

When replacing mirror with modular PHP (LadyZone pattern):

1. `base.css` tokens from computed styles
2. Nav
3. Hero video mosaic + kinetic type
4. Case studies carousel
5. Services / talent-first band
6. Brand logo marquee
7. Footer + CTA

Do not start decomposition until mirror parity is signed off.
