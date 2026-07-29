<?php

/**
 * The content of a preview card in a flyout: cover image, title and teaser.
 *
 * The wrapper is rendered by snippets/page/menu-item.php – it is either the
 * link to the parent page or a span inside the link of a subpage. In the second
 * case the title only repeats the text of that link, so it is hidden from
 * assistive technology.
 *
 * @var \Kirby\Cms\Page $item
 * @var string $title Already escaped
 * @var bool $hidden
 */
?>
<?php if ($image = $item->menuImage()->toFile()) : ?>
	<span class="menubar__preview-image">
		<?php snippet('image', [
			'image'      => $image,
			'ratio'      => '16/9',
			'sizes'      => '15rem',
			'dimensions' => [240, 480],
			'alt'        => ''
		]) ?>
	</span>
<?php endif ?>
<span class="menubar__preview-title" <?php e($hidden, 'aria-hidden="true"') ?>><?= $title ?></span>
<?php if ($item->menuText()->isNotEmpty()) : ?>
	<span class="menubar__preview-text has-size-8"><?= $item->menuText()->escape() ?></span>
<?php endif ?>
