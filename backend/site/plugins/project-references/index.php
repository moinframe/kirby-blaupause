<?php

use Kirby\Cms\App;

$flush = function (): void {
	kirby()->cache('project.references')->flush();
};

App::plugin(
	'project/references',
	[
		'options' => [
			'cache' => true,
			'expiration' => 15,
		],
		'sections' => require __DIR__ . '/src/extensions/sections.php',
		'hooks' => [
			'page.update:after' => $flush,
			'page.delete:after' => $flush,
			'page.duplicate:after' => $flush,
			'site.update:after' => $flush,
		],
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
