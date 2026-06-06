<?php
declare(strict_types=1);

function site_config_with_request_base(array $site): array
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host === '') {
        return $site;
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $isHttps ? 'https' : 'http';
    $site['base_url'] = $scheme . '://' . $host;

    return $site;
}
