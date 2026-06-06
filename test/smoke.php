<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

foreach ([
    'public/index.php',
    'public/dist/style.min.css',
    'public/dist/scripts.min.js',
    'public/ui/button_arrow.svg',
    'public/media/images/brand/roots-agency-logo.svg',
    'public/media/images/brand/roots-agency-favicon.svg',
    'public/media/video/hero/roots-agency-hero-01.mp4',
    'public/media/video/hero/roots-agency-hero-02.mp4',
    'public/media/video/hero/roots-agency-hero-03.mp4',
    'public/media/video/hero/roots-agency-hero-04.mp4',
    'public/media/images/home/roots-agency-media-sentence-01.webp',
    'public/media/images/home/roots-agency-media-sentence-01@2x.webp',
    'public/media/images/home/roots-agency-media-sentence-05.webp',
    'public/media/images/home/roots-agency-media-sentence-05@2x.webp',
    'public/features/roots-brand.css',
    'public/features/roots-theme.css',
    'public/features/roots-hero-video.js',
    'app/Views/pages/home/rhythm-influence-body.html',
    'dev/template-source/rhythm-influence-home.raw.html',
] as $rel) {
    if (!is_file($root . '/' . $rel)) {
        $errors[] = 'Missing file: ' . $rel;
    }
}

$cssSize = @filesize($root . '/public/dist/style.min.css') ?: 0;
$jsSize = @filesize($root . '/public/dist/scripts.min.js') ?: 0;
if ($cssSize < 50000) {
    $errors[] = 'style.min.css looks too small — re-run template capture';
}
if ($jsSize < 50000) {
    $errors[] = 'scripts.min.js looks too small — re-run template capture';
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

echo "smoke ok\n";
