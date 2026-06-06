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
        $fsPath = $projectRoot . '/public' . $path;
        if (is_dir($fsPath)) {
            return $path;
        }
        $version = @filemtime($fsPath);

        return $version ? ($path . '?v=' . $version) : $path;
    }
}
