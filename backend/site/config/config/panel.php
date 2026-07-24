<?php

return [
	"ready" => fn() => [
		"panel" => [
			"css" => vite("frontend/panel.css"),
			"favicon" => vite()->asset("frontend/assets/panel/favicon.svg"),
			"js" => vite("frontend/panel.ts"),
			"vue.compiler" => false,
			"menu" => fn($kirby) => panelMenu($kirby)
				->site(['label' => 'Dashboard', 'icon' => 'dashboard'])
				->separator()
				->page('Media', 'page://globalmedia', ['icon' => 'images'])
				->separator()
				->area('retour')
				->separator()
				->area('help')
				->area('users')
				->area('languages')
				->area('system')
				->toArray(),
			'viewButtons' => [
				'page' => ['typo-and-paste', 'accessibility-check', 'open', 'preview', 'settings', 'languages', 'status'],
				'site' => ['typo-and-paste', 'open', 'preview', 'languages']
			]
		],
	],
];
