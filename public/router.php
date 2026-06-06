<?php
declare(strict_types=1);

$public = __DIR__;
$raw = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = $raw === null || $raw === false || $raw === '' ? '/' : rawurldecode($raw);
if ($path === '' || $path[0] !== '/') {
    $path = '/' . ltrim($path, '/');
}
if ($path !== '/' && str_ends_with($path, '/')) {
    $path = rtrim($path, '/') ?: '/';
}

$sep = DIRECTORY_SEPARATOR;
$filesystemPath = $public . str_replace('/', $sep, $path);

if ($path !== '/' && is_file($filesystemPath)) {
    return false;
}

if ($path === '/') {
    require $public . $sep . 'index.php';
    return;
}

http_response_code(404);
echo '404 Not Found';
