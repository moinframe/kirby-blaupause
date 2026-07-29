<?php

/**
 * Site header with the main navigation.
 *
 * The bar holds the logo on the left and the navigation next to it. Top level
 * items are the pages selected in the site's `mainmenu` field, their listed
 * children are rendered as a flyout. How a single page looks in the menu
 * (label, teaser, style) is defined on the page itself in the menu tab, see
 * blueprints/tabs/menu.yml and snippets/page/menu-item.php.
 *
 * @var \Femundfilou\AssetManager\AssetManager $assetManager
 * @var \Kirby\Cms\App $kirby
 * @var \Kirby\Cms\Site $site
 */

use Kirby\Toolkit\Html;

$assetManager->add('js', vite()->asset('frontend/snippets/header.ts'), ['type' => 'module']);
$assetManager->add('js', vite()->asset('frontend/snippets/menu.ts'), ['type' => 'module']);

$menu = $site->mainmenu()->toPages();
$home = $site->homePage();
$logo = $site->logo()->toFile();
?>
<a href="#page" class="skip-link"><?= t('button.skip-menu') ?></a>

<scroll-header class="grid-reset" id="page-header">
	<div class="span-full flow-row has-justify-center has-px-m-l">
		<main-menu class="menubar">
			<a class="menubar__logo" href="<?= $home?->url() ?? $site->url() ?>" <?php e($home?->isOpen(), 'aria-current="page"') ?>>
				<?php if ($logo) : ?>
					<img src="<?= $logo->url() ?>" alt="<?= Html::encode($site->title()->value()) ?>" <?php e($logo->width(), 'width="' . $logo->width() . '" height="' . $logo->height() . '"') ?>>
				<?php else : ?>
					<?= $site->title() ?>
				<?php endif ?>
			</a>
			<?php if ($menu->isNotEmpty()) : ?>
				<button type="button" class="button menubar__toggle is-hidden:m" aria-expanded="false" aria-controls="mainnav">
					<?= t('button.menu') ?>
					<?= icon('menu', '1.25em') ?><?= icon('close', '1.25em') ?>
				</button>
				<nav class="menubar__nav" id="mainnav" aria-label="<?= t('aria.nav.main') ?>">
					<ul class="menubar__list flow has-gap-2xs has-items-stretch has-flex-row:m has-items-center:m">
						<?php foreach ($menu as $item) : ?>
							<?php snippet('page/menu-item', ['item' => $item]) ?>
						<?php endforeach ?>
					</ul>
				</nav>
				<div class="menubar__backdrop" aria-hidden="true"></div>
			<?php endif ?>
		</main-menu>
	</div>
</scroll-header>
