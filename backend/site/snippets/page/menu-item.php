<?php

/**
 * One item of the main navigation.
 *
 * A page without listed subpages is a plain link. A page with subpages becomes
 * a disclosure button that opens the flyout (ARIA APG pattern "disclosure
 * navigation menu") – the page itself stays reachable through the preview at
 * the top of the flyout, which is a link to it.
 *
 * The flyout shows the subpages in a list. Each of them carries a preview that
 * is placed in the left column of the flyout and revealed on hover or focus,
 * the preview of the parent page is shown as long as nothing is hovered. Below
 * `m` the flyout is a plain list inside the drawer: the parent link on top, the
 * subpages below. What a preview contains is rendered by
 * snippets/page/menu-preview.php.
 *
 * @var \Kirby\Cms\Page $item
 */

use Kirby\Toolkit\Html;

$title    = Html::encode($item->menuTitle()->or($item->title())->value());
$children = $item->children()->listed();

// The style of a page applies wherever it shows up, in the bar and in a flyout.
// The classes are written out so purgecss keeps them.
$style = match ($item->menuStyle()->value()) {
	'button' => 'menubar__cta button',
	'accent' => 'menubar__link menubar__link--accent',
	default  => 'menubar__link'
};
?>
<li class="menubar__item">
	<?php if ($children->isEmpty()) : ?>
		<a class="<?= $style ?>" href="<?= $item->url() ?>" <?php e($item->isActive(), 'aria-current="page"') ?>>
			<span class="menubar__label" data-text="<?= $title ?>"><?= $title ?></span>
		</a>
	<?php else : ?>
		<button type="button" class="<?= $style ?> menubar__disclosure" aria-expanded="false" aria-controls="submenu-<?= $item->uid() ?>" <?php e($item->isOpen(), 'aria-current="true"') ?>>
			<span class="menubar__label" data-text="<?= $title ?>"><?= $title ?></span>
			<?= icon('chevron-down', '1em') ?>
		</button>
		<div class="menubar__flyout" id="submenu-<?= $item->uid() ?>">
			<a class="menubar__preview menubar__preview--parent" href="<?= $item->url() ?>" <?php e($item->isActive(), 'aria-current="page"') ?>>
				<!-- In the drawer the title is already on the button above, so the link to the page is labelled instead. The label is hidden from `m` upwards, title and all. -->
				<span class="menubar__preview-overview">
					<?= t('menu.overview') ?><span class="visually-hidden"> <?= $title ?></span>
				</span>
				<?php snippet('page/menu-preview', ['item' => $item, 'title' => $title, 'hidden' => false]) ?>
			</a>
			<ul class="menubar__sublist flow has-gap-0 has-items-stretch">
				<?php foreach ($children as $child) : ?>
					<?php
					$childTitle = Html::encode($child->menuTitle()->or($child->title())->value());
					$childStyle = match ($child->menuStyle()->value()) {
						'button' => 'menubar__sub menubar__sub--button',
						'accent' => 'menubar__sub menubar__sub--accent',
						default  => 'menubar__sub'
					};
					?>
					<li>
						<a class="<?= $childStyle ?>" href="<?= $child->url() ?>" <?php e($child->isActive(), 'aria-current="page"') ?>>
							<span class="menubar__sub-title"><?= $childTitle ?></span>
							<span class="menubar__preview">
								<?php snippet('page/menu-preview', ['item' => $child, 'title' => $childTitle, 'hidden' => true]) ?>
							</span>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	<?php endif ?>
</li>
