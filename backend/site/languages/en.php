<?php

use Kirby\Data\Yaml;
use Kirby\Filesystem\F;

return [
	'code' => 'en',
	'default' => false,
	'direction' => 'ltr',
	'locale' => [
		'LC_ALL' => 'en_US'
	],
	'name' => 'English',
	'translations' => Yaml::decode(
		F::read(kirby()->root('languages') . '/vars/en.yml')
	),
	'url' => null
];
