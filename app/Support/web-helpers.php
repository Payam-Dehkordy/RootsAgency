<?php
declare(strict_types=1);

final class WebHelpers
{
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function asset(string $path, string $projectRoot): string
    {
        if ($path === '' || $path[0] !== '/') {
            return $path;
        }

        $barePath = str_contains($path, '?') ? strstr($path, '?', true) : $path;
        if ($barePath === false || $barePath === '') {
            $barePath = $path;
        }

        $fsPath = $projectRoot . '/public' . $barePath;
        if (is_dir($fsPath)) {
            return $path;
        }

        $version = @filemtime($fsPath);
        if (!$version) {
            return $path;
        }

        $sep = str_contains($path, '?') ? '&' : '?';

        return $path . $sep . 'v=' . $version;
    }

    public static function isLocalPreview(): bool
    {
        $host = (string) ($_SERVER['HTTP_HOST'] ?? '');

        return str_contains($host, '127.0.0.1') || str_contains($host, 'localhost');
    }
}
