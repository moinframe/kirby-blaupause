<?php

use Kirby\Cms\Response;
use Kirby\Cms\Page;

Kirby\Cms\App::plugin(
	'project/sitemap',
	[
		'options' => [
			'include' => null,
			'exclude' => null,
			'priority' => [
				'home' => 1.0,
				'default' => 0.8
			],
			'changefreq' => [
				'home' => 'daily',
				'default' => 'weekly'
			]
		],
		'templates' => [
			'sitemap' => __DIR__ . '/templates/sitemap.php',
			'sitemap.xsl' => __DIR__ . '/templates/sitemap.xsl.php',
			'robots' => __DIR__ . '/templates/robots.php'
		],
		'siteMethods' => [
			'sitemap' => function () {
				return Page::factory([
					'slug' => 'sitemap',
					'template' => 'sitemap',
					'model' => 'sitemap',
					'content' => [
						'title' => 'Sitemap',
					],
				]);
			},
			'robots' => function () {
				return Page::factory([
					'slug' => 'robots',
					'template' => 'robots',
					'model' => 'robots',
					'content' => [
						'title' => 'Robots.txt',
					],
				]);
			}
		],
		'routes' => [
			[
				'pattern' => 'sitemap.xml',
				'action' => fn() => new Response(kirby()->site()->sitemap()->render(), 'application/xml')
			],
			[
				'pattern' => 'sitemap.xsl',
				'action' => fn() => new Response(kirby()->site()->sitemap()->render(contentType: 'xsl'), 'application/xslt+xml')
			],
			[
				'pattern' => 'sitemap',
				'action'  => fn() => go('sitemap.xml', 301)
			],
			[
				'pattern' => 'robots.txt',
				'action'  => fn() => new Response(kirby()->site()->robots()->render(), 'text/plain')
			]
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
