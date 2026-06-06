<?php
declare(strict_types=1);

/**
 * Fail if any non-default locale JSON is missing keys present in the default locale.
 *
 *   php dev/scripts/i18n/check-locale-key-parity.php
 */

$root = dirname(__DIR__, 3);
require_once $root . '/app/Support/locale-key-parity.php';

$report = roots_locale_key_parity_scan($root);
$exit = 0;

if (isset($report['_error'])) {
    foreach ($report['_error']['missing'] as $line) {
        fwrite(STDERR, $line . "\n");
    }
    exit(1);
}

foreach ($report as $locale => $row) {
    $missing = $row['missing'] ?? [];
    $extra = $row['extra'] ?? [];
    foreach ($missing as $key) {
        fwrite(STDERR, "[{$locale}] missing key: {$key}\n");
        $exit = 1;
    }
    foreach ($extra as $key) {
        fwrite(STDERR, "[{$locale}] extra key (not in en.json): {$key}\n");
    }
}

if ($exit !== 0) {
    fwrite(STDERR, "\nLocale key parity check failed.\n");
}

exit($exit);
