# Roots Agency naming conventions

Follow the same **client-slug prefix** model as NeoGym (`ng_*`), LadyZone (`lz_*` / `ladyzone_*`), and Byuregh (`bsc_*`). See [LadyZone NAMING_CONVENTIONS.md](../../LadyZone/docs/NAMING_CONVENTIONS.md) for the shared pattern.

**Rule:** Third-party template names, sibling-project prefixes, and vendor capture identifiers must **not** appear in production code (`app/`, `public/` except vendored `dist/`, `test/`). Those references belong in `dev/` and `docs/` only.

Run the guard:

```bash
php dev/scripts/audit/check-production-naming.php
```

---

## 1. PHP

| Prefix / symbol | Use |
|-----------------|-----|
| **`roots_*`** | View helpers, SEO, locale, team/work renderers, routing helpers |
| **`h()`**, **`asset()`** | Global escapes and cache-busted Roots asset URLs |
| **`bundle_asset()`** | Versioned URLs for vendored `public/dist/*` (CSS/JS bundle) |
| **`bootstrap_*`** | Page bootstrap (unchanged pattern) |

**Views:** `app/Views/pages/home/home-body.php` (homepage markup), `app/Views/partials/roots-*.php`.

**Config keys:** `project_slug`, `bundle_asset_version` — no `template_*` or vendor site names.

---

## 2. CSS / JS filenames

| Pattern | Use |
|---------|-----|
| **`roots-{feature}.css`** | All custom stylesheets under `public/features/` |
| **`roots-{feature}.js`** | All custom scripts under `public/features/` |
| **`public/dist/style.min.css`**, **`scripts.min.js`** | Vendored homepage bundle (neutral paths; do not rename in URLs) |

New Roots-owned classes, IDs, and `data-*` hooks use the **`roots-`** prefix.

---

## 3. CSS classes and tokens

| Pattern | Use |
|---------|-----|
| **`.roots-*`** | All new components, layout shells, overrides scoped to Roots (`roots-footer`, `roots-hero`, `roots-team-card`, `roots-nav-lang`, …) |
| **`--roots-*`** | Design tokens (`--roots-brand-navy`, `--roots-nav-height`, …) |
| **Legacy bundle BEM** | Classes required by `public/dist/style.min.css` (e.g. `.workCard__*`, `.nav__*`) may remain in markup until a markup refactor; do **not** add new unprefixed component names in Roots CSS/JS/PHP |

---

## 4. HTML IDs and `data-*`

| Pattern | Use |
|---------|-----|
| **`#roots-*`** | New section hooks and pools (`#roots-work-pending`, …) |
| **`data-roots-*`** | Roots behavior (`data-roots-locale-home-prefixes`, …) |
| **Bundle IDs** | IDs required by vendored JS (`#slider-cards`, `#nav`, `#contact-form`, …) stay until bundle is replaced |

---

## 5. Media and UI assets

- **Brand / content:** `/media/images/…`, `/media/video/…` with **`roots-agency-*`** filenames
- **No** partner CDN hashes or vendor slug paths under `public/media/`
- Prefer moving generic UI SVGs from `/ui/` to `/media/ui/roots-agency-*` when touched

---

## 6. Forbidden in production (audit enforced)

Examples: `rhythm-influence`, `rhythminfluence`, `Rhythm Influence`, `NeoGym`, `LadyZone`, `Byuregh`, `webflow`, `Webflow`, `servd-rhythm`, `template_asset`, `template-source`, sibling prefixes (`ng_`, `lz_`, `bsc_`).

Allowed in **`dev/`** and **`docs/`** only.

---

## 7. See also

- [BRAND_ASSETS.md](BRAND_ASSETS.md) — tokens and media paths
- [TEMPLATE_REFERENCE_WORKFLOW.md](TEMPLATE_REFERENCE_WORKFLOW.md) — vendor capture (dev-only names)
- [PROJECT_KICKOFF.md](PROJECT_KICKOFF.md) — phase-1 bundle parity notes
