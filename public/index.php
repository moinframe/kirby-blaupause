<?php

use Kirby\Cms\App;

$base = __DIR__;
$storage = dirname(__DIR__) . '/storage';
$backend = dirname(__DIR__) . '/backend';
require $backend . '/kirby/bootstrap.php';

require_once $backend . '/site/plugins/kirby3-dotenv/global.php';

loadenv([
	'dir' => realpath(dirname(__DIR__)),
	'file' => '.env',
]);

echo (new App([
	'roots' => [
		'index'     => $base,
		'media'     => $base . '/media',
		'site'      => $backend . '/site',
		'content'   => $storage . '/content',
		'accounts'  => $storage . '/accounts',
		'cache' 	=> $base . '/cache',
		'sessions'  => $storage . '/sessions',
		'logs'  	=> $storage . '/logs',
		'license'   => $storage . '/license/.license',
		'redirects' => $storage . '/redirects/retour.yml'
	]
]))->render();
