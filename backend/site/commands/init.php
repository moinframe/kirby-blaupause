<?php

use Kirby\Filesystem\F;
use Kirby\Data\Data;

return [
	'description' => 'Initialize Kirby Blaupause for a new project',
	'args' => [
		'name' => [
			'prefix'      => 'n',
			'longPrefix'  => 'name',
			'description' => 'Project name, used for the composer/package name (e.g. my-site)',
		],
		'vendor' => [
			'longPrefix'  => 'vendor',
			'description' => 'Composer vendor namespace (default: project)',
		],
		'domain' => [
			'longPrefix'  => 'domain',
			'description' => 'Local development domain (e.g. my-site.test)',
		],
	],
	'command' => static function ($cli): void {
		$root = dirname(kirby()->root());

		// Turn arbitrary input into a valid composer/npm name segment.
		$slug = static function (?string $value): string {
			$value = strtolower(trim((string) $value));
			$value = preg_replace('/[^a-z0-9._-]+/', '-', $value);
			return trim($value, '-._');
		};

		// Read a JSON file, apply $mutate to the decoded array, write it back.
		// Skips gracefully if the file does not exist.
		$updateJson = static function (string $file, callable $mutate) use ($cli, $root): bool {
			$path = $root . '/' . $file;
			if (F::exists($path) === false) {
				$cli->out('  - skipped ' . $file . ' (not found)');
				return false;
			}
			Data::write($path, $mutate(Data::read($path)));
			$cli->out('  - updated ' . $file);
			return true;
		};

		$envPath = $root . '/.env';

		// Make sure a .env exists, seeded from .env.example when available.
		$ensureEnv = static function () use ($cli, $envPath, $root): void {
			if (F::exists($envPath)) {
				return;
			}

			if (F::exists($root . '/.env.example')) {
				F::copy($root . '/.env.example', $envPath);
				$cli->out('  - created .env from .env.example');
				return;
			}

			F::write($envPath, '');
		};

		// Current value of a key in .env, empty string if unset or missing.
		$getEnv = static function (string $key) use ($envPath): string {
			if (F::exists($envPath) === false) {
				return '';
			}
			$pattern = '/^' . preg_quote($key, '/') . '=(.*)$/m';
			return preg_match($pattern, F::read($envPath), $m) === 1 ? trim($m[1]) : '';
		};

		// Replace a key in .env, appending it when it is not present yet.
		$setEnv = static function (string $key, string $value) use ($envPath, $ensureEnv): void {
			$ensureEnv();

			$env     = F::read($envPath);
			$line    = $key . '=' . $value;
			$pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

			if (preg_match($pattern, $env) === 1) {
				// callback replacement: the value may contain $ or \ sequences
				$env = preg_replace_callback($pattern, static fn (): string => $line, $env);
			} else {
				$env = $env === '' ? $line . "\n" : rtrim($env, "\n") . "\n" . $line . "\n";
			}

			F::write($envPath, $env);
		};

		$changes = [];

		// ── Names & metadata ───────────────────────────────────────────
		$vendor = $slug($cli->argOrPrompt('vendor', 'Composer vendor namespace [project]:', false)) ?: 'project';

		$name = $slug($cli->argOrPrompt('name', 'Project name (lowercase, e.g. my-site):'));
		while ($name === '') {
			$name = $slug($cli->prompt('Please enter a valid name (letters or numbers required):'));
		}

		$description = '';

		if ($cli->confirm('Update names & metadata in composer.json and package.json?')->confirmed()) {
			$description  = $cli->prompt('Short project description (optional):', false);
			$authorName   = $cli->prompt('Author name (optional):', false);
			$authorEmail  = $cli->prompt('Author email (optional):', false);
			$homepage     = $cli->prompt('Project homepage URL (optional):', false);

			$updateJson('composer.json', static function (array $data) use ($vendor, $name, $description, $authorName, $authorEmail, $homepage): array {
				$data['name'] = $vendor . '/' . $name;

				if ($description !== '') {
					$data['description'] = $description;
				}

				if ($homepage !== '') {
					$data['homepage'] = $homepage;
				} else {
					unset($data['homepage']);
				}

				if ($authorName !== '') {
					$author = ['name' => $authorName];
					if ($authorEmail !== '') {
						$author['email'] = $authorEmail;
					}
					if ($homepage !== '') {
						$author['homepage'] = $homepage;
					}
					$data['authors'] = [$author];
				} else {
					unset($data['authors']);
				}

				return $data;
			});

			$updateJson('package.json', static function (array $data) use ($name, $description, $authorName, $authorEmail): array {
				$data['name']     = $name;
				$data['version']  = '0.0.0';
				$data['keywords'] = [];

				if ($description !== '') {
					$data['description'] = $description;
				}

				if ($authorName !== '') {
					$data['author'] = $authorEmail !== ''
						? $authorName . ' <' . $authorEmail . '>'
						: $authorName;
				} else {
					unset($data['author']);
				}

				return $data;
			});

			// Reset the release-managed version marker.
			$envPhp = $root . '/backend/site/config/env.php';
			if (F::exists($envPhp)) {
				F::write($envPhp, "<?php\n\nreturn [\n\t\"version\" => \"0.0.0\"\n];\n");
			}

			$changes[] = 'names & metadata';
		}

		// ── Local environment (.env + config) ──────────────────────────
		$domainInput = trim((string) $cli->argOrPrompt('domain', 'Local development domain (e.g. ' . $name . '.test) [skip with empty]:', false));

		$host = '';
		if ($domainInput !== '') {
			$scheme = preg_match('#^(https?)://#i', $domainInput, $m) ? strtolower($m[1]) : 'https';
			$host   = rtrim(preg_replace('#^https?://#i', '', $domainInput), '/');
			$url    = $scheme . '://' . $host;

			// Update APP_URL in .env
			if ($cli->confirm('Set APP_URL in .env to ' . $url . '?')->confirmed()) {
				$setEnv('APP_URL', $url);
				$cli->out('  - updated .env');
				$changes[] = '.env APP_URL';
			}

			// Rename the host-specific config file (idempotent)
			if ($cli->confirm('Rename the local config file to config.' . $host . '.php?')->confirmed()) {
				$configDir = $root . '/backend/site/config';
				$source    = $configDir . '/config.kirby-blaupause.test.php';
				$target    = $configDir . '/config.' . $host . '.php';

				if (F::exists($target)) {
					$cli->out('  - config.' . $host . '.php already exists, skipped');
				} elseif (F::exists($source) === false) {
					$cli->out('  - template config file not found, skipped');
				} else {
					F::move($source, $target);
					$cli->out('  - renamed local config file');
					$changes[] = 'local config file';
				}
			}
		}

		// ── Security keys (.env) ───────────────────────────────────────
		// content.salt and cookie.key are read from .env in
		// backend/site/config/config/security.php and must be unique per project.
		$secrets = ['CONTENT_SALT', 'COOKIE_KEY'];
		$missing = array_values(array_filter($secrets, static fn (string $key): bool => $getEnv($key) === ''));
		$present = array_values(array_diff($secrets, $missing));

		$rotate = [];

		if ($missing !== [] && $cli->confirm('Generate a secure ' . implode(' and ', $missing) . ' in .env?')->confirmed()) {
			$rotate = $missing;
		}

		if ($present !== []) {
			$cli->out(implode(' and ', $present) . ' already set in .env.');
			$cli->out('  Regenerating invalidates existing sessions, media URLs and preview tokens.');

			if ($cli->confirm('Regenerate ' . implode(' and ', $present) . ' anyway?')->confirmed()) {
				$rotate = [...$rotate, ...$present];
			}
		}

		if ($rotate !== []) {
			foreach ($rotate as $key) {
				$setEnv($key, bin2hex(random_bytes(32)));
			}
			$cli->out('  - generated ' . implode(' and ', $rotate) . ' in .env');
			$changes[] = 'security keys';
		}

		// ── Template cleanup ───────────────────────────────────────────
		if ($cli->confirm('Replace README.md with a minimal project stub (and remove the banner image)?')->confirmed()) {
			$title  = $name !== '' ? $name : 'Project';
			$readme = '# ' . $title . "\n";
			if ($description !== '') {
				$readme .= "\n" . $description . "\n";
			}
			F::write($root . '/README.md', $readme);

			if (F::exists($root . '/kirby-blaupause.png')) {
				F::remove($root . '/kirby-blaupause.png');
			}
			$cli->out('  - reset README.md and removed banner image');
			$changes[] = 'README & banner';
		}

		// ── Remove template tooling (keep this last) ───────────────────
		if ($cli->confirm('Remove the template tooling (this init command and scripts/test-template.sh)?')->confirmed()) {
			if (F::exists($root . '/scripts/test-template.sh')) {
				F::remove($root . '/scripts/test-template.sh');
				$cli->out('  - removed scripts/test-template.sh');
			}
			// keep this removal last: the command file is executing right now
			F::remove($root . '/backend/site/commands/init.php');
			$cli->out('  - removed backend/site/commands/init.php');
			$changes[] = 'template tooling';
		}

		// ── Summary ────────────────────────────────────────────────────
		$cli->br();
		if ($changes === []) {
			$cli->out('No changes were made.');
			return;
		}

		$cli->success('All done. Applied: ' . implode(', ', $changes) . '.');
		$cli->out('Tip: run `pnpm format` to normalise composer.json / package.json indentation.');
	}
];
