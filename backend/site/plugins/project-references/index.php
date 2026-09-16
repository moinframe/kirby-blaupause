<?php

use Kirby\Cms\App;

App::plugin(
	'project/references',
	[
		'options' => [
			'cache' => true,
			'expiration' => 15,
		],
		'sections' => require __DIR__ . '/src/extensions/sections.php',
		'translations' => [
			'en' => [
				'project.references.noReferences' => 'No references found.',
				'project.references.loading' => 'Loading…',
				'project.references.defaultLabel'	=> 'Referenced by',
			],
			'de' => [
				'project.references.noReferences' => 'Keine Referenzen gefunden.',
				'project.references.loading' => 'Lädt…',
				'project.references.defaultLabel'	=> 'Eingesetzt auf',
			],
		]
	],
	version: '1.0.0',
	info: [
		'license' => 'MIT',
		'authors' => [
			[
				'name' => 'Justus Kraft',
				'email' => 'justus@moinfra.me'
			],
		],
		'homepage' => 'https://moinfra.me'
	]
);
