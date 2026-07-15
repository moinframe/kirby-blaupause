<?php

/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 */
?>
<!DOCTYPE html>
<html lang="<?= $kirby->language()->code() ?>">

<head>
	<!DOCTYPE html>
	<html lang="<?= $kirby->language()->code() ?>" class="no-js" data-theme="light">

	<head>
		<meta charset="UTF-8" />
		<!-- Viewport -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!-- Title -->
		<title><?= $page->metaTitle()->or($page->title() . ' - ' . $site->title()) ?></title>
		<!-- Description -->
		<meta name="description" content="<?= $page->metaDescription()->or($site->metaDescription()) ?>" />
		<!-- Robots -->
		<meta name="robots" content="<?= $page->metaRobots()->or($site->metaRobots()) ?>" />
		<!-- Canonical -->
		<link rel="canonical" href="<?= $page->url() ?>" />
		<!-- Open Graph -->
		<meta property="og:url" content="<?= $page->url() ?>">
		<meta property="og:type" content="website">
		<meta property="og:title" content="<?= $page->metaTitle()->or($page->title() . ' - ' . $site->title()) ?>">
		<meta property="og:description" content="<?= $page->metaDescription()->or($site->metaDescription()) ?>">
		<meta property="og:site_name" content="<?= $site->title() ?>">
		<meta property="og:locale" content="<?= $kirby->language()->code() ?>">
		<!-- OG Image -->
		<?php if ($ogImage = $page->ogImage()->or($site->ogImage())->toFile()): ?>
			<meta property="og:image" content="<?= $ogImage->crop(1200, 630)->url() ?>">
			<meta property="og:image:alt" content="<?= $ogImage->alt() ?>">
		<?php endif; ?>
		<!-- AssetManager CSS -->
		<!-- JS -->
		<?= vite(['frontend/index.ts']) ?>
		<!-- Analytics -->
		<?php if (option('project.plausible.enabled')): ?>
			<script async src="#"></script>
			<script>
				window.plausible = window.plausible || function() {
					(plausible.q = plausible.q || []).push(arguments)
				}, plausible.init = plausible.init || function(i) {
					plausible.o = i || {}
				};
				plausible.init()
			</script>
		<?php endif; ?>
		<!-- Favicon -->
		<link rel="icon" type="image/png" href="<?= $kirby->url() ?>/favicon-96x96.png" sizes="96x96" />
		<link rel="icon" type="image/svg+xml" href="<?= $kirby->url() ?>/favicon.svg" />
		<link rel="shortcut icon" href="<?= $kirby->url() ?>/favicon.ico" />
		<link rel="apple-touch-icon" sizes="180x180" href="<?= $kirby->url() ?>/apple-touch-icon.png" />
		<meta name="apple-mobile-web-app-title" content="<?= $site->title() ?>" />
		<link rel="manifest" href="<?= $kirby->url() ?>/site.webmanifest" />

		<script type="speculationrules">
			{
			"prerender": [{
				"where": {
					"and": [
						{ "href_matches": "/*" },
						{ "not": { "href_matches": "/panel/*" } }
					]
				},
				"eagerness": "moderate"
			}]
		}
	</script>
	</head>

<body data-template="<?= $page->intendedTemplate() ?>">
	<div class="grid">
		<?php snippet('page/header') ?>

		<main id="page" class="subgrid span-full" tabindex="-1">
			<?php if ($page->hero()->isNotEmpty()) : ?>
				<?= $page->hero()->toBlocks() ?>
			<?php endif; ?>
			<?= $slot ?>
		</main>

		<?php snippet('page/footer'); ?>
	</div>
	<script>
		console.info('%cv<?= option('version') ?> | developed by gutekombi.de', 'font-size: 12px; font-weight: bold; color: #6430F2; margin: 8px 0;')
	</script>

</body>

</html>