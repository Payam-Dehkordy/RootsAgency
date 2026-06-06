# Roots Agency — concept site (Rhythm Influence template)

B2B concept demo for **Roots Agency** (Yerevan). Phase 1 mirrors the [Rhythm Influence](https://www.rhythminfluence.com/) homepage **exactly** — same markup classes, vendored CSS/JS bundle, and template copy — on the lightweight PHP shell used by NeoGym / LadyZone / Byuregh.

## Local preview

```bash
cd RootsAgency
php test/smoke.php
python serve.py
```

Open **http://127.0.0.1:8013/** and compare with the live template in a split view.

## Architecture (v1 — template mirror)

| Layer | Approach |
|-------|----------|
| **Markup** | Captured `<body>` from template (`app/Views/pages/home/rhythm-influence-body.html`) |
| **Styles** | Vendored `public/dist/style.min.css` (same bundle as template) |
| **Motion** | Vendored `public/dist/scripts.min.js` + Lenis smooth scroll |
| **Media** | Template CDN URLs in v1; optional local mirror via sync script |
| **Stack** | PHP 8.x router + static `public/` — no Webflow/Craft runtime |

Phase 2 will decompose sections into PHP partials and `public/features/` CSS (LadyZone workflow) once Roots branding replaces template copy.

## Docs

- [PROJECT_KICKOFF.md](docs/PROJECT_KICKOFF.md)
- [TEMPLATE_REFERENCE_WORKFLOW.md](docs/TEMPLATE_REFERENCE_WORKFLOW.md)
- [dev/template-source/README.md](dev/template-source/README.md)

## Re-sync template

```powershell
.\dev\scripts\sync\sync-rhythm-influence-assets.ps1
```
