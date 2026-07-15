<?php
$pages = site()->index();
$includeCallback = option('project.sitemap.include', null);
$excludeCallback = option('project.sitemap.exclude', null);
$priorities = option('project.sitemap.priority', []);
$changefreqs = option('project.sitemap.changefreq', []);

$isMultilingual = $kirby->languages()->count() > 1;
$languages = $isMultilingual ? $kirby->languages() : [];
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL ?>
<?= '<?xml-stylesheet type="text/xsl" href="' . $kirby->url() . '/sitemap.xsl"?>' . PHP_EOL ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	<?php if ($isMultilingual): ?>xmlns:xhtml="http://www.w3.org/1999/xhtml" <?php endif ?>>
	<?php foreach ($pages as $page): ?>
		<?php

		if (!is_callable($includeCallback) || !$includeCallback($page)) {
			continue;
		}

		if (is_callable($excludeCallback) && $excludeCallback($page)) {
			continue;
		}

		// For multilingual sites, use the default language version of each page
		if ($isMultilingual && !$page->translation($kirby->defaultLanguage()->code())->exists()) {
			continue;
		}

		// Calculate priority: 1.0 for homepage, 0.5/depth for others (0.5 is the default priority)
		$basePriority = $page->isHomePage() ? 1.0 : round(0.5 / $page->depth(), 1);

		// Allow template-specific overrides
		$template = $page->intendedTemplate()->name();
		$priority = $priorities[$template] ?? $basePriority;
		$changefreq = $changefreqs[$template] ?? 'monthly';

		// Format lastmod
		$lastmod = $page->modified('Y-m-d\TH:i:s\Z', 'date');

		// Use default language URL or regular URL
		$pageUrl = $isMultilingual ? $page->url($kirby->defaultLanguage()->code()) : $page->url();
		?>
		<url>
			<loc><?= $pageUrl ?></loc>
			<lastmod><?= $lastmod ?></lastmod>
			<changefreq><?= $changefreq ?></changefreq>
			<priority><?= $priority ?></priority>
			<?php if ($isMultilingual): ?>
				<?php foreach ($languages as $lang): ?>
					<?php if ($page->translation($lang->code())->exists()): ?>
						<xhtml:link
							rel="alternate"
							hreflang="<?= $lang->code() ?>"
							href="<?= $page->url($lang->code()) ?>" />
					<?php endif ?>
				<?php endforeach ?>
				<?php if (count($languages) > 1): ?>
					<xhtml:link
						rel="alternate"
						hreflang="x-default"
						href="<?= $page->url($kirby->defaultLanguage()->code()) ?>" />
				<?php endif ?>
			<?php endif ?>
		</url>
	<?php endforeach ?>
</urlset>
