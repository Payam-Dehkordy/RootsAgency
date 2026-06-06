<?php
declare(strict_types=1);

if (!function_exists('roots_ensure_locale_config')) {
    function roots_ensure_locale_config(): void
    {
        if (!function_exists('locale_config')) {
            require_once __DIR__ . '/locale.php';
        }
    }
}

/**
 * @return array<string, array<string, mixed>>
 */
function site_pages_registry(): array
{
    static $pages;

    return $pages ??= require __DIR__ . '/../Config/site-pages.php';
}

/** @return array<string, string> */
function roots_public_routes(): array
{
    $pages = site_pages_registry();
    $routes = [];
    foreach ($pages as $key => $page) {
        if (!is_array($page)) {
            continue;
        }
        $path = (string) ($page['canonical_path'] ?? '');
        if ($path === '') {
            continue;
        }
        $routes[$path] = 'index.php';
    }

    $routes['/sitemap.xml'] = 'sitemap.php';

    return $routes;
}

/** @return array<string, string> */
function roots_router_paths(): array
{
    static $cache;

    return $cache ??= (function (): array {
        roots_ensure_locale_config();
        $base = roots_public_routes();
        $merged = $base;
        $cfg = locale_config();

        foreach (($cfg['prefix'] ?? []) as $segment) {
            $localePrefix = '/' . $segment;
            foreach ($base as $routePath => $file) {
                if ($routePath === '/sitemap.xml') {
                    continue;
                }
                $prefixedPath = $routePath === '/' ? $localePrefix : $localePrefix . $routePath;
                $merged[$prefixedPath] = $file;
            }
        }

        return $merged;
    })();
}

/** @return list<string> */
function roots_sitemap_paths(): array
{
    $pages = site_pages_registry();
    $paths = [];
    foreach ($pages as $page) {
        if (!is_array($page)) {
            continue;
        }
        $path = (string) ($page['canonical_path'] ?? '');
        if ($path !== '') {
            $paths[] = $path;
        }
    }

    return $paths;
}
