# RootsAgency local preview — no browser cache, auto hard refresh on save.
#
# Usage:
#   powershell -ExecutionPolicy Bypass -File dev/serve-live.ps1
#   powershell -ExecutionPolicy Bypass -File dev/serve-live.ps1 -Port 8013 -NoBrowser

param(
    [int] $Port = 8013,
    [switch] $NoBrowser
)

$ErrorActionPreference = "Stop"
$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
Set-Location $repoRoot

$reloadMarker = Join-Path $repoRoot "public\__reload.txt"
[void](New-Item -ItemType Directory -Force -Path (Split-Path $reloadMarker))
[System.IO.File]::WriteAllText($reloadMarker, ((Get-Date).ToUniversalTime().ToString("o")) + "`n")

$phpArgs = "-S 127.0.0.1:$Port -t public public/router.php"
$hasListener = $null -ne (Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue)

if ($hasListener) {
    Write-Host "Reusing PHP on :$Port"
} else {
    Start-Process -FilePath "php" -ArgumentList $phpArgs -WorkingDirectory $repoRoot -WindowStyle Minimized | Out-Null
    Start-Sleep -Milliseconds 600
    Write-Host "Started PHP on :$Port (router.php)"
}

$watcher = Join-Path $repoRoot "dev\scripts\dev-server\dev-live.ps1"
if (-not (Test-Path $watcher)) {
    Write-Warning "Missing watcher script; auto-reload disabled."
} else {
    Start-Process -FilePath "powershell" -ArgumentList @(
        "-ExecutionPolicy", "Bypass",
        "-File", $watcher,
        "-PhpPort", $Port
    ) -WorkingDirectory $repoRoot -WindowStyle Minimized | Out-Null
    Write-Host "Live reload watcher started (public/__reload.txt)"
}

$cacheBust = [DateTimeOffset]::UtcNow.ToUnixTimeMilliseconds()
$url = "http://127.0.0.1:$Port/?_dev=$cacheBust"

Write-Host ""
Write-Host "Preview: $url"
Write-Host "Hard refresh: Ctrl+Shift+R"
Write-Host "Disable auto-reload once: add ?noreload to the URL"
Write-Host ""

if (-not $NoBrowser) {
    $chrome = "${env:ProgramFiles}\Google\Chrome\Application\chrome.exe"
    if (-not (Test-Path $chrome)) {
        $chrome = "${env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe"
    }
    if (Test-Path $chrome) {
        Start-Process $chrome $url
    } else {
        Start-Process $url
    }
}

Write-Host "Press Ctrl+C to exit (PHP + watcher keep running in background)."
try {
    while ($true) { Start-Sleep -Seconds 3600 }
} finally {
    Write-Host "Done."
}
