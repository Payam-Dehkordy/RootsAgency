<?php
declare(strict_types=1);

require_once __DIR__ . '/web-helpers.php';

if (!function_exists('h')) {
    function h(string $s): string
    {
        return WebHelpers::escape($s);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return WebHelpers::asset($path, dirname(__DIR__, 2));
    }
}

$site = require __DIR__ . '/../Config/site-config.php';
require_once __DIR__ . '/site-request-base.php';
$site = site_config_with_request_base($site);
$GLOBALS['site'] = $site;

if (!function_exists('roots_is_local_preview')) {
    function roots_is_local_preview(): bool
    {
        return WebHelpers::isLocalPreview();
    }
}

if (!function_exists('template_asset')) {
    function template_asset(string $path): string
    {
        $version = (string) (($GLOBALS['site']['template_asset_version'] ?? '') ?: '');

        return asset($version !== '' ? $path . '?v=' . $version : $path);
    }
}

require_once __DIR__ . '/page-routes.php';
require_once __DIR__ . '/locale.php';
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/seo-jsonld.php';

if (!isset($GLOBALS['site_locale'])) {
    $GLOBALS['site_locale'] = detect_site_locale();
}

require_once __DIR__ . '/view-helpers.php';
require_once __DIR__ . '/page-bootstrap.php';
require_once __DIR__ . '/seo-social-meta.php';
