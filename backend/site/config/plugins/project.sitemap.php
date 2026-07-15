<?php

use Kirby\Toolkit\V;

return [
	'project.sitemap' => [
		'include' => fn($page) => V::in($page->intendedTemplate(), ['home', 'page']),
		'exclude' => fn($page) => V::same($page->metaRobots(), 'noindex,nofollow'),
	]
];
