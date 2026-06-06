<?php
declare(strict_types=1);

/** @return list<array{slug: string, name: string, role_key: string}> */
function roots_team_members(): array
{
    return [
        ['slug' => 'anahit-voskanyan', 'name' => 'Anahit Voskanyan', 'role_key' => 'team.role.ceo'],
        ['slug' => 'anahit-matevosyan', 'name' => 'Anahit Matevosyan', 'role_key' => 'team.role.team_lead'],
        ['slug' => 'anjela-khachatryan', 'name' => 'Anjela Khachatryan', 'role_key' => 'team.role.art_director'],
        ['slug' => 'anna-hayrunts', 'name' => 'Anna Hayrunts', 'role_key' => 'team.role.senior_graphic_designer'],
        ['slug' => 'armenuhi-harutyunyan', 'name' => 'Armenuhi Harutyunyan', 'role_key' => 'team.role.project_manager'],
        ['slug' => 'hasmik-khachatryan', 'name' => 'Hasmik Khachatryan', 'role_key' => 'team.role.reel_maker'],
        ['slug' => 'vika-khachatryan', 'name' => 'Vika Khachatryan', 'role_key' => 'team.role.project_manager'],
        ['slug' => 'milena-hovsepyan', 'name' => 'Milena Hovsepyan', 'role_key' => 'team.role.project_manager'],
        ['slug' => 'gohar-ghazaryan', 'name' => 'Gohar Ghazaryan', 'role_key' => 'team.role.graphic_designer'],
        ['slug' => 'mane-hambardzumyan', 'name' => 'Mane Hambardzumyan', 'role_key' => 'team.role.project_manager'],
        ['slug' => 'janna-taroyan', 'name' => 'Janna Taroyan', 'role_key' => 'team.role.project_manager'],
    ];
}

/** @return list<array{id: string, vid: string, poster: string, title_key: string, hashtag: string}> */
function roots_work_cards(): array
{
    return [
        ['id' => '01', 'vid' => '/media/video/work/roots-agency-work-01.mp4', 'poster' => '/media/images/work/roots-agency-work-01.webp', 'title_key' => 'work.card.01.title', 'hashtag' => '#creative'],
        ['id' => '02', 'vid' => '/media/video/work/roots-agency-work-02.mp4', 'poster' => '/media/images/work/roots-agency-work-02.webp', 'title_key' => 'work.card.02.title', 'hashtag' => '#horeca'],
        ['id' => '03', 'vid' => '/media/video/work/roots-agency-work-03.mp4', 'poster' => '/media/images/work/roots-agency-work-03.webp', 'title_key' => 'work.card.03.title', 'hashtag' => '#horeca'],
        ['id' => '04', 'vid' => '/media/video/work/roots-agency-work-04.mp4', 'poster' => '/media/images/work/roots-agency-work-04.webp', 'title_key' => 'work.card.04.title', 'hashtag' => '#creative'],
        ['id' => '05', 'vid' => '/media/video/work/roots-agency-work-05.mp4', 'poster' => '/media/images/work/roots-agency-work-05.webp', 'title_key' => 'work.card.05.title', 'hashtag' => '#foodandbev'],
        ['id' => '06', 'vid' => '/media/video/work/roots-agency-work-06.mp4', 'poster' => '/media/images/work/roots-agency-work-06.webp', 'title_key' => 'work.card.06.title', 'hashtag' => '#horeca'],
        ['id' => '07', 'vid' => '/media/video/work/roots-agency-work-07.mp4', 'poster' => '/media/images/work/roots-agency-work-07.webp', 'title_key' => 'work.card.07.title', 'hashtag' => '#horeca'],
        ['id' => '08', 'vid' => '/media/video/work/roots-agency-work-08.mp4', 'poster' => '/media/images/work/roots-agency-work-08.webp', 'title_key' => 'work.card.08.title', 'hashtag' => '#branding'],
        ['id' => '09', 'vid' => '/media/video/work/roots-agency-work-09.mp4', 'poster' => '/media/images/work/roots-agency-work-09.webp', 'title_key' => 'work.card.09.title', 'hashtag' => '#branding'],
        ['id' => '10', 'vid' => '/media/video/work/roots-agency-work-10.mp4', 'poster' => '/media/images/work/roots-agency-work-10.webp', 'title_key' => 'work.card.10.title', 'hashtag' => '#lifestyle'],
    ];
}

/** @return list<string> */
function roots_service_items(): array
{
    return [
        'services.item.strategy',
        'services.item.digital',
        'services.item.branding',
        'services.item.pr',
        'services.item.audit',
        'services.item.master_classes',
    ];
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

function roots_render_flip_link(string $href, string $labelKey, string $default, string $class, bool $external = false): void
{
    $label = tr($labelKey, $default);
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
    $role = tr((string) $member['role_key'], (string) ($member['role_default'] ?? ''));
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
    $html = tr_html($key, '');
    for ($i = 1; $i <= 5; $i++) {
        $html = str_replace('{{img' . $i . '}}', roots_media_sentence_picture($i), $html);
    }
    echo $html;
}

function roots_render_work_card(array $card): void
{
    echo '<div class="workCard sliderCard">';
    echo '<div class="workCard__media" data-vid="' . h((string) $card['vid']) . '" data-poster="' . h((string) $card['poster']) . '"></div>';
    echo '<div class="workCard__content">';
    $partnered = tr('work.partnered_with', 'We partnered with');
    if (current_locale() === 'en' && str_starts_with($partnered, 'We ')) {
        $styled = '<i>W</i>e' . substr($partnered, 2);
    } else {
        $styled = preg_replace('/^(\s*)(\S)/u', '$1<i>$2</i>', $partnered, 1) ?? $partnered;
    }
    echo '<p class="subheading">' . $styled . '</p>';
    $title = tr((string) $card['title_key'], (string) ($card['title_default'] ?? ''));
    echo '<h3 class="workCard__heading h0"><p>' . h($title) . '</p></h3>';
    echo '<p class="workCard__hashtag">' . h((string) $card['hashtag']) . '</p>';
    echo '</div></div>';
}
