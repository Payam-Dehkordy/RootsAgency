<?php
declare(strict_types=1);

if (!function_exists('current_locale')) {
    function current_locale(): string
    {
        return $GLOBALS['site_locale'] ?? 'en';
    }
}

if (!function_exists('locale_config')) {
    /** @return array{default: string, prefix: array<string, string>, html_lang: array<string, string>, text_direction: array<string, string>, og_locale: array<string, string>} */
    function locale_config(): array
    {
        static $c;

        if ($c === null) {
            $c = require __DIR__ . '/../Config/locales.php';
        }

        return $c;
    }
}

if (!function_exists('roots_public_locale_codes')) {
    /** @return list<string> */
    function roots_public_locale_codes(): array
    {
        $cfg = locale_config();

        return array_values(array_unique(array_merge(
            [(string) ($cfg['default'] ?? 'en')],
            array_keys((array) ($cfg['prefix'] ?? []))
        )));
    }
}

if (!function_exists('detect_site_locale')) {
    function detect_site_locale(): string
    {
        $cfg = locale_config();
        $raw = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($raw, PHP_URL_PATH);
        if ($path === null || $path === false || $path === '') {
            $path = '/';
        }
        $path = rawurldecode($path);
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }
        $path = rtrim($path, '/') ?: '/';

        foreach ($cfg['prefix'] as $locale => $segment) {
            if ($path === '/' . $segment || str_starts_with($path, '/' . $segment . '/')) {
                return $locale;
            }
        }

        return $cfg['default'];
    }
}

if (!function_exists('locale_url_prefix')) {
    function locale_url_prefix(?string $locale = null): string
    {
        $locale = $locale ?? current_locale();
        $cfg = locale_config();
        if ($locale === $cfg['default']) {
            return '';
        }
        $seg = $cfg['prefix'][$locale] ?? null;

        return $seg !== null ? '/' . $seg : '';
    }
}

if (!function_exists('locale_html_lang')) {
    function locale_html_lang(?string $locale = null): string
    {
        $locale = $locale ?? current_locale();
        $cfg = locale_config();

        return $cfg['html_lang'][$locale] ?? $cfg['html_lang'][$cfg['default']];
    }
}

if (!function_exists('locale_text_direction')) {
    function locale_text_direction(?string $locale = null): string
    {
        $locale = $locale ?? current_locale();
        $cfg = locale_config();

        return $cfg['text_direction'][$locale] ?? 'ltr';
    }
}

if (!function_exists('localized_path')) {
    function localized_path(string $path): string
    {
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }
        $prefix = locale_url_prefix();

        return $prefix === '' ? $path : $prefix . $path;
    }
}

if (!function_exists('localized_canonical_path')) {
    function localized_canonical_path(string $canonicalPath, ?string $locale = null): string
    {
        $locale = $locale ?? current_locale();
        $prefix = locale_url_prefix($locale);
        if ($canonicalPath === '/') {
            return $prefix === '' ? '/' : $prefix;
        }

        return $prefix . $canonicalPath;
    }
}

if (!function_exists('public_page_path')) {
    /** Relative canonical path for a page in a given locale (e.g. `/`, `/hy`, `/ru`). */
    function public_page_path(string $pageKey, ?string $locale = null): string
    {
        $pages = site_pages_registry();
        if (!isset($pages[$pageKey])) {
            return localized_canonical_path('/', $locale);
        }

        return localized_canonical_path((string) $pages[$pageKey]['canonical_path'], $locale);
    }
}

if (!function_exists('public_page_url')) {
    function public_page_url(array $site, string $pageKey, ?string $locale = null): string
    {
        $pages = site_pages_registry();
        if (!isset($pages[$pageKey])) {
            return rtrim((string) $site['base_url'], '/') . '/';
        }
        $path = localized_canonical_path((string) $pages[$pageKey]['canonical_path'], $locale);

        return rtrim((string) $site['base_url'], '/') . $path;
    }
}

if (!function_exists('roots_og_locale_content')) {
    function roots_og_locale_content(string $localeCode): string
    {
        $cfg = locale_config();
        $og = (array) ($cfg['og_locale'] ?? []);

        return $og[$localeCode] ?? $og[$cfg['default']] ?? 'en_US';
    }
}

if (!function_exists('language_switcher_entries')) {
    /** @return list<array{href: string, label: string, lang: string, hreflang: string}> */
    function language_switcher_entries(array $site, string $pageKey): array
    {
        $cfg = locale_config();
        $cur = current_locale();
        $out = [];

        foreach (roots_public_locale_codes() as $code) {
            if ($code === $cur) {
                continue;
            }
            $out[] = [
                'href' => public_page_path($pageKey, $code),
                'label' => tr('lang_switch.' . $code),
                'lang' => $cfg['html_lang'][$code] ?? $code,
                'hreflang' => $code,
            ];
        }

        return $out;
    }
}

if (!function_exists('hreflang_link_specs')) {
    /** @return list<array{href: string, hreflang: string}> */
    function hreflang_link_specs(array $site, string $pageKey): array
    {
        $cfg = locale_config();
        $specs = [];
        foreach (roots_public_locale_codes() as $code) {
            $specs[] = [
                'href' => public_page_url($site, $pageKey, $code),
                'hreflang' => $code,
            ];
        }
        $specs[] = [
            'href' => public_page_url($site, $pageKey, $cfg['default']),
            'hreflang' => 'x-default',
        ];

        return $specs;
    }
}

if (!function_exists('roots_locale_path_prefixes_csv')) {
    /** Comma-separated URL segments for locale home roots — consumed by client scripts. */
    function roots_locale_path_prefixes_csv(): string
    {
        return implode(',', array_values(locale_config()['prefix']));
    }
}

if (!function_exists('roots_google_fonts_css_url')) {
    /**
     * One Google Fonts CSS request: brand stack + navbar `a:lang()` faces + locale body stack.
     */
    function roots_google_fonts_css_url(): string
    {
        $locale = current_locale();

        $families = [
            'family=Archivo:wght@100..900',
            $locale === 'hy'
                ? 'family=Noto+Sans+Armenian:wght@100..900'
                : 'family=Noto+Sans+Armenian:wght@400;500;600;700',
            'family=Noto+Sans:wght@400;500;600;700',
            'display=swap',
        ];

        return 'https://fonts.googleapis.com/css2?' . implode('&', $families);
    }
}
