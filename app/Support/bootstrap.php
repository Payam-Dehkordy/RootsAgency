<?php
declare(strict_types=1);

if (!function_exists('h')) {
    function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        return $path;
    }
}

$site = require dirname(__DIR__) . '/Config/site-config.php';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
$site['canonical_url'] = rtrim((string) ($site['base_url'] ?? ($scheme . '://' . $host)), '/');
