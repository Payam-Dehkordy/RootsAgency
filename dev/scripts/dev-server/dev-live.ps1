param(
  [int]$PhpPort = 8013,
  [int]$DebounceMs = 700,
  [int]$PollMs = 250,
  [switch]$StartPhpServer
)

$ErrorActionPreference = "Stop"

$root = (Resolve-Path (Join-Path $PSScriptRoot "..\..\..")).Path
$phpProc = $null
$reloadMarker = Join-Path $root "public/__reload.txt"
$phpArgs = "-S 127.0.0.1:$PhpPort -t public public/router.php"
$hasListener = $null -ne (Get-NetTCPConnection -LocalPort $PhpPort -State Listen -ErrorAction SilentlyContinue)

if ($StartPhpServer) {
  if ($hasListener) {
    Write-Host "PHP server already listening on :$PhpPort (reusing existing process)."
  } else {
    $phpProc = Start-Process -FilePath "php" -ArgumentList $phpArgs -WorkingDirectory $root -PassThru -WindowStyle Minimized
    Write-Host "PHP server started by watcher."
  }
} elseif (-not $hasListener) {
  Write-Warning "No PHP server detected on :$PhpPort. Start it separately, or run with -StartPhpServer."
}

Write-Host "PHP server:  http://127.0.0.1:$PhpPort"
Write-Host "Live preview: http://127.0.0.1:$PhpPort"
Write-Host "Press Ctrl+C to stop."

function Get-LatestSourceTicks {
  param([string]$projectRoot)

  $sourceDirs = @(
    (Join-Path $projectRoot "app"),
    (Join-Path $projectRoot "public")
  )
  $maxTicks = 0L
  foreach ($dir in $sourceDirs) {
    if (-not (Test-Path $dir)) {
      continue
    }
    $files = Get-ChildItem -Path $dir -Recurse -File -Include *.php,*.css,*.js,*.html
    foreach ($file in $files) {
      if ($file.FullName -eq $reloadMarker) {
        continue
      }
      $ticks = $file.LastWriteTimeUtc.Ticks
      if ($ticks -gt $maxTicks) {
        $maxTicks = $ticks
      }
    }
  }
  return $maxTicks
}

function Write-ReloadMarker {
  param([string]$Path)
  $payload = ((Get-Date).ToUniversalTime().ToString("o")) + "`n"
  $attempts = 12
  for ($i = 0; $i -lt $attempts; $i++) {
    try {
      [System.IO.File]::WriteAllText($Path, $payload, [System.Text.Encoding]::ASCII)
      return $true
    }
    catch {
      Start-Sleep -Milliseconds (60 + ($i * 35))
    }
  }
  Write-Warning "Could not write reload marker (file locked). Browser may not auto-refresh until unlocked."
  return $false
}

try {
  [void](Write-ReloadMarker -Path $reloadMarker)
  $lastSeenTicks = Get-LatestSourceTicks -projectRoot $root
  $pendingSinceUtc = $null

  while ($true) {
    $currentTicks = Get-LatestSourceTicks -projectRoot $root
    if ($currentTicks -gt $lastSeenTicks) {
      $lastSeenTicks = $currentTicks
      $pendingSinceUtc = [DateTime]::UtcNow
    }

    if ($pendingSinceUtc -ne $null) {
      $elapsedMs = ([DateTime]::UtcNow - $pendingSinceUtc).TotalMilliseconds
      if ($elapsedMs -ge $DebounceMs) {
        [void](Write-ReloadMarker -Path $reloadMarker)
        $pendingSinceUtc = $null
      }
    }

    Start-Sleep -Milliseconds $PollMs
  }
}
finally {
  if ($phpProc -and -not $phpProc.HasExited) {
    Stop-Process -Id $phpProc.Id -Force
  }
}
