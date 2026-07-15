<?php
return [
	"distantnative.retour" => [
		'config' => fn() => kirby()->root('redirects'),
		"deleteAfter" => 1,
		"ignore" => ["media/(:any)"]
	]
];
