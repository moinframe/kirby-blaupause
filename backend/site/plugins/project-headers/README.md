# Project Headers

Sends security and other common HTTP response headers, configurable via `site/config/plugins/project.headers.php`.

## Options

| Option    | Default         | Description                                                                                    |
| --------- | --------------- | ---------------------------------------------------------------------------------------------- |
| `enabled` | `null`          | `null` = auto: enabled unless `$kirby->system()->isLocal()`. Also accepts a bool or a closure. |
| `headers` | see `index.php` | Map of header name → value. Merged with the defaults, so single headers can be overridden.     |

Default headers: `X-Frame-Options`, `X-Content-Type-Options`, `Strict-Transport-Security`, `Referrer-Policy`, `Permissions-Policy`, `Content-Security-Policy` and removal of `X-Powered-By`.

Headers are never applied to panel, api or media responses.

## Header values

- **String**: sent as-is. `{nonce}` is replaced with `$kirby->nonce()` (see below).
- **Closure**: called with `$kirby`, must return a string or `null`.
- **`null`**: header is removed (also unsets headers PHP adds itself, e.g. `X-Powered-By`).

```php
return [
	'project.headers' => [
		'headers' => [
			'Content-Security-Policy' => null, // drop a default
			'X-Robots-Tag' => fn($kirby) => $kirby->environment()->isLocal() ? 'noindex' : null,
		],
	],
];
```

## CSP nonces for inline scripts/styles

The default `Content-Security-Policy` contains `'nonce-{nonce}'` in `script-src`. Inline `<script>` tags must therefore carry the matching attribute:

```php
<script nonce="<?= $kirby->nonce() ?>">…</script>
```

Once a nonce is present in a directive, CSP3 browsers ignore `'unsafe-inline'` there (it only remains as a fallback for old browsers) — so **every** inline script needs the attribute.

## Caching

The pages cache (staticache with `'headers' => true`, see `site/config/config/cache.php`) stores these headers together with the rendered HTML in one cache file, so a cached page's CSP nonce always matches the nonce in its markup.

- Without direct serving, Kirby re-renders every request (staticache is write-only through PHP), so headers and nonces are always fresh.
- To serve cache files directly, the server must replay the stored header block: Apache needs `mod_asis` (`send-as-is` handler) or staticache's PHP loader — see the [staticache readme](https://github.com/getkirby/staticache). The plain rewrite rules commented out in `public/.htaccess` would print the header block as text and are incompatible with `'headers' => true`.
- Flush the pages cache after changing header config, otherwise directly served pages keep their old headers.
