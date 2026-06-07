<?php
declare(strict_types=1);

/**
 * Fail if forbidden template / sibling-project tokens appear outside dev/ and docs/.
 *
 *   php dev/scripts/audit/check-production-naming.php
 */

$root = dirname(__DIR__, 3);
$scanDirs = ['app', 'public', 'test'];

$patterns = [
    'rhythm-influence' => 'Rhythm Influence capture slug (dev/docs only)',
    'rhythminfluence' => 'Rhythm Influence domain slug',
    'Rhythm Influence' => 'Rhythm Influence vendor name',
    'servd-rhythm' => 'Rhythm CDN host',
    'NeoGym' => 'sibling project name',
    'neogym' => 'sibling project slug',
    'LadyZone' => 'sibling project name',
    'ladyzone' => 'sibling project slug',
    'Byuregh' => 'sibling project name',
    'byuregh' => 'sibling project slug',
    'webflow' => 'Webflow export vendor',
    'Webflow' => 'Webflow export vendor',
    'fit-and-you' => 'Fit & You template',
    'reshape' => 'ReShape template slug',
    'ReShape' => 'ReShape template name',
    'template_asset' => 'use bundle_asset() in production',
    'template_asset_version' => 'use bundle_asset_version in config',
    'template-source' => 'template snapshot path (dev/docs only)',
    'template_reference' => 'vendor reference (docs only)',
    'ng_contact' => 'NeoGym contact prefix',
    'lz_contact' => 'LadyZone contact prefix',
    'bsc_contact' => 'Byuregh contact prefix',
    '--ng-' => 'NeoGym design token',
    '--lz-' => 'LadyZone design token',
    '--bsc-' => 'Byuregh design token',
];

$skipPathContains = [
    '/public/dist/',
    DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR,
];

$violations = [];

foreach ($scanDirs as $dir) {
    $base = $root . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($base)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $rel = str_replace('\\', '/', substr($path, strlen($root) + 1));
        $skip = false;
        foreach ($skipPathContains as $needle) {
            if (str_contains($path, str_replace('/', DIRECTORY_SEPARATOR, $needle))
                || str_contains($rel, trim($needle, '/'))) {
                $skip = true;
                break;
            }
        }
        if ($skip || str_contains($rel, 'public/dist/')) {
            continue;
        }
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['php', 'css', 'js', 'json', 'html', 'yml', 'yaml', 'txt', 'md'], true)) {
            continue;
        }
        if (str_ends_with($rel, '.min.css') || str_ends_with($rel, '.min.js')) {
            continue;
        }
        $contents = file_get_contents($path);
        if ($contents === false) {
            continue;
        }
        foreach ($patterns as $token => $label) {
            if (stripos($contents, $token) === false) {
                continue;
            }
            $lines = preg_split('/\r\n|\n|\r/', $contents) ?: [];
            foreach ($lines as $i => $line) {
                if (stripos($line, $token) === false) {
                    continue;
                }
                $violations[] = sprintf('%s:%d  [%s] %s', $rel, $i + 1, $token, trim($line));
            }
        }
    }
}

if ($violations === []) {
    echo "Production naming check passed.\n";
    exit(0);
}

fwrite(STDERR, "Production naming check failed (" . count($violations) . " hits):\n");
foreach ($violations as $line) {
    fwrite(STDERR, $line . "\n");
}
exit(1);
