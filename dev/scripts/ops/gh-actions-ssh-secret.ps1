# Emit ONE line of base64 (private key file bytes) for GitHub secret SSH_PRIVATE_KEY_B64.
param(
    [string] $KeyPath = (Join-Path $env:USERPROFILE '.ssh\do_roots-agency')
)
if (-not (Test-Path -LiteralPath $KeyPath)) {
    Write-Error "Key not found: $KeyPath"
    exit 1
}
$b = [IO.File]::ReadAllBytes($KeyPath)
[Convert]::ToBase64String($b)
