<?php

/**
 * @var \Kirby\Cms\App $kirby
 * @var \Kirby\Cms\Site $site
 */

use Kirby\Toolkit\Html;

$blocks = $site->footerBlocks()->toBlocks();
$menus  = array_filter(
	[$site->footermenu()->toPages(), $site->footermenu2()->toPages()],
	fn($menu) => $menu->isNotEmpty()
);
$meta   = $site->metamenu()->toPages();
$social = $site->social()->toEntries();
?>
<footer class="subgrid span-full" id="page-footer">
	<div class="subgrid span-content has-pt-2xl-3xl has-pb-m">
		<?php if ($blocks->isNotEmpty()) : ?>
			<div class="span-full span-7:m flow has-gap-m">
				<?= $blocks ?>
			</div>
		<?php endif ?>

		<?php if ($menus !== []) : ?>
			<nav class="span-full span-5:m footer__menus" aria-label="<?= t('aria.nav.footer') ?>">
				<?php foreach ($menus as $menu) : ?>
					<ul class="footer__menu">
						<?php foreach ($menu as $item) : ?>
							<li>
								<a class="footer__link" href="<?= $item->url() ?>" <?php e($item->isActive(), 'aria-current="page"') ?>>
									<?= Html::encode($item->menuTitle()->or($item->title())->value()) ?>
								</a>
							</li>
						<?php endforeach ?>
					</ul>
				<?php endforeach ?>
			</nav>
		<?php endif ?>

		<div class="span-full footer__bar">

			<p class="footer__copyright has-size-7">
				© <?= date('Y') ?> <?= $site->title() ?>
			</p>

			<?php if ($social->isNotEmpty()) : ?>
				<ul class="footer__group" aria-label="<?= t('aria.social') ?>">
					<?php foreach ($social as $item) : ?>
						<?php
						$input = $item->value();
						$platform = null;
						if (strpos($input, 'instagram') !== false) {
							$platform = "instagram";
						} elseif (strpos($input, 'linkedin') !== false) {
							$platform = "linkedin";
						} elseif (strpos($input, 'signal') !== false) {
							$platform = "signal";
						} elseif (strpos($input, 'youtube') !== false) {
							$platform = "youtube";
						} elseif (strpos($input, 'facebook') !== false) {
							$platform = "facebook";
						}
						?>
						<?php if ($platform) : ?>
							<li>
								<a class="footer__link footer__link--icon" href="<?= $item ?>" target="_blank" rel="me noopener">
									<?= icon($platform, '1em') ?>
									<span class="visually-hidden"><?= $platform ?></span>
								</a>
							</li>
						<?php endif ?>
					<?php endforeach ?>
				</ul>
			<?php endif ?>



			<?php if ($meta->isNotEmpty()) : ?>
				<nav aria-label="<?= t('aria.nav.meta') ?>">
					<ul class="footer__group">
						<?php foreach ($meta as $item) : ?>
							<li>
								<a class="footer__link has-size-7" href="<?= $item->url() ?>" <?php e($item->isActive(), 'aria-current="page"') ?>>
									<?= Html::encode($item->menuTitle()->or($item->title())->value()) ?>
								</a>
							</li>
						<?php endforeach ?>
					</ul>
				</nav>
			<?php endif ?>
		</div>
	</div>
</footer>
