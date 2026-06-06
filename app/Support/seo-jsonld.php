<?php
declare(strict_types=1);

/**
 * JSON-LD `@graph`: MarketingAgency + WebSite (`publisher` → agency, `inLanguage` from `locale_config()`).
 */
if (!function_exists('roots_json_ld_script_html')) {
    function roots_json_ld_script_html(array $site): string
    {
        $base = rtrim((string) ($site['base_url'] ?? ''), '/');
        $name = (string) ($site['brand'] ?? 'Roots Agency');
        $street = tr('office.address', (string) (($site['office'] ?? [])['address'] ?? ''));
        $email = (string) ($site['contact_to_email'] ?? '');
        $url = public_page_url($site, 'home', current_locale());
        $image = trim((string) ($site['og_default_image'] ?? ''));

        $sameAs = [];
        foreach ((array) ($site['social'] ?? []) as $link) {
            if (is_string($link) && $link !== '') {
                $sameAs[] = $link;
            }
        }

        $entity = [
            '@type' => ['Organization', 'MarketingAgency'],
            '@id' => $base . '#organization',
            'name' => $name,
            'url' => $url,
        ];

        if ($street !== '') {
            $entity['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $street,
                'addressLocality' => 'Yerevan',
                'addressCountry' => 'AM',
            ];
        }
        if ($email !== '') {
            $entity['email'] = $email;
        }
        if ($image !== '') {
            $entity['image'] = $image;
        }
        if ($sameAs !== []) {
            $entity['sameAs'] = $sameAs;
        }

        $website = [
            '@type' => 'WebSite',
            '@id' => $base . '#website',
            'url' => $url,
            'name' => $name,
            'publisher' => ['@id' => $base . '#organization'],
        ];

        $cfg = locale_config();
        $inLanguage = [];
        foreach (roots_public_locale_codes() as $code) {
            if (!is_string($code)) {
                continue;
            }
            $tag = $cfg['html_lang'][$code] ?? $code;
            if ($tag !== '') {
                $inLanguage[] = $tag;
            }
        }
        $inLanguage = array_values(array_unique($inLanguage));
        if ($inLanguage !== []) {
            $website['inLanguage'] = $inLanguage;
        }

        $graph = [
            '@context' => 'https://schema.org',
            '@graph' => [$entity, $website],
        ];

        $json = json_encode(
            $graph,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
        );

        return '<script type="application/ld+json">' . $json . "</script>\n";
    }
}
