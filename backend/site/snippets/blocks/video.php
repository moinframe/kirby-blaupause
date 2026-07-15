<?php

use Kirby\Cms\Html;

/**
 *  @var \Kirby\Cms\Block $block
 *  @var \Femundfilou\AssetManager\AssetManager $assetManager
 *
 */

$assetManager->add('css', vite()->asset('frontend/styles/blocks/video.css'));
$assetManager->add('js', vite()->asset('frontend/blocks/video.ts'), ['type' => 'module']);


$crop    = $block->crop()->isTrue();
$ratio   = $block->ratio()->or('auto');
$cropFactor = 0;

if ($ratio != 'auto' && $crop) {
	// Split string to array and reverse it, e.g. "16/9" => [9,16]
	$ratioArray = array_reverse($ratio->split('/'));
	// Use first item of array as initial value
	$initialValue = array_shift($ratioArray);
	// Reduce array by division to get a crop factor, e.g. 0.5625
	$cropFactor = array_reduce($ratioArray, fn($r, $v) => $v == 0 ? $r : ($r / $v), $initialValue);
}

$thumbConfig = $cropFactor !== 0 ? ['width' => 1920, 'height' => 1920 * $cropFactor, 'crop' => true, 'quality' => 90] : ['width' => 1920, 'quality' => 90];
$poster = $block->poster()->toFile() ? $block->poster()->toFile()->thumb($thumbConfig)->url() : "";
?>

<figure style="--aspect-ratio: <?= $ratio ?>; <?= $crop ? '--object-fit: cover' : '--object-fit: contain'; ?>">
	<?php if ($block->external()->toBool()) : ?>
		<privacy-video
			poster="<?= $poster ?>"
			message="<?= t('privacy-overlay.message') ?>"
			button-text="<?= t('privacy-overlay.button') ?>">
			<?= Html::video($block->url(), [], ['data-src' => $block->url(), 'src' => '', 'title' => t('video.iframe.title')]); ?>
		</privacy-video>
	<?php else : ?>
		<video <?= e($block->loop()->toBool(), 'autoplay muted loop', 'controls') ?> playsinline <?= e($poster, 'poster="' . $poster . '"'); ?>>
			<?php foreach ($block->sources()->toFiles() as $video) : ?>
				<source src="<?= $video->url() ?>" type="<?= $video->mime() ?>">
			<?php endforeach; ?>
		</video>
	<?php endif; ?>
	<?php if ($block->caption()->isNotEmpty()) : ?>
		<figcaption><?= $block->caption() ?></figcaption>
	<?php endif ?>
</figure>