<?php
declare(strict_types=1);

/**
 * Single revision signal for sitemap {@see https://www.sitemaps.org/protocol.html lastmod}.
 * Uses max filemtime of authoritative content/config/i18n/SEO PHP sources and SEO raster assets.
 */
if (!function_exists('roots_site_revision_gmtime')) {
    function roots_site_revision_gmtime(): int
    {
        $root = dirname(__DIR__, 2);
        $paths = [
            $root . '/app/Config/site-config.php',
            $root . '/app/Config/site-pages.php',
            $root . '/app/Config/locales.php',
            $root . '/app/Config/public-seo-assets.php',
            $root . '/app/Support/seo-social-meta.php',
            $root . '/app/Support/seo-jsonld.php',
        ];

        $times = [];
        foreach ($paths as $path) {
            if (is_file($path)) {
                $t = @filemtime($path);
                if ($t !== false) {
                    $times[] = $t;
                }
            }
        }

        foreach (glob($root . '/app/Lang/*.json') ?: [] as $langFile) {
            if (is_file($langFile)) {
                $t = @filemtime($langFile);
                if ($t !== false) {
                    $times[] = $t;
                }
            }
        }

        static $assetMtimes = null;
        if ($assetMtimes === null) {
            $seo = require $root . '/app/Config/public-seo-assets.php';
            $assetMtimes = [];
            foreach (['og_image_relative'] as $key) {
                $p = $root . '/public' . (string) ($seo[$key] ?? '');
                $t = is_file($p) ? @filemtime($p) : false;
                if ($t !== false) {
                    $assetMtimes[] = $t;
                }
            }
        }
        foreach ($assetMtimes as $mt) {
            $times[] = $mt;
        }

        return $times === [] ? time() : max($times);
    }
}

if (!function_exists('roots_site_revision_w3c_datetime')) {
    function roots_site_revision_w3c_datetime(): string
    {
        return gmdate('Y-m-d\TH:i:s\Z', roots_site_revision_gmtime());
    }
}
