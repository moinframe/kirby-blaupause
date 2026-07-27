<?php

use Kirby\Toolkit\Str;

/** 
 * @var \Kirby\Cms\Block $block
 * @var \Femundfilou\AssetManager\AssetManager $assetManager 
 * */

$assetManager->add('css',  vite()->asset('frontend/styles/blocks/image.css'));

$caption 		= $block->caption();
$crop   	 	= $block->crop()->isTrue();
$link    		= $block->link();
$ratio   		= $block->ratio()->or('auto');
$maxWidth 	= $block->maxwidth()->toInt();
$alignment 	= $block->alignment()->or('left');

// The image snippet takes width/height, e.g. "16/9". Null keeps the image's
// own ratio, which is what an uncropped or "auto" image should use.
$cropRatio = $crop && $ratio != 'auto' ? $ratio->value() : null;

?>
<?php if ($image = $block->image()->toFile()) : ?>
	<figure class="has-text-<?= $alignment ?>" style="--aspect-ratio: <?= $ratio ?>; <?= $crop ? '--object-fit: cover;' : '--object-fit: contain;'; ?> --max-width: <?= $maxWidth ? $maxWidth . 'px' : 'none' ?>;">
		<div class="block-image__media">
			<?php if ($link->isNotEmpty()) : ?>
				<a href="<?= Str::esc($link->toUrl()) ?>">
					<?php snippet('image', ['image' => $image, 'ratio' => $cropRatio]) ?>
				</a>
			<?php else : ?>
				<?php snippet('image', ['image' => $image, 'ratio' => $cropRatio]) ?>
			<?php endif ?>

			<?php snippet('media-credits', [
				'copyright' => $image->copyright(),
				'ai'        => $block->ai()
			]) ?>
		</div>

		<?php if ($caption->isNotEmpty()) : ?>
			<figcaption>
				<?= $caption ?>
			</figcaption>
		<?php endif ?>
	</figure>
<?php endif ?>