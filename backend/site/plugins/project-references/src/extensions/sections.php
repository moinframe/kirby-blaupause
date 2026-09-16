<?php

/**
 * @var \Kirby\Cms\App
 **/

use Kirby\Toolkit\I18n;

return [
	'references' => [
		'props' => [
			'label' => fn($label = null) => I18n::translate($label, $label) ?? '',
			'headline' => fn($headline = null) => I18n::translate($headline, $headline),
		],
		'computed' => [
			'references' => function () {

				$kirby = $this->kirby();
				$site = $kirby->site();
				$model = $this->model();
				$uuid  = $model->uuid()?->toString();

				if (!$uuid) {
					return [];
				}

				$cacheKey = md5($uuid);
				$cache    = $kirby->cache('project.references');
				$result   = $cache->get($cacheKey);

				if ($result !== null) {
					return $result;
				}

				$references = [];

				foreach ($site->index(true) as $page) {
					foreach ($page->content()->fields() as $field) {
						if (str_contains((string)$field->value(), $uuid)) {
							$references[] = [
								'text'  => $page->title()->value(),
								'id'    => $page->id(),
								'link'  => $page->panel()->url(true),
							];
							break;
						}
					}
				}

				foreach ($site->content()->fields() as $field) {
					if (str_contains((string)$field->value(), $uuid)) {
						$references[] = [
							'text'  => $site->title()->value(),
							'id'    => $site->id(),
							'link'  => $site->panel()->url(true),
						];
						break;
					}
				}


				$cache->set($cacheKey, $references, option('project.references.expiration', 15));
				return $references;
			},
		],
	],
];
