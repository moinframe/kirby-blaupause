<?php

/**
 * Badges laid over a media element. The AI label sits in the top corner, the
 * copyright in the bottom one, so the two never overlap – and neither of them
 * covers the consent panel of the `content-blocker` snippet, which is centred
 * at the bottom of the embed.
 *
 * Renders nothing when there is neither a copyright nor an AI label.
 *
 * @var \Femundfilou\AssetManager\AssetManager $assetManager
 * @var \Kirby\Content\Field|string|null $copyright
 * @var \Kirby\Content\Field|string|null $ai "generated" or "modified"
 */

use Kirby\Toolkit\Html;

$copyright = $copyright ?? null;
$ai        = $ai ?? null;

$copyright = $copyright instanceof Kirby\Content\Field ? $copyright->value() : $copyright;
$ai        = $ai instanceof Kirby\Content\Field ? $ai->value() : $ai;

$hasAi = in_array($ai, ['generated', 'modified'], true);

if (empty($copyright) === true && $hasAi === false) {
	return;
}

$assetManager->add('css', vite()->asset('frontend/styles/snippets/media-credits.css'));
?>
<div class="media-credits">
	<?php if ($hasAi === true) : ?>
		<?php snippet('ai-label', ['type' => $ai, 'class' => 'media-credits__item']) ?>
	<?php endif ?>
	<?php if (empty($copyright) === false) : ?>
		<p class="media-credits__item media-credits__copyright">&copy;&nbsp;<?= Html::encode($copyright) ?></p>
	<?php endif ?>
</div>
