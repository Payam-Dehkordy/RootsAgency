<?php
declare(strict_types=1);

require_once __DIR__ . '/locale.php';

/**
 * Compare flat `app/Lang/{locale}.json` maps: non-default locales must define every key present in the default locale file.
 *
 * @return array<string, array{missing: list<string>, extra: list<string>}>
 */
function roots_locale_key_parity_scan(string $projectRoot): array
{
    $cfg = locale_config();
    $ref = (string) ($cfg['default'] ?? 'en');
    $refPath = $projectRoot . '/app/Lang/' . $ref . '.json';
    $rawRef = is_file($refPath) ? file_get_contents($refPath) : false;
    $refData = is_string($rawRef) ? json_decode($rawRef, true) : null;
    if (!is_array($refData)) {
        return ['_error' => ['missing' => ['failed to read reference locale: ' . $refPath], 'extra' => []]];
    }

    $refKeys = [];
    foreach ($refData as $k => $_) {
        if (is_string($k)) {
            $refKeys[] = $k;
        }
    }
    sort($refKeys);

    $out = [];
    foreach (roots_public_locale_codes() as $locale) {
        if (!is_string($locale) || $locale === $ref) {
            continue;
        }
        $path = $projectRoot . '/app/Lang/' . $locale . '.json';
        $raw = is_file($path) ? file_get_contents($path) : false;
        $data = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($data)) {
            $out[$locale] = [
                'missing' => $refKeys,
                'extra' => [],
            ];
            continue;
        }
        $missing = [];
        foreach ($refKeys as $key) {
            if (!array_key_exists($key, $data)) {
                $missing[] = $key;
            }
        }
        $extra = [];
        foreach (array_keys($data) as $key) {
            if (is_string($key) && !array_key_exists($key, $refData)) {
                $extra[] = $key;
            }
        }
        sort($missing);
        sort($extra);
        $out[$locale] = ['missing' => $missing, 'extra' => $extra];
    }

    return $out;
}
