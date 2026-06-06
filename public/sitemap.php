<?php
declare(strict_types=1);

header('Content-Type: application/xml; charset=UTF-8');

require_once dirname(__DIR__) . '/app/Support/page-routes.php';
require_once dirname(__DIR__) . '/app/Support/locale.php';
require_once dirname(__DIR__) . '/app/Support/site-revision.php';

$site = require dirname(__DIR__) . '/app/Config/site-config.php';
$base = rtrim((string) ($site['base_url'] ?? ''), '/');
$paths = roots_sitemap_paths();
$lastmod = roots_site_revision_w3c_datetime();

$locales = roots_public_locale_codes();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($locales as $locale): ?>
<?php foreach ($paths as $p): ?>
  <url>
    <loc><?= htmlspecialchars($base . localized_canonical_path($p, $locale), ENT_QUOTES, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($lastmod, ENT_QUOTES, 'UTF-8') ?></lastmod>
  </url>
<?php endforeach; ?>
<?php endforeach; ?>
</urlset>
