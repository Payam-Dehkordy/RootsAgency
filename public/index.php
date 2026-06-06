<?php
declare(strict_types=1);

require __DIR__ . '/../app/Support/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && (($_POST['action'] ?? '') === 'contact-form/send')) {
    require __DIR__ . '/../app/Handlers/contact-form.php';
    exit;
}

$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocalPreview = str_contains($host, '127.0.0.1') || str_contains($host, 'localhost');
if ($isLocalPreview) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$assetVersion = (string) ($site['template_asset_version'] ?? '7c85f98c63f5e1f89737e800920875f74ad6abf9');
$bodyPath = dirname(__DIR__) . '/app/Views/pages/home/rhythm-influence-body.html';

if (!is_readable($bodyPath)) {
    http_response_code(500);
    echo 'Template body missing. Run dev capture workflow first.';
    exit;
}

?><!doctype html>
<html class="  is-animating" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="google" content="notranslate">
  <meta name="chrome" content="nointentdetection">

  <title><?= h((string) $site['page_title']) ?></title>
  <meta name="description" content="<?= h((string) $site['meta_description']) ?>">

  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= h((string) $site['page_title']) ?>">
  <meta property="og:description" content="<?= h((string) $site['meta_description']) ?>">
  <meta property="og:url" content="<?= h((string) $site['canonical_url']) ?>">
  <meta property="og:site_name" content="<?= h((string) $site['brand']) ?>">

  <link rel="preload" href="<?= h(asset('/dist/scripts.min.js?v=' . $assetVersion)) ?>" as="script">
  <link rel="preload" href="<?= h(asset('/dist/style.min.css?v=' . $assetVersion)) ?>" as="style">
  <link rel="preload" href="<?= h(asset('/fonts/WorkhorseScriptTest-Display.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?= h(asset('/fonts/aeonik-regular.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/dist/style.min.css?v=' . $assetVersion)) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-brand.css')) ?>">
  <link rel="stylesheet" type="text/css" href="<?= h(asset('/features/roots-theme.css')) ?>">
  <meta name="theme-color" content="<?= h((string) ($site['brand_color_primary'] ?? '#011F39')) ?>">

  <link rel="icon" href="<?= h(asset((string) ($site['favicon_relative'] ?? '/media/images/brand/roots-agency-favicon.svg'))) ?>" type="image/svg+xml">
  <script>
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);
  </script>
<?php if ($isLocalPreview): ?>
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
<?php readfile($bodyPath); ?>
</body>
</html>
