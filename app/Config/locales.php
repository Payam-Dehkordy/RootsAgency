<?php
declare(strict_types=1);

/**
 * Public site locales. Default English has no URL prefix; hy / ru use /{segment}/...
 *
 * @return array{
 *   default: string,
 *   prefix: array<string, string>,
 *   html_lang: array<string, string>,
 *   text_direction: array<string, string>
 * }
 */
return [
    'default' => 'en',
    'prefix' => [
        'hy' => 'hy',
        'ru' => 'ru',
    ],
    'html_lang' => [
        'en' => 'en',
        'hy' => 'hy',
        'ru' => 'ru',
    ],
    'text_direction' => [
        'en' => 'ltr',
        'hy' => 'ltr',
        'ru' => 'ltr',
    ],
];
