<?php if ($kirby->option('debug')): ?>
	User-agent: *
	Disallow: /
<?php else: ?>
	User-agent: *
	Disallow: /kirby/
	Disallow: /site/
	Allow: /media/

	Sitemap: <?= $kirby->url() ?>/sitemap.xml
<?php endif ?>