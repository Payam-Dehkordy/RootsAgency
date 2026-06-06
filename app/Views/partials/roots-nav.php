<?php
declare(strict_types=1);

/** @var array $site */
$pageKey = (string) ($GLOBALS['page_key'] ?? 'home');
$langSwitch = language_switcher_entries($site, $pageKey);
$langNavAria = h(tr('common.language_nav'));
$home = h(localized_path('/'));
$brandAlt = h(tr('common.brand_alt'));
$logoAria = h(tr('common.logo_home_aria'));

$renderLangSwitchLink = static function (array $entry): void {
    $label = h((string) $entry['label']);
    echo '<a href="' . h((string) $entry['href']) . '" class="roots-lang-link" hreflang="' . h((string) $entry['hreflang']) . '" lang="' . h((string) $entry['lang']) . '" title="' . $label . '" data-no-swup>' . $label . '</a>';
};

$navLinks = [
    ['href' => '#company', 'key' => 'nav.company'],
    ['href' => '#slider-cards', 'key' => 'nav.our_work'],
    ['href' => '#services', 'key' => 'nav.services'],
    ['href' => '#team', 'key' => 'nav.our_team'],
    ['href' => '#contact', 'key' => 'nav.contact'],
];

$socialLinks = [
    ['href' => (string) ($site['social']['instagram'] ?? ''), 'key' => 'nav.instagram'],
    ['href' => (string) ($site['social']['facebook'] ?? ''), 'key' => 'nav.facebook'],
    ['href' => (string) ($site['social']['linkedin'] ?? ''), 'key' => 'nav.linkedin'],
];
?>
<nav class="nav" id="nav">

    <div class="nav__bar">
        <a href="<?= $home ?>" class="nav__logo" aria-label="<?= $logoAria ?>">
          <img
          src="<?= h(asset('/media/images/brand/roots-agency-logo.svg')) ?>"
          class="media img "
          alt="<?= $brandAlt ?>"
          />
        </a>

        <div class="nav__links" aria-label="<?= h(tr('common.primary_nav')) ?>">
<?php foreach ($navLinks as $item): ?>
          <?php roots_render_flip_link((string) $item['href'], (string) $item['key'], 'link nav__link flipLink'); ?>

<?php endforeach; ?>
        </div>

        <div class="nav__cta">
<?php if ($langSwitch !== []): ?>
            <nav class="roots-nav-lang" aria-label="<?= $langNavAria ?>">
<?php foreach ($langSwitch as $entry): ?>
                <?php $renderLangSwitchLink($entry); ?>
<?php endforeach; ?>
            </nav>
<?php endif; ?>
        </div>

        <div class="nav__toggle" id="nav-toggle">
            <span><span><?= h(tr('common.menu')) ?></span></span>
        </div>
    </div>


    <div class="nav__menu">
        <div class="nav__menuBg"></div>

        <div class="nav__menuInner">

            <div class="nav__menuLinks">
<?php foreach ($navLinks as $item): ?>
          <?php roots_render_flip_link((string) $item['href'], (string) $item['key'], 'link nav__menuLink'); ?>

<?php endforeach; ?>
            </div>

            <div class="nav__menuSocial">
<?php foreach ($socialLinks as $item): ?>
          <?php roots_render_flip_link((string) $item['href'], (string) $item['key'], 'link nav__socialLink', true); ?>

<?php endforeach; ?>
            </div>

<?php if ($langSwitch !== []): ?>
            <nav class="roots-nav-lang roots-nav-lang--menu" aria-label="<?= $langNavAria ?>">
<?php foreach ($langSwitch as $entry): ?>
                <?php $renderLangSwitchLink($entry); ?>
<?php endforeach; ?>
            </nav>
<?php endif; ?>
        </div>
    </div>

</nav>
