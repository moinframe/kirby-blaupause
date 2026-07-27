<?php

/**
 * @var \Kirby\Cms\Block $block
 * @var \Femundfilou\AssetManager\AssetManager $assetManager
 */

$assetManager->add('css', vite()->asset('frontend/styles/blocks/ai-label.css'));

$alignment = $block->alignment()->or('left');
$text      = $block->text();
?>
<p class="ai-label-block has-text-<?= $alignment ?>">
	<?php snippet('ai-label', [
		'type'    => $block->ai(),
		'compact' => $block->compact()->toBool()
	]) ?>
	<?php if ($text->isNotEmpty()) : ?>
		<span class="ai-label-block__text"><?= $text ?></span>
	<?php endif ?>
</p>
