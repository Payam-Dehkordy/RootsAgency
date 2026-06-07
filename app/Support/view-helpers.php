<?php
declare(strict_types=1);

/** @return list<array{slug: string, name: string, role_key: string}> */
function roots_team_members(): array
{
    static $members;

    return $members ??= require __DIR__ . '/../Data/team-members.php';
}

/** @return list<array{id: string, vid: string, poster: string, title_key: string, hashtag: string}> */
function roots_work_cards(): array
{
    static $cards;

    return $cards ??= require __DIR__ . '/../Data/work-cards.php';
}

/** @return list<string> */
function roots_service_items(): array
{
    static $items;

    return $items ??= require __DIR__ . '/../Data/service-items.php';
}

function roots_media_sentence_picture(int $index): string
{
    $base = '/media/images/home/roots-agency-media-sentence-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT);
    $webp = $base . '.webp';
    $webp2x = $base . '@2x.webp';

    return '<span class="window"><picture>'
        . '<source type="image/webp" srcset="' . h($webp) . ' 1x, ' . h($webp2x) . ' 2x" />'
        . '<img src="' . h($webp) . '" class="media img lazy" alt="" width="124" height="88" loading="lazy" />'
        . '</picture></span>';
}

function roots_render_flip_link(string $href, string $labelKey, string $class, bool $external = false): void
{
    $label = tr($labelKey);
    echo '<a href="' . h($href) . '" class="' . h($class) . '" data-content="' . h($label) . '"';
    if ($external) {
        echo ' target="_blank" rel="noopener noreferrer"';
    }
    echo '><span>' . h($label) . '</span></a>';
}

function roots_render_team_card(array $member, bool $banner = false): void
{
    $src = '/media/images/team/roots-agency-team-' . $member['slug'] . '.webp';
    $name = (string) $member['name'];
    $role = tr((string) $member['role_key']);
    $wrapClass = $banner ? 'logoBanner__logo roots-team-banner' : 'clientLogos__logo logo roots-team-card';
    echo '<div class="' . h($wrapClass) . '">';
    echo '<img src="' . h($src) . '" class="media img roots-team-card__photo" alt="' . h($name) . '" />';
    echo '<div class="roots-team-card__bar">';
    echo '<p class="roots-team-card__name">' . h($name) . '</p>';
    echo '<p class="roots-team-card__role">' . h($role) . '</p>';
    echo '</div>';
    echo $banner ? '<span></span>' : '<span></span>';
    echo '</div>';
}

function roots_render_media_sentence_html(string $key): void
{
    require_once __DIR__ . '/media-sentence-layout.php';

    $html = tr_html($key);
    for ($i = 1; $i <= 5; $i++) {
        $html = str_replace('{{img' . $i . '}}', roots_media_sentence_picture($i), $html);
    }
    echo roots_normalize_media_sentence_html($html);
}

function roots_render_work_card(array $card): void
{
    echo '<div class="workCard sliderCard">';
    echo '<div class="workCard__media" data-vid="' . h((string) $card['vid']) . '" data-poster="' . h((string) $card['poster']) . '"></div>';
    echo '<div class="workCard__content">';
    $partnered = tr('work.partnered_with');
    if (current_locale() === 'en' && str_starts_with($partnered, 'We ')) {
        $styled = '<i>W</i>e' . substr($partnered, 2);
    } else {
        $styled = preg_replace('/^(\s*)(\S)/u', '$1<i>$2</i>', $partnered, 1) ?? $partnered;
    }
    echo '<p class="subheading">' . $styled . '</p>';
    $title = tr((string) $card['title_key']);
    echo '<h3 class="workCard__heading h0"><p>' . h($title) . '</p></h3>';
    echo '<p class="workCard__hashtag">' . h((string) $card['hashtag']) . '</p>';
    echo '</div></div>';
}
