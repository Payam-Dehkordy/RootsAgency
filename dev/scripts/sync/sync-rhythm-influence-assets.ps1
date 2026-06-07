param(
    [switch]$DownloadMedia
)

$ErrorActionPreference = 'Stop'
$root = Split-Path (Split-Path (Split-Path $PSScriptRoot -Parent) -Parent) -Parent
$src = Join-Path $root 'dev\template-source'
$public = Join-Path $root 'public'
$staging = Join-Path $root 'dev\template-assets'

New-Item -ItemType Directory -Force -Path $src, "$public\dist", "$public\ui", "$public\fonts", $staging | Out-Null

$assetVersion = '7c85f98c63f5e1f89737e800920875f74ad6abf9'
$baseUrl = 'https://www.rhythminfluence.com'

Write-Host "Fetching homepage HTML..."
curl.exe -sL "$baseUrl/" -o (Join-Path $src 'rhythm-influence-home.raw.html')

Write-Host "Fetching dist bundles..."
curl.exe -sL "$baseUrl/dist/style.min.css?v=$assetVersion" -o (Join-Path $src 'rhythm-influence.style.min.css')
curl.exe -sL "$baseUrl/dist/scripts.min.js?v=$assetVersion" -o (Join-Path $src 'rhythm-influence.scripts.min.js')

Copy-Item (Join-Path $src 'rhythm-influence.style.min.css') (Join-Path $public 'dist\style.min.css') -Force
Copy-Item (Join-Path $src 'rhythm-influence.scripts.min.js') (Join-Path $public 'dist\scripts.min.js') -Force

Write-Host "Fetching UI SVGs..."
foreach ($ui in @('button_arrow.svg', 'footer_bg_light.svg')) {
    curl.exe -sL "$baseUrl/ui/$ui" -o (Join-Path $public "ui\$ui")
}

Write-Host "Fetching template fonts (Workhorse + Aeonik)..."
foreach ($font in @(
    'WorkhorseScriptTest-Display.woff2',
    'aeonik-regular.woff2',
    'aeonik-bold.woff2',
    'aeonik-medium.woff2',
    'aeonik-light.woff2'
)) {
    curl.exe -sL "$baseUrl/fonts/$font" -o (Join-Path $public "fonts\$font")
}

$raw = Get-Content (Join-Path $src 'rhythm-influence-home.raw.html') -Raw
if ($raw -match '(?s)<body>(.*)</body>') {
    $body = $matches[1]
    $body = $body -replace '(?s)<!-- Start of HubSpot Embed Code -->.*?<!-- End of HubSpot Embed Code -->', ''
    $body = $body -replace 'https://rhythm-influence\.files\.svdcdn\.com/staging/Partner-Logo-W\.svg\?[^"]+', '/media/images/brand/roots-agency-logo.svg'
    $body = $body -replace 'alt="Nav logo"', 'alt="Roots Agency logo"'
    $bodyPath = Join-Path $src 'rhythm-influence-body.fragment.html'
    [System.IO.File]::WriteAllText($bodyPath, $body)
    Write-Host "Wrote template body fragment ($($body.Length) chars) — merge into app/Views/pages/home/home-body.php for runtime"
}

if ($DownloadMedia) {
    Write-Host "Downloading media URLs (this may take a while)..."
    $urls = [regex]::Matches($raw, 'https://(?:rhythm-influence\.(?:files|transforms)\.svdcdn\.com|servd-rhythm-influence\.b-cdn\.net)/[^"\s<>]+') |
        ForEach-Object { $_.Value } |
        Sort-Object -Unique

    $i = 0
    foreach ($url in $urls) {
        $i++
        $safe = [regex]::Replace($url, '[^\w\-.]+', '_')
        if ($safe.Length -gt 120) { $safe = $safe.Substring(0, 120) }
        $out = Join-Path $staging $safe
        if (-not (Test-Path $out)) {
            curl.exe -sL $url -o $out
        }
        if ($i % 10 -eq 0) { Write-Host "  $i / $($urls.Count)" }
    }
    Write-Host "Staged $($urls.Count) unique media URLs under dev/template-assets/"
}

Write-Host "Done. Run: php test\smoke.php && python serve.py"
