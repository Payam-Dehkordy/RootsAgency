<?php
declare(strict_types=1);

if (!function_exists('load_locale_strings')) {
    /**
     * @return array<string, string>
     */
    function load_locale_strings(string $locale): array
    {
        static $cache = [];

        if (isset($cache[$locale])) {
            return $cache[$locale];
        }

        $path = __DIR__ . '/../Lang/' . $locale . '.json';
        if (!is_file($path)) {
            return $cache[$locale] = [];
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return $cache[$locale] = [];
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return $cache[$locale] = [];
        }
        $flat = [];
        foreach ($data as $k => $v) {
            if (is_string($k) && is_string($v)) {
                $flat[$k] = $v;
            }
        }

        return $cache[$locale] = $flat;
    }
}

if (!function_exists('tr')) {
    function tr(string $key, string $default = ''): string
    {
        $dict = load_locale_strings(current_locale());

        return $dict[$key] ?? $default;
    }
}

if (!function_exists('tr_html')) {
    /** Trusted HTML from locale JSON only — never use with user input. */
    function tr_html(string $key, string $default = ''): string
    {
        return tr($key, $default);
    }
}
