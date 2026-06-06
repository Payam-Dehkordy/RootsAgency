# Roots Agency — project kickoff

Concept website for **Roots Agency** (Yerevan marketing agency B2B partner). Visual reference: [**Rhythm Influence**](https://www.rhythminfluence.com/).

**Technical baseline:** Same lightweight PHP shell as NeoGym / LadyZone / Byuregh — see sibling `README.md` files under `Clients/`.

---

## 1. Confirmed

| Item | Value |
|------|--------|
| **Client / pitch** | Roots Agency (Yerevan) |
| **Template URL** | https://www.rhythminfluence.com/ |
| **Brand color** | `#011F39` (navy) — see [BRAND_ASSETS.md](BRAND_ASSETS.md) |
| **Content (v1)** | Same as template (Rhythm Influence copy) — Roots branding later |
| **Parity goal** | Indistinguishable design + animation in side-by-side live view |
| **Scale** | Full-scale (1:1 with template) |
| **Deploy target (planned)** | `roots-agency.payam-dehkordy.com` |

---

## 2. Implementation strategy

### Phase 1 — Template mirror (current)

1. Capture homepage HTML + dist CSS/JS from template.
2. Vend bundles under `public/dist/`.
3. Ship captured body markup class-for-class.
4. Keep template CDN media URLs until `-DownloadMedia` sync completes.

This preserves **exact** motion (scroll reveals, flip links, case-study carousel, hero video grid) because the original minified JS drives the same DOM.

### Phase 2 — Decompose (later)

- Split body into `app/Views/pages/home/section-*.php`
- Extract tokens to `public/features/base/base.css`
- Replace Rhythm copy with Roots Agency content
- Localize media under `public/media/` (WebP policy)

---

## 3. Open items

- [ ] Roots Agency logo + brand colors (replace Rhythm Influence chrome)
- [ ] Armenian / Russian locale need (if pitching local clients)
- [ ] Droplet vhost + GitHub Actions deploy (copy from NeoGym)
- [ ] Download all template media for offline deploy

---

## 4. See also

- [TEMPLATE_REFERENCE_WORKFLOW.md](TEMPLATE_REFERENCE_WORKFLOW.md)
- [readmap Yerevan B2B doc](../../readmap/docs/go-to-market/YEREVAN_AGENCY_B2B_PARTNERSHIP.md)
