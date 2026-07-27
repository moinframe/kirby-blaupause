<?php

use Kirby\Cms\Html;
use Kirby\Http\Uri;
use Kirby\Toolkit\Str;

/**
 *  @var \Kirby\Cms\Block $block
 *  @var \Femundfilou\AssetManager\AssetManager $assetManager
 *
 */

$assetManager->add('css', vite()->asset('frontend/styles/blocks/video.css'));


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
$posterFile = $block->poster()->toFile();
$poster = $posterFile ? $posterFile->thumb($thumbConfig)->url() : "";
?>

<figure style="--aspect-ratio: <?= $ratio ?>; <?= $crop ? '--object-fit: cover' : '--object-fit: contain'; ?>">
	<div class="block-video__media">
		<?php if ($block->external()->toBool()) : ?>
			<?php
			$url = $block->url()->value();
			// drop `www.` so google.com and www.google.com share one consent key
			$host = Str::ltrim((new Uri($url))->host() ?? '', 'www.');

			// The service is matched from the embed host. Anything unknown still gets
			// a blocker, just labelled with its host and without a policy link.
			$service = match (true) {
				Str::contains($host, 'vimeo') => [
					'provider' => 'vimeo',
					'label'    => 'Vimeo',
					'policy'   => 'https://vimeo.com/privacy',
					// do-not-track embed
					'options'  => ['vimeo' => ['dnt' => 1]]
				],
				Str::contains($host, 'youtu') => [
					'provider' => 'youtube',
					'label'    => 'YouTube',
					'policy'   => 'https://policies.google.com/privacy',
					'options'  => ['youtube' => ['rel' => 0]]
				],
				default => [
					'provider' => $host,
					'label'    => $host,
					'policy'   => null,
					'options'  => []
				]
			};

			// Serve YouTube from the cookie-less domain. Html::video() derives the
			// embed host from the URL, so the host is swapped before it is called.
			$embedUrl = $url;
			if ($service['provider'] === 'youtube') {
				$uri = new Uri($url);
				$embedUrl = Str::contains($host, 'youtu.be')
					? 'https://www.youtube-nocookie.com/watch?v=' . $uri->path()->first()
					: $uri->setHost('www.youtube-nocookie.com')->toString();
			}

			$attr = [
				'title'          => t('video.iframe.title'),
				'loading'        => 'lazy',
				'referrerpolicy' => 'strict-origin-when-cross-origin'
			];

			// Html::video() only knows YouTube, Vimeo and video files
			$embed = Html::video($embedUrl, $service['options'], $attr)
				?? Html::iframe($embedUrl, Html::videoAttr($attr));
			?>
			<?php snippet('content-blocker', [
				'provider' => $service['provider'],
				'label'    => $service['label'],
				'policy'   => $service['policy'],
				'poster'   => $posterFile,
				'ratio'    => $ratio != 'auto' ? $ratio->value() : null,
				'fallback' => $url
			], slots: true) ?>
			<?= $embed ?>
			<?php endsnippet() ?>
		<?php else : ?>
			<video <?php e($block->loop()->toBool(), 'autoplay muted loop', 'controls') ?> playsinline <?php e($poster, 'poster="' . $poster . '"'); ?>>
				<?php foreach ($block->sources()->toFiles() as $video) : ?>
					<source src="<?= $video->url() ?>" type="<?= $video->mime() ?>">
				<?php endforeach; ?>
			</video>
		<?php endif; ?>

		<?php snippet('media-credits', ['ai' => $block->ai()]) ?>
	</div>

	<?php if ($block->caption()->isNotEmpty()) : ?>
		<figcaption><?= $block->caption() ?></figcaption>
	<?php endif ?>
</figure>
