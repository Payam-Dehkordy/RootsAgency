<?php
declare(strict_types=1);

/** @var array $site */
$pageKey = (string) ($GLOBALS['page_key'] ?? 'home');
$langSwitch = language_switcher_entries($site, $pageKey);
$langNavAria = h(tr('common.language_nav', 'Languages'));
$home = h(localized_path('/'));
$brandAlt = h(tr('common.brand_alt', 'Roots Agency logo'));
$logoAria = h(tr('common.logo_home_aria', 'Roots Agency home'));

$renderLangSwitchLink = static function (array $entry): void {
    $label = h((string) $entry['label']);
    echo '<a href="' . h((string) $entry['href']) . '" class="roots-lang-link" hreflang="' . h((string) $entry['hreflang']) . '" lang="' . h((string) $entry['lang']) . '" title="' . $label . '" data-no-swup>' . $label . '</a>';
};

$navLinks = [
    ['href' => '#company', 'key' => 'nav.company', 'default' => 'Company'],
    ['href' => '#slider-cards', 'key' => 'nav.our_work', 'default' => 'Our Work'],
    ['href' => '#services', 'key' => 'nav.services', 'default' => 'Services'],
    ['href' => '#team', 'key' => 'nav.our_team', 'default' => 'Our Team'],
    ['href' => '#contact', 'key' => 'nav.contact', 'default' => 'Contact'],
];

$socialLinks = [
    ['href' => (string) ($site['social']['instagram'] ?? ''), 'key' => 'nav.instagram', 'default' => 'Instagram'],
    ['href' => (string) ($site['social']['facebook'] ?? ''), 'key' => 'nav.facebook', 'default' => 'Facebook'],
    ['href' => (string) ($site['social']['linkedin'] ?? ''), 'key' => 'nav.linkedin', 'default' => 'LinkedIn'],
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

        <div class="nav__links" aria-label="<?= h(tr('common.primary_nav', 'Primary')) ?>">
<?php foreach ($navLinks as $item): ?>
          <?php roots_render_flip_link((string) $item['href'], (string) $item['key'], (string) $item['default'], 'link nav__link flipLink'); ?>

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
            <span><span><?= h(tr('common.menu', 'Menu')) ?></span></span>
        </div>
    </div>


    <div class="nav__menu">
        <div class="nav__menuBg"></div>

        <div class="nav__menuInner">

            <div class="nav__menuLinks">
<?php foreach ($navLinks as $item): ?>
          <?php roots_render_flip_link((string) $item['href'], (string) $item['key'], (string) $item['default'], 'link nav__menuLink'); ?>

<?php endforeach; ?>
            </div>

            <div class="nav__menuSocial">
<?php foreach ($socialLinks as $item): ?>
          <?php roots_render_flip_link((string) $item['href'], (string) $item['key'], (string) $item['default'], 'link nav__socialLink', true); ?>

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
