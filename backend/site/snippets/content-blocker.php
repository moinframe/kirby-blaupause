<?php

/**
 * Wraps any third party embed and keeps it inert until the visitor consents.
 * The embed is passed in as the default slot and lives in a `<template>`,
 * so nothing is requested before consent is given.
 *
 * <?php snippet('content-blocker', ['provider' => 'youtube', 'label' => 'YouTube'], slots: true) ?>
 *     <iframe src="…"></iframe>
 * <?php endsnippet() ?>
 *
 * @var \Femundfilou\AssetManager\AssetManager $assetManager
 * @var string $provider Consent key, e.g. "youtube". Groups embeds and keys the storage
 * @var string|null $label Display name, e.g. "YouTube". Defaults to $provider
 * @var string|null $policy URL of the service's privacy policy
 * @var string|null $description Overlay text, HTML allowed. Defaults to the translated text
 * @var string|null $button Button label. Defaults to the translated label
 * @var \Kirby\Cms\File|null $poster Preview image shown behind the overlay
 * @var string|null $ratio Aspect ratio for the poster crop, e.g. "16/9". Null keeps the image ratio
 * @var string|null $fallback URL for the no-JS link
 * @var string|null $remember "session" (default) or "none"
 * @var \Kirby\Template\Slot|null $slot The embed markup
 */

use Kirby\Toolkit\Html;
use Kirby\Toolkit\Str;

$assetManager->add('css', vite()->asset('frontend/styles/snippets/content-blocker.css'));
$assetManager->add('js', vite()->asset('frontend/snippets/content-blocker.ts'), ['type' => 'module']);

$label       = $label ?? $provider;
$policy      = $policy ?? null;
$poster      = $poster ?? null;
$fallback    = $fallback ?? null;
$remember    = $remember ?? 'session';
$id          = 'cb-' . Str::random(8, 'alphaLower');
$description = $description ?? null;

// The policy link is built here so the translations stay free of URLs and attributes
if ($description === null) {
	$description = $policy
		? tt('content-blocker.description', null, [
			'provider' => Html::encode($label),
			'policy'   => Html::a($policy, tt('content-blocker.policy', null, ['provider' => $label]), [
				'target' => '_blank'
			])
		])
		: tt('content-blocker.description.nopolicy', null, ['provider' => Html::encode($label)]);
}
?>
<content-blocker provider="<?= Html::encode($provider) ?>" remember="<?= $remember ?>" data-state="blocked">
	<div class="content-blocker__overlay">
		<?php if ($poster) : ?>
			<div class="content-blocker__poster">
				<?php snippet('image', ['image' => $poster, 'ratio' => $ratio ?? null, 'alt' => '']) ?>
			</div>
		<?php endif ?>
		<div class="content-blocker__panel">
			<div class="content-blocker__description" id="<?= $id ?>"><?= $description ?></div>
			<button type="button" class="button" aria-describedby="<?= $id ?>" data-accept>
				<?= $button ?? t('content-blocker.button') ?>
			</button>
		</div>
	</div>

	<template data-embed><?= $slot ?></template>

	<noscript>
		<div class="content-blocker__panel">
			<p><?= tt('content-blocker.noscript', null, ['provider' => Html::encode($label)]) ?></p>
			<?php if ($fallback) : ?>
				<a class="button" href="<?= Str::esc($fallback) ?>" target="_blank" rel="noopener noreferrer">
					<?= tt('content-blocker.fallback', null, ['provider' => Html::encode($label)]) ?>
				</a>
			<?php endif ?>
		</div>
	</noscript>
</content-blocker>
