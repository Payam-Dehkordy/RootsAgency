<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$php = PHP_BINARY;
$failures = [];

require_once $root . '/app/Support/web-helpers.php';
require_once $root . '/app/Support/locale-key-parity.php';
require_once $root . '/app/Support/page-routes.php';
require_once $root . '/app/Support/locale.php';

$siteCfg = require $root . '/app/Config/site-config.php';

$locCfg = locale_config();
$defaultLocaleCode = (string) ($locCfg['default'] ?? 'en');
$defaultLangRaw = @file_get_contents($root . '/app/Lang/' . $defaultLocaleCode . '.json');
$defaultLangDecoded = is_string($defaultLangRaw) ? json_decode($defaultLangRaw, true) : null;
/** @var array<string, mixed>|null */
$defaultLangArr = is_array($defaultLangDecoded) ? $defaultLangDecoded : null;
$defaultHtmlLang = (string) ($locCfg['html_lang'][$defaultLocaleCode] ?? $defaultLocaleCode);
$defaultTextDir = (string) ($locCfg['text_direction'][$defaultLocaleCode] ?? 'ltr');

$parityReport = roots_locale_key_parity_scan($root);
$parityOk = !isset($parityReport['_error']);
foreach ($parityReport as $locale => $parityRow) {
    if ($locale === '_error') {
        continue;
    }
    $parityOk = $parityOk && (($parityRow['missing'] ?? []) === []);
}
assert_true($parityOk, 'locale JSON has no missing keys vs default (en.json)', $failures);

foreach ([
    'public/index.php',
    'public/router.php',
    'public/sitemap.php',
    'public/robots.txt',
    'app/Support/app-init.php',
    'app/Support/site-request-base.php',
    'app/Support/seo-social-meta.php',
    'app/Support/seo-jsonld.php',
    'app/Support/locale-key-parity.php',
    'app/Views/partials/head.php',
    'public/dist/style.min.css',
    'public/dist/scripts.min.js',
    'app/Lang/en.json',
    'app/Lang/hy.json',
    'app/Lang/ru.json',
    'app/Views/pages/home/rhythm-influence-body.php',
    'public/features/roots-locale-fonts.css',
    'public/features/roots-locale-type-scale.css',
    'dev/scripts/generate-locale-type-scale-css.py',
    'public/features/roots-site-chrome.js',
    'public/features/roots-hero-video.js',
    'public/features/roots-layout.css',
    'public/features/roots-breakpoints.js',
    'public/features/roots-hero.css',
] as $rel) {
    assert_true(is_file($root . '/' . $rel), 'file exists: ' . $rel, $failures);
}

$cssSize = @filesize($root . '/public/dist/style.min.css') ?: 0;
$jsSize = @filesize($root . '/public/dist/scripts.min.js') ?: 0;
assert_true($cssSize >= 50000, 'style.min.css looks large enough', $failures);
assert_true($jsSize >= 50000, 'scripts.min.js looks large enough', $failures);

/**
 * @return array{exit:int,stdout:string,stderr:string}
 */
function run_php(string $php, string $code, string $cwd): array
{
    $tmp = tempnam(sys_get_temp_dir(), 'roots-smoke-');
    if ($tmp === false) {
        return ['exit' => 1, 'stdout' => '', 'stderr' => 'failed to create temp file'];
    }
    file_put_contents($tmp, "<?php\n" . $code . "\n");
    $cmd = escapeshellarg($php) . ' -d display_errors=1 ' . escapeshellarg($tmp);
    $desc = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $proc = proc_open($cmd, $desc, $pipes, $cwd);
    if (!is_resource($proc)) {
        return ['exit' => 1, 'stdout' => '', 'stderr' => 'failed to start process'];
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]) ?: '';
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]) ?: '';
    fclose($pipes[2]);
    $exit = proc_close($proc);
    @unlink($tmp);

    return ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr];
}

function assert_true(bool $ok, string $message, array &$failures): void
{
    if ($ok) {
        echo "[OK] {$message}\n";
        return;
    }
    echo "[FAIL] {$message}\n";
    $failures[] = $message;
}

/** @return list<string> */
function roots_smoke_ld_type_list(mixed $typeField): array
{
    if (is_string($typeField)) {
        return [$typeField];
    }
    if (!is_array($typeField)) {
        return [];
    }
    $out = [];
    foreach ($typeField as $t) {
        if (is_string($t) && $t !== '') {
            $out[] = $t;
        }
    }

    return $out;
}

/** @return array<int, mixed>|null */
function roots_smoke_first_json_ld_graph(string $html): ?array
{
    if (preg_match(
        '/<script\b[^>]*type="application\/ld\+json"[^>]*>(.*?)<\/script>/is',
        $html,
        $m,
    ) !== 1) {
        return null;
    }
    try {
        /** @var mixed $decoded */
        $decoded = json_decode(trim($m[1]), true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        return null;
    }
    if (!is_array($decoded) || ($decoded['@context'] ?? '') !== 'https://schema.org') {
        return null;
    }
    $graph = $decoded['@graph'] ?? null;

    return is_array($graph) ? $graph : null;
}

/** @param array<string, mixed> $siteCfg */
function roots_smoke_json_ld_document_ok(string $html, array $siteCfg): bool
{
    $base = rtrim((string) ($siteCfg['base_url'] ?? ''), '/');
    if ($base === '') {
        return false;
    }
    $graph = roots_smoke_first_json_ld_graph($html);
    if ($graph === null) {
        return false;
    }

    $hasOrg = false;
    $hasWebsite = false;
    foreach ($graph as $node) {
        if (!is_array($node)) {
            continue;
        }
        $types = roots_smoke_ld_type_list($node['@type'] ?? null);
        $id = (string) ($node['@id'] ?? '');
        if (
            in_array('Organization', $types, true)
            && in_array('MarketingAgency', $types, true)
            && $id === $base . '#organization'
        ) {
            $hasOrg = true;
        }
        if (!in_array('WebSite', $types, true) || $id !== $base . '#website') {
            continue;
        }
        $publisher = $node['publisher'] ?? null;
        if (!is_array($publisher) || (($publisher['@id'] ?? '') !== $base . '#organization')) {
            return false;
        }
        $langs = $node['inLanguage'] ?? null;
        if (!is_array($langs)) {
            return false;
        }
        $nonEmptyLang = false;
        foreach ($langs as $lang) {
            if (is_string($lang) && $lang !== '') {
                $nonEmptyLang = true;
                break;
            }
        }
        if (!$nonEmptyLang) {
            return false;
        }
        $hasWebsite = true;
    }

    return $hasOrg && $hasWebsite;
}

/** @param array<string, mixed> $siteCfg */
function roots_smoke_og_bundle_ok(string $html, array $siteCfg): bool
{
    $ogDefaultImage = (string) ($siteCfg['og_default_image'] ?? '');
    $brand = (string) ($siteCfg['brand'] ?? '');
    if ($ogDefaultImage === '' || $brand === '') {
        return false;
    }
    $brandEsc = WebHelpers::escape($brand);

    return str_contains($html, 'property="og:type" content="website"')
        && str_contains($html, 'property="og:site_name" content="' . $brandEsc . '"')
        && str_contains($html, 'property="og:title"')
        && str_contains($html, 'property="og:url"')
        && str_contains($html, 'property="og:description"')
        && str_contains($html, 'property="og:locale"')
        && str_contains($html, 'property="og:locale:alternate"')
        && str_contains($html, 'property="og:image"')
        && str_contains($html, 'property="og:image:alt"')
        && str_contains($html, WebHelpers::escape($ogDefaultImage))
        && str_contains($html, 'name="twitter:card" content="summary_large_image"')
        && str_contains($html, 'name="twitter:title"')
        && str_contains($html, 'name="twitter:description"')
        && str_contains($html, 'name="twitter:image"');
}

function roots_smoke_canonical_and_og_url_ok(string $html, string $expectedAbsoluteUrl): bool
{
    if ($expectedAbsoluteUrl === '') {
        return false;
    }
    $e = WebHelpers::escape($expectedAbsoluteUrl);

    return str_contains($html, '<link rel="canonical" href="' . $e . '">')
        && str_contains($html, 'property="og:url" content="' . $e . '"');
}

/** @param array<string, mixed>|null $dict */
function roots_smoke_home_document_shell_ok(
    string $out,
    ?array $dict,
    string $htmlLangAttr,
    string $textDirectionAttr,
    string $hreflangSelfLocale,
): bool {
    $pageTitle = is_array($dict) && isset($dict['pages.home.title']) ? (string) $dict['pages.home.title'] : '';
    $h1Count = preg_match_all('/<h1\b/i', $out);
    $titleNeedle = '<title>' . WebHelpers::escape($pageTitle) . '</title>';

    return $pageTitle !== ''
        && str_contains($out, $titleNeedle)
        && $h1Count === 1
        && str_contains($out, '/features/roots-layout.css')
        && str_contains($out, '/features/roots-locale-fonts.css')
        && str_contains($out, '/features/roots-locale-type-scale.css')
        && str_contains($out, '/features/roots-hero.css')
        && str_contains($out, 'data-roots-locale-home-prefixes="')
        && str_contains($out, 'lang="' . WebHelpers::escape($htmlLangAttr) . '"')
        && str_contains($out, 'dir="' . WebHelpers::escape($textDirectionAttr) . '"')
        && str_contains($out, 'hreflang="' . WebHelpers::escape($hreflangSelfLocale) . '"');
}

/** @param array<string, mixed> $siteCfg */
function roots_smoke_robots_txt_matches_site(string $projectRoot, array $siteCfg): bool
{
    $path = $projectRoot . '/public/robots.txt';
    $raw = @file_get_contents($path);
    if (!is_string($raw) || $raw === '') {
        return false;
    }
    $base = rtrim((string) ($siteCfg['base_url'] ?? ''), '/');
    if ($base === '') {
        return false;
    }
    $needle = 'Sitemap: ' . $base . '/sitemap.xml';
    if (!str_contains($raw, 'User-agent:') || !str_contains($raw, $needle)) {
        return false;
    }
    $allowCrawl = !empty($siteCfg['robots_allow_crawl']);

    return $allowCrawl
        ? str_contains($raw, 'Allow:')
        : str_contains($raw, 'Disallow:');
}

$routerNotFound = run_php(
    $php,
    '$_SERVER["REQUEST_URI"]="/no-such-page"; require "public/router.php";',
    $root
);
assert_true(
    $routerNotFound['exit'] === 0 && str_contains($routerNotFound['stdout'], '404 Not Found'),
    'router returns 404 body for unknown path',
    $failures
);

$routerStackedLocales = run_php(
    $php,
    '$_SERVER["REQUEST_URI"]="/hy/ru/"; require "public/router.php";',
    $root
);
assert_true(
    $routerStackedLocales['exit'] === 0
        && str_contains($routerStackedLocales['stdout'], '404 Not Found'),
    'router returns 404 for stacked locale prefixes',
    $failures
);

$routerSitemap = run_php(
    $php,
    '$_SERVER["REQUEST_URI"]="/sitemap.xml"; require "public/router.php";',
    $root
);
$sitemapXml = $routerSitemap['stdout'];
$sitemapOk = $routerSitemap['exit'] === 0
    && str_contains($sitemapXml, 'http://www.sitemaps.org/schemas/sitemap/0.9');
if ($sitemapOk) {
    $paths = roots_sitemap_paths();
    $localeCodes = roots_public_locale_codes();
    $base = rtrim((string) ($siteCfg['base_url'] ?? ''), '/');
    $expectedUrlCount = count($paths) * count($localeCodes);
    $lastmodCount = preg_match_all('/<lastmod>\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z<\/lastmod>/', $sitemapXml);
    $sitemapOk = $lastmodCount === $expectedUrlCount;
    foreach ($localeCodes as $locale) {
        foreach ($paths as $p) {
            $path = localized_canonical_path($p, $locale);
            $full = $base . $path;
            $locTag = '<loc>' . htmlspecialchars($full, ENT_QUOTES, 'UTF-8') . '</loc>';
            $sitemapOk = $sitemapOk && str_contains($sitemapXml, $locTag);
        }
    }
}
assert_true(
    $sitemapOk,
    'router serves /sitemap.xml with every locale × path loc and matching lastmod count',
    $failures
);

assert_true(
    roots_smoke_robots_txt_matches_site($root, $siteCfg),
    'public/robots.txt blocks crawl (preview) and Sitemap URL matches site-config base_url',
    $failures
);

$routerHome = run_php(
    $php,
    '$_SERVER["REQUEST_URI"]="/"; require "public/router.php";',
    $root
);
$homeHtml = $routerHome['stdout'];
$homeExitOk = $routerHome['exit'] === 0;

assert_true(
    $homeExitOk
        && roots_smoke_home_document_shell_ok(
            $homeHtml,
            $defaultLangArr,
            $defaultHtmlLang,
            $defaultTextDir,
            $defaultLocaleCode
        ),
    'router serves / with default-locale home title, single h1, locale fonts CSS, html lang, dir, and hreflang',
    $failures
);

$expectedHomeUrl = public_page_url($siteCfg, 'home', $defaultLocaleCode);
assert_true(
    $homeExitOk
        && roots_smoke_og_bundle_ok($homeHtml, $siteCfg)
        && roots_smoke_json_ld_document_ok($homeHtml, $siteCfg)
        && roots_smoke_canonical_and_og_url_ok($homeHtml, $expectedHomeUrl),
    'router serves / with OG/Twitter bundle, JSON-LD @graph, canonical matching og:url',
    $failures
);

$homeLangSwitchOk = $homeExitOk
    && !str_contains($homeHtml, 'roots-lang-link flipLink')
    && !str_contains($homeHtml, 'roots-lang-link is-current')
    && str_contains($homeHtml, 'class="roots-lang-link" hreflang="hy" lang="hy"')
    && str_contains($homeHtml, 'data-no-swup')
    && str_contains($homeHtml, '>Հայերեն</a>')
    && str_contains($homeHtml, '>Русский</a>')
    && !preg_match('/>English<\/a>/', $homeHtml)
    && str_contains($homeHtml, 'href="/hy"')
    && str_contains($homeHtml, 'href="/ru"');
assert_true(
    $homeLangSwitchOk,
    'router serves / with native lang switcher labels, relative hrefs, no flipLink, active locale hidden',
    $failures
);

$localeScaleOk = is_file($root . '/public/features/roots-locale-fonts.css')
    && is_file($root . '/public/features/roots-locale-type-scale.css')
    && str_contains((string) file_get_contents($root . '/public/features/roots-locale-fonts.css'), '--roots-type-scale: 0.75')
    && str_contains((string) file_get_contents($root . '/public/features/roots-locale-fonts.css'), '--roots-type-scale: 0.8')
    && !str_contains((string) file_get_contents($root . '/public/features/roots-locale-fonts.css'), '--roots-html-vw-desktop')
    && str_contains((string) file_get_contents($root . '/public/features/roots-locale-type-scale.css'), 'html[lang="hy"] #main .body')
    && str_contains((string) file_get_contents($root . '/public/features/roots-locale-type-scale.css'), 'calc(2rem * var(--roots-type-scale))')
    && !str_contains((string) file_get_contents($root . '/public/features/roots-locale-type-scale.css'), 'homeHeader');
assert_true(
    $localeScaleOk,
    'locale type scale applies to #main body copy; hero typography lives in roots-hero.css',
    $failures
);

$headPhp = (string) file_get_contents($root . '/app/Views/partials/head.php');
$heroCss = (string) file_get_contents($root . '/public/features/roots-hero.css');
$localeFontsCss = (string) file_get_contents($root . '/public/features/roots-locale-fonts.css');
$heroBody = (string) file_get_contents($root . '/app/Views/pages/home/rhythm-influence-body.php');
$heroLayoutOk = str_contains($heroCss, '--roots-hero-subcopy-margin-top: 18rem')
    && str_contains($heroCss, '/ var(--roots-type-scale, 1)')
    && str_contains($heroCss, 'calc(var(--roots-hero-heading-size) * var(--roots-type-scale, 1))')
    && str_contains($heroCss, '#home-header.roots-hero .homeHeader__content .body')
    && !str_contains($heroCss, 'html[lang="hy"]')
    && !str_contains($localeFontsCss, 'roots-page-end')
    && !str_contains($localeFontsCss, '#home-header')
    && str_contains($heroBody, 'class="homeHeader roots-hero')
    && str_contains($heroBody, '<p class="body anima fade" data-anima-delay="18">')
    && strpos($headPhp, 'roots-locale-fonts.css') < strpos($headPhp, 'roots-hero.css');
assert_true(
    $heroLayoutOk,
    'hero typography + EN-matched spacing in roots-hero.css only (excluded from locale-type-scale)',
    $failures
);

$heroVideoJs = (string) file_get_contents($root . '/public/features/roots-hero-video.js');
$heroVideoOk = substr_count($heroBody, 'homeHeader__mediaCard') >= 4
    && !str_contains($heroBody, 'onplaying=')
    && !str_contains($heroBody, 'autoplay muted loop playsinline')
    && str_contains($heroBody, 'muted loop playsinline preload="auto" class="media vid"')
    && str_contains($heroVideoJs, 'currentTime = 0.001')
    && !preg_match('/\.play\s*\(/', $heroVideoJs);
assert_true(
    $heroVideoOk,
    'hero videos use single seek-based primer (no inline onplaying/autoplay play races)',
    $failures
);

$layoutCss = (string) file_get_contents($root . '/public/features/roots-layout.css');
$themeCss = (string) file_get_contents($root . '/public/features/roots-theme.css');
$brandCss = (string) file_get_contents($root . '/public/features/roots-brand.css');
$breakpointsJs = (string) file_get_contents($root . '/public/features/roots-breakpoints.js');
$responsiveOk = str_contains($layoutCss, '--roots-vh: 100dvh')
    && str_contains($layoutCss, '--roots-brand-navy: #011F39')
    && str_contains($layoutCss, '--roots-page-end-height')
    && str_contains($layoutCss, '800px')
    && !str_contains($brandCss, '--roots-brand-navy:')
    && str_contains($themeCss, '#main section')
    && str_contains($themeCss, 'background-color: var(--roots-brand-navy)')
    && !str_contains($brandCss, 'max-width: 799px')
    && str_contains($breakpointsJs, 'mqMobileLayout')
    && str_contains($breakpointsJs, 'mqTemplateSliderMobile')
    && strpos($headPhp, 'roots-layout.css') < strpos($headPhp, 'roots-brand.css')
    && str_contains($headPhp, 'roots-nav.css')
    && str_contains($heroBody, '/features/roots-breakpoints.js');
assert_true(
    $responsiveOk,
    'responsive SSOT: roots-layout.css tokens + Rhythm 800px breakpoints + roots-breakpoints.js',
    $failures
);

$navCss = (string) file_get_contents($root . '/public/features/roots-nav.css');
$navDrawerOk = str_contains($navCss, 'translateX(100%)')
    && str_contains($navCss, '.nav__menuBg')
    && str_contains($navCss, 'roots-nav-lang--menu');
$navDrawerJs = (string) file_get_contents($root . '/public/features/roots-nav-drawer.js');
$navDrawerOk = $navDrawerOk
    && str_contains($heroBody, 'roots-nav-drawer.js')
    && str_contains($navDrawerJs, 'bindSwipeDismiss')
    && str_contains($navDrawerJs, 'bindScrollDismiss');
assert_true(
    $navDrawerOk,
    'mobile nav uses right-side drawer with Byuregh-style dismiss (roots-nav.css + roots-nav-drawer.js)',
    $failures
);

$workSliderCss = (string) file_get_contents($root . '/public/features/roots-work-slider.css');
$workSliderJs = (string) file_get_contents($root . '/public/features/roots-work-slider.js');
$workSliderOk = str_contains($headPhp, 'roots-work-slider.css')
    && str_contains($workSliderCss, 'roots-show-work-title')
    && str_contains($workSliderCss, 'margin-bottom: 10rem')
    && str_contains($workSliderJs, 'syncWorkSliderTitle');
assert_true(
    $workSliderOk,
    'previous work: title visible on first card + reallocated stick spacing (roots-work-slider)',
    $failures
);

$mediaSentenceCss = (string) file_get_contents($root . '/public/features/roots-media-sentence.css');
$enLangRaw = (string) file_get_contents($root . '/app/Lang/en.json');
$mediaSentenceOk = str_contains($headPhp, 'roots-media-sentence.css')
    && is_file($root . '/app/Support/media-sentence-layout.php')
    && str_contains($mediaSentenceCss, 'roots-ms-row')
    && str_contains($enLangRaw, 'PR {{img2}} agency')
    && !preg_match('/<p>\s*\{\{img/u', $enLangRaw);
$msRender = run_php(
    $php,
    'require "app/Support/app-init.php"; ob_start(); roots_render_media_sentence_html("home.media.mobile_html"); echo ob_get_clean();',
    $root
);
$mediaSentenceOk = $mediaSentenceOk
    && $msRender['exit'] === 0
    && !preg_match('/<p[^>]*>\s*<span class="window"/', $msRender['stdout']);
assert_true(
    $mediaSentenceOk,
    'company media sentence keeps images between words (layout normalizer + roots-media-sentence.css)',
    $failures
);

$siteChromeJs = (string) file_get_contents($root . '/public/features/roots-site-chrome.js');
$cookieRemovedOk = !str_contains($heroBody, 'cookie-box')
    && !str_contains($heroBody, 'cookieBox')
    && !str_contains($heroBody, 'common.cookie_text')
    && !str_contains((string) file_get_contents($root . '/app/Lang/en.json'), 'common.cookie_text')
    && str_contains($siteChromeJs, 'cookie-box');
assert_true(
    $cookieRemovedOk,
    'template cookie bar removed; roots-site-chrome.js stubs cookie-consent for vendored JS',
    $failures
);

$routerHyScale = run_php(
    $php,
    '$_SERVER["REQUEST_URI"]="/hy/"; require "public/router.php";',
    $root
);
assert_true(
    $routerHyScale['exit'] === 0
        && str_contains($routerHyScale['stdout'], 'lang="hy"')
        && str_contains($routerHyScale['stdout'], 'roots-locale-fonts.css')
        && str_contains($routerHyScale['stdout'], 'roots-locale-type-scale.css'),
    'router serves /hy/ with html lang=hy and locale font stylesheets',
    $failures
);

foreach (['hy', 'ru'] as $localeCode) {
    $segment = (string) (($locCfg['prefix'][$localeCode] ?? $localeCode));
    $langPath = $root . '/app/Lang/' . $localeCode . '.json';
    $langRaw = @file_get_contents($langPath);
    $langDict = is_string($langRaw) ? json_decode($langRaw, true) : null;
    $langDict = is_array($langDict) ? $langDict : null;
    $htmlLang = (string) ($locCfg['html_lang'][$localeCode] ?? $localeCode);
    $textDir = (string) ($locCfg['text_direction'][$localeCode] ?? 'ltr');

    $routerPrefixed = run_php(
        $php,
        '$_SERVER["REQUEST_URI"]=' . var_export('/' . $segment, true) . '; require "public/router.php";',
        $root
    );
    $prefixedHtml = $routerPrefixed['stdout'];
    $expectedUrl = public_page_url($siteCfg, 'home', $localeCode);
    $prefixedOk = $routerPrefixed['exit'] === 0
        && roots_smoke_home_document_shell_ok(
            $prefixedHtml,
            $langDict,
            $htmlLang,
            $textDir,
            $localeCode
        )
        && roots_smoke_og_bundle_ok($prefixedHtml, $siteCfg)
        && roots_smoke_json_ld_document_ok($prefixedHtml, $siteCfg)
        && roots_smoke_canonical_and_og_url_ok($prefixedHtml, $expectedUrl);
    assert_true(
        $prefixedOk,
        'router serves /' . $segment . ' with localized home shell and SEO head',
        $failures
    );
}

if ($failures !== []) {
    fwrite(STDERR, count($failures) . " failure(s)\n");
    exit(1);
}

echo "smoke ok\n";
