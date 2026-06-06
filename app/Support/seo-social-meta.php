<?php
declare(strict_types=1);

/**
 * Open Graph + Twitter Card tags for HTML pages.
 * Reads {@see bootstrap_public_page} globals: page_title, meta_description, canonical_url, page_key.
 */
if (!function_exists('roots_og_locale_content')) {
    function roots_og_locale_content(string $localeCode): string
    {
        return match ($localeCode) {
            'en' => 'en_US',
            'hy' => 'hy_AM',
            'ru' => 'ru_RU',
            default => 'en_US',
        };
    }
}

if (!function_exists('roots_social_meta_html')) {
    function roots_social_meta_html(array $site): string
    {
        $title = (string) ($GLOBALS['page_title'] ?? 'Roots Agency');
        $desc = (string) ($GLOBALS['meta_description'] ?? '');
        $url = (string) ($GLOBALS['canonical_url'] ?? rtrim((string) ($site['base_url'] ?? ''), '/'));
        $brand = (string) ($site['brand'] ?? 'Roots Agency');
        $imageUrl = trim((string) ($site['og_default_image'] ?? ''));
        $imageAlt = (string) ($site['og_default_image_alt'] ?? $brand);

        $lines = [
            '<meta property="og:type" content="website">',
            '<meta property="og:site_name" content="' . h($brand) . '">',
            '<meta property="og:title" content="' . h($title) . '">',
            '<meta property="og:url" content="' . h($url) . '">',
        ];

        if ($desc !== '') {
            $lines[] = '<meta property="og:description" content="' . h($desc) . '">';
        }

        $cur = current_locale();
        $lines[] = '<meta property="og:locale" content="' . h(roots_og_locale_content($cur)) . '">';

        foreach (roots_public_locale_codes() as $code) {
            if ($code === $cur) {
                continue;
            }
            $lines[] = '<meta property="og:locale:alternate" content="' . h(roots_og_locale_content($code)) . '">';
        }

        if ($imageUrl !== '') {
            $lines[] = '<meta property="og:image" content="' . h($imageUrl) . '">';
            $lines[] = '<meta property="og:image:alt" content="' . h($imageAlt) . '">';
        }

        $isRasterOg = $imageUrl !== '' && preg_match('/\.(png|jpe?g|webp|gif)(\?|$)/i', $imageUrl) === 1;
        $twitterCard = $isRasterOg ? 'summary_large_image' : 'summary';
        $lines[] = '<meta name="twitter:card" content="' . h($twitterCard) . '">';
        $lines[] = '<meta name="twitter:title" content="' . h($title) . '">';
        if ($desc !== '') {
            $lines[] = '<meta name="twitter:description" content="' . h($desc) . '">';
        }
        if ($imageUrl !== '') {
            $lines[] = '<meta name="twitter:image" content="' . h($imageUrl) . '">';
            $lines[] = '<meta name="twitter:image:alt" content="' . h($imageAlt) . '">';
        }

        return implode("\n", $lines) . "\n";
    }
}
