<?php

use Kirby\Cms\App;
use Kirby\Toolkit\Str;

App::plugin('project/headers', [
	'options' => [
		// null = auto: enabled unless the setup is local; also accepts bool or closure
		'enabled' => null,
		// values may be strings or closures; null removes the header;
		// '{nonce}' in a value is replaced with $kirby->nonce()
		'headers' => [
			'X-Frame-Options'           => 'SAMEORIGIN',
			'X-Content-Type-Options'    => 'nosniff',
			'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains; preload',
			'Referrer-Policy'           => 'strict-origin-when-cross-origin',
			'Permissions-Policy'        => 'geolocation=(), camera=(), microphone=(), payment=()',
			'Content-Security-Policy'   => "default-src 'self'; font-src 'self' data:; img-src * data:; script-src * data: blob: 'unsafe-eval' 'nonce-{nonce}'; style-src 'self';",
			'X-Powered-By'              => null,
		],
	],
	'hooks' => [
		'route:before' => function () {
			$kirby = App::instance();

			$enabled = $kirby->option('project.headers.enabled');
			if ($enabled instanceof Closure) {
				$enabled = $enabled($kirby);
			}
			$enabled ??= $kirby->system()->isLocal() === false;

			if ((bool)$enabled === false) {
				return;
			}

			// never touch panel, api or media responses
			$path = $kirby->path();
			$reserved = [
				$kirby->option('panel.slug', 'panel'),
				$kirby->option('api.slug', 'api'),
				'media'
			];

			foreach ($reserved as $slug) {
				if ($path === $slug || Str::startsWith($path, $slug . '/')) {
					return;
				}
			}

			foreach ($kirby->option('project.headers.headers', []) as $name => $value) {
				if ($value instanceof Closure) {
					$value = $value($kirby);
				}

				if ($value === null) {
					// also covers headers PHP sets itself, e.g. X-Powered-By
					header_remove($name);
					$kirby->response()->header($name, false);
					continue;
				}

				$value = Str::replace($value, '{nonce}', $kirby->nonce());

				// set via the Responder so staticache ("headers" option) caches
				// the header together with the rendered HTML and a page's CSP
				// nonce stays in sync with the nonce baked into its markup
				$kirby->response()->header($name, $value);
			}
		}
	]
], version: '1.0.0', info: [
	'license' => 'MIT',
	'authors' => [
		['name' => 'Justus Kraft', 'email' => 'justus@moinfra.me']
	],
	'homepage' => 'https://moinfra.me',
]);
