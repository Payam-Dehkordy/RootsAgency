<?php
declare(strict_types=1);

$page_key = $GLOBALS['page_key'] ?? ($page_key ?? 'home');
$include_hreflang = $GLOBALS['include_hreflang'] ?? false;
?>
<!doctype html>
<html class="roots-locale-<?= h(current_locale()) ?>  is-animating" lang="<?= h(locale_html_lang()) ?>" dir="<?= h(locale_text_direction()) ?>" data-roots-locale-home-prefixes="<?= h(roots_locale_path_prefixes_csv()) ?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="google" content="notranslate">
  <meta name="chrome" content="nointentdetection">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="<?= h(roots_google_fonts_css_url()) ?>" rel="stylesheet">

  <title><?= h($page_title ?? tr('pages.home.title')) ?></title>
  <meta name="description" content="<?= h($meta_description ?? '') ?>">
  <link rel="canonical" href="<?= h($canonical_url ?? '') ?>">
<?php if (isset($site)): ?>
<?= roots_social_meta_html($site) ?>
<?= roots_json_ld_script_html($site) ?>
<?php endif; ?>
<?php if ($include_hreflang && isset($site)): ?>
<?php foreach (hreflang_link_specs($site, (string) $page_key) as $spec): ?>
  <link rel="alternate" href="<?= h($spec['href']) ?>" hreflang="<?= h($spec['hreflang']) ?>">
<?php endforeach; ?>
<?php endif; ?>

  <link rel="preload" href="<?= h(template_asset('/dist/scripts.min.js')) ?>" as="script">
  <link rel="preload" href="<?= h(template_asset('/dist/style.min.css')) ?>" as="style">
  <link rel="preload" href="<?= h(asset('/fonts/WorkhorseScriptTest-Display.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?= h(asset('/fonts/aeonik-regular.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" type="text/css" href="<?= h(template_asset('/dist/style.min.css')) ?>">

  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-layout.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-brand.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-nav.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-theme.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-locale-type-scale.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-locale-fonts.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-hero.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-media-sentence.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-work-slider.css')) ?>">
  <meta name="theme-color" content="<?= h((string) ($site['brand_color_primary'] ?? '#011F39')) ?>">

  <link rel="icon" href="<?= h(asset((string) ($site['favicon_relative'] ?? '/media/images/brand/roots-agency-favicon.svg'))) ?>" type="image/svg+xml">
  <script>
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }
    if (!window.location.hash) {
      window.scrollTo(0, 0);
    }
  </script>
  <?= $extra_head ?? '' ?>
<?php if (roots_is_local_preview()): ?>
  <script>
    (() => {
      try {
        if (localStorage.getItem('rootsAgencyDisableLiveReload') === '1') return;
        const q = new URLSearchParams(window.location.search);
        if (q.has('noreload')) {
          sessionStorage.setItem('rootsAgencyDisableLiveReload', '1');
          q.delete('noreload');
          const qs = q.toString();
          window.history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '') + window.location.hash);
        }
        if (sessionStorage.getItem('rootsAgencyDisableLiveReload') === '1') return;
      } catch (_) {}

      const markerUrl = '/__reload.txt';
      let lastValue = '';
      let reloadPollTimer = 0;
      const check = async () => {
        try {
          const res = await fetch(`${markerUrl}?v=${Date.now()}`, { cache: 'no-store' });
          if (!res.ok) return;
          const next = (await res.text()).trim();
          if (!next) return;
          if (lastValue && next !== lastValue) {
            if ('scrollRestoration' in history) {
              history.scrollRestoration = 'manual';
            }
            window.scrollTo(0, 0);
            window.location.reload();
            return;
          }
          lastValue = next;
        } catch (_) {}
      };
      const tick = async () => {
        if (document.hidden) return;
        await check();
      };
      const scheduleNextReloadPoll = () => {
        if (reloadPollTimer) window.clearTimeout(reloadPollTimer);
        reloadPollTimer = window.setTimeout(async () => {
          reloadPollTimer = 0;
          await tick();
          if (!document.hidden) scheduleNextReloadPoll();
        }, 900);
      };
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          if (reloadPollTimer) { window.clearTimeout(reloadPollTimer); reloadPollTimer = 0; }
        } else {
          void tick();
          scheduleNextReloadPoll();
        }
      }, { passive: true });
      void tick();
      scheduleNextReloadPoll();
    })();
  </script>
<?php endif; ?>
</head>
<body>
