<?php

return [
	'project.headers' => [
		// 'enabled' => true, // force headers on local setups
		'headers' => [
			// override or add headers here; set a header to null to remove it
			// '{nonce}' in a value is replaced with $kirby->nonce()

			// `frame-src` has to list every host embedded via the content blocker,
			// otherwise it falls back to `default-src 'self'` and no embed loads.
			// `style-src` needs 'unsafe-inline' for the inline custom properties
			// used by the image and video blocks (e.g. style="--aspect-ratio: 16/9")
			'Content-Security-Policy' => "default-src 'self'; font-src 'self' data:; img-src * data:; script-src * data: blob: 'unsafe-eval' 'nonce-{nonce}'; style-src 'self' 'unsafe-inline'; frame-src 'self' https://www.youtube-nocookie.com https://player.vimeo.com;",
		],
	],
];
