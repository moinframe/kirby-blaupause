<?php

date_default_timezone_set('Europe/Berlin');

return [

	// Core configurations
	...require __DIR__ . '/config/cache.php',
	...require __DIR__ . '/config/date.php',
	...require __DIR__ . '/config/debug.php',
	...require __DIR__ . '/config/languages.php',
	...require __DIR__ . '/config/panel.php',
	...require __DIR__ . '/config/theme.php',
	...require __DIR__ . '/config/security.php',

	// Plugin configurations
	...require __DIR__ . '/plugins/distantnative.retour.php',
	...require __DIR__ . '/plugins/femundfilou.asset-manager.php',
	...require __DIR__ . '/plugins/moinframe.image-snippet.php',
	...require __DIR__ . '/plugins/philippoehrlein.typo-and-paste.php',

	// Project configurations
	...require __DIR__ . '/plugins/project.headers.php',
	...require __DIR__ . '/plugins/project.sitemap.php',
	...require __DIR__ . '/plugins/project.plausible.php',

	// Routes
	'routes' => require __DIR__ . '/routes/index.php',
];
