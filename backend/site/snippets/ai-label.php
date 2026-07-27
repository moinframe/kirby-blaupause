<?php

/**
 * Renders the official EU label for AI generated or AI modified content.
 * https://digital-strategy.ec.europa.eu/en/policies/eu-icons-labelling-ai-generated-content
 *
 * <?php snippet('ai-label', ['type' => 'generated']) ?>
 *
 * Nothing is rendered for an unknown or empty type, so the field value of the
 * `fields/ai` select can be passed straight through.
 *
 * @var \Femundfilou\AssetManager\AssetManager $assetManager
 * @var \Kirby\Content\Field|string|null $type "generated" or "modified"
 * @var bool|null $compact Use the round "AI" mark instead of the wordmark
 * @var string|null $text Accessible name, defaults to the translated label
 * @var string|null $class Additional class names for the wrapper
 */

use Kirby\Toolkit\Html;

$type = $type ?? null;
$type = $type instanceof Kirby\Content\Field ? $type->value() : $type;

if (in_array($type, ['generated', 'modified'], true) === false) {
	return;
}

$assetManager->add('css', vite()->asset('frontend/styles/snippets/ai-label.css'));

// The compact mark carries no wording, so the meaning only lives in the
// accessible name – which is derived from the type either way.
$compact = $compact ?? false;
$text    = $text ?? t('ai-label.' . $type);
$classes = array_filter([
	'ai-label',
	$compact === true ? 'ai-label--compact' : null,
	$class ?? null
]);
?>
<span class="<?= implode(' ', $classes) ?>" role="img" aria-label="<?= Html::encode($text) ?>" title="<?= Html::encode($text) ?>">
	<?php snippet('ai-label/' . ($compact ? 'ai' : $type)) ?>
</span>
