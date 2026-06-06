<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

foreach ([
    'public/index.php',
    'public/dist/style.min.css',
    'public/dist/scripts.min.js',
    'public/fonts/WorkhorseScriptTest-Display.woff2',
    'public/fonts/aeonik-regular.woff2',
    'public/ui/button_arrow.svg',
    'public/media/images/brand/roots-agency-logo.svg',
    'public/media/images/brand/roots-agency-favicon.svg',
    'public/media/video/hero/roots-agency-hero-01.mp4',
    'public/media/video/hero/roots-agency-hero-02.mp4',
    'public/media/video/hero/roots-agency-hero-03.mp4',
    'public/media/video/hero/roots-agency-hero-04.mp4',
    'public/media/video/work/roots-agency-work-01.mp4',
    'public/media/video/work/roots-agency-work-10.mp4',
    'public/media/images/work/roots-agency-work-01.webp',
    'public/media/images/work/roots-agency-work-10.webp',
    'public/media/images/services/roots-agency-services-team.webp',
    'public/media/images/services/roots-agency-services-team@2x.webp',
    'public/media/images/team/roots-agency-team-anahit-voskanyan.webp',
    'public/media/images/team/roots-agency-team-janna-taroyan.webp',
    'public/media/images/contact/roots-agency-contact-hero.webp',
    'public/media/images/contact/roots-agency-contact-hero@2x.webp',
    'public/media/images/footer/roots-agency-footer-instagram.webp',
    'public/ui/time_icon.svg',
    'app/Handlers/contact-form.php',
    'dev/sync-contact-assets.py',
    'dev/build-team-section.py',
    'public/media/images/home/roots-agency-media-sentence-01@2x.webp',
    'public/media/images/home/roots-agency-media-sentence-05.webp',
    'public/media/images/home/roots-agency-media-sentence-05@2x.webp',
    'public/features/roots-brand.css',
    'public/features/roots-theme.css',
    'public/features/roots-hero-video.js',
    'public/features/roots-scroll-top.js',
    'public/features/roots-nav-scroll.js',
    'public/features/roots-work-slider.js',
    'public/features/roots-contact-form.js',
    'public/features/roots-contact-header.js',
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
