<?php
declare(strict_types=1);

function bootstrap_public_page(array $site, string $pageKey): void
{
    $pages = site_pages_registry();
    if (!isset($pages[$pageKey])) {
        throw new InvalidArgumentException('Unknown page key: ' . $pageKey);
    }

    $p = $pages[$pageKey];
    $canonical_path = localized_canonical_path((string) $p['canonical_path']);
    $GLOBALS['page_key'] = $pageKey;
    $GLOBALS['page_title'] = tr('pages.' . $pageKey . '.title');
    $GLOBALS['meta_description'] = tr('pages.' . $pageKey . '.meta_description');
    $GLOBALS['canonical_url'] = rtrim((string) $site['base_url'], '/') . $canonical_path;
    $GLOBALS['include_hreflang'] = !empty($p['include_hreflang']);
    $GLOBALS['extra_head'] = '';
}
