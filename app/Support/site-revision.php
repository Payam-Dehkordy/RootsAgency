<?php
declare(strict_types=1);

if (!function_exists('roots_site_revision_gmtime')) {
    function roots_site_revision_gmtime(): int
    {
        $root = dirname(__DIR__, 2);
        $paths = [
            $root . '/app/Config/site-config.php',
            $root . '/app/Config/site-pages.php',
            $root . '/app/Config/locales.php',
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

        return $times === [] ? time() : max($times);
    }
}

if (!function_exists('roots_site_revision_w3c_datetime')) {
    function roots_site_revision_w3c_datetime(): string
    {
        return gmdate('Y-m-d\TH:i:s\Z', roots_site_revision_gmtime());
    }
}
