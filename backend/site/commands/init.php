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
				$envPath = $root . '/.env';

				if (F::exists($envPath) === false) {
					if (F::exists($root . '/.env.example')) {
						F::copy($root . '/.env.example', $envPath);
						$cli->out('  - created .env from .env.example');
					} else {
						F::write($envPath, '');
					}
				}

				$env = F::read($envPath);
				if (preg_match('/^APP_URL=.*$/m', $env)) {
					$env = preg_replace('/^APP_URL=.*$/m', 'APP_URL=' . $url, $env);
				} else {
					$env = 'APP_URL=' . $url . "\n" . ltrim($env);
				}
				F::write($envPath, $env);
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
