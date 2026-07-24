<?php
return [
	'philippoehrlein.typo-and-paste' => [
		'characters' => [
			[
				'label' => [
					'de' => 'Anführungszeichen',
					'en' => 'Quotation marks',
				],
				'lang' => 'de',
				'characters' => [
					['value' => '„', 'label' => ['de' => 'Anführungszeichen unten (öffnend)', 'en' => 'German opening quote (low)']],
					['value' => '“', 'label' => ['de' => 'Anführungszeichen oben (schließend)', 'en' => 'German closing quote (high)']],
					['value' => '‚', 'label' => ['de' => 'Einfaches Anführungszeichen unten (öffnend)', 'en' => 'German opening single quote (low)']],
					['value' => '‘', 'label' => ['de' => 'Einfaches Anführungszeichen oben (schließend)', 'en' => 'German closing single quote (high)']],
					['value' => '»', 'label' => ['de' => 'Guillemet öffnend (»…«)', 'en' => 'German opening guillemet']],
					['value' => '«', 'label' => ['de' => 'Guillemet schließend (»…«)', 'en' => 'German closing guillemet']],
				],
			],
			[
				'label' => [
					'de' => 'Geschützte Leerzeichen & Trennung',
					'en' => 'Non-breaking spaces & hyphenation',
				],
				'characters' => [
					[
						'value' => "\u{00A0}", // NO-BREAK SPACE
						'label' => [
							'de' => 'Geschütztes Leerzeichen – verhindert Umbruch, z. B. „20 g", „§ 5", „10 €", „Dr. Müller"',
							'en' => 'Non-breaking space (nbsp) – keeps e.g. “20 g”, “§ 5” together',
						],
					],
					[
						'value' => "\u{202F}", // NARROW NO-BREAK SPACE
						'label' => [
							'de' => 'Schmales geschütztes Leerzeichen – für „z. B.", „u. a." und Tausender „1 000"',
							'en' => 'Narrow non-breaking space – for abbreviations and digit grouping',
						],
					],
					[
						'value' => "\u{00AD}", // SOFT HYPHEN (&shy;)
						'label' => [
							'de' => 'Bedingter Trennstrich (weich) – nur sichtbar am Zeilenende, für lange Komposita wie „Donaudampfschifffahrt"',
							'en' => 'Soft hyphen (&shy;) – optional break point in long words',
						],
					],
					[
						'value' => "\u{2011}", // NON-BREAKING HYPHEN
						'label' => [
							'de' => 'Geschützter Bindestrich – Bindestrich ohne Umbruch, z. B. „E‑Mail", „Covid‑19"',
							'en' => 'Non-breaking hyphen – hyphen that never breaks',
						],
					],
					[
						'value' => "\u{2060}", // WORD JOINER
						'label' => [
							'de' => 'Wortverbinder – unsichtbar, verhindert Umbruch an dieser Stelle',
							'en' => 'Word joiner – invisible, prevents a line break at this point',
						],
					],
				],
			],
			[
				'label' => [
					'de' => 'Striche',
					'en' => 'Dashes',
				],
				'characters' => [
					['value' => "\u{2013}", 'label' => ['de' => 'Gedankenstrich / Halbgeviert – auch Bis-Strich „Mo–Fr", „10–12 Uhr"', 'en' => 'En dash']],
					['value' => "\u{2014}", 'label' => ['de' => 'Geviertstrich (Em dash)', 'en' => 'Em dash']],
					['value' => "\u{2212}", 'label' => ['de' => 'Minuszeichen (echtes Minus), z. B. „−5 °C"', 'en' => 'Minus sign']],
				],
			],
			[
				'label' => [
					'de' => 'Einheiten & Maße',
					'en' => 'Units & measurements',
				],
				'characters' => [
					['value' => "\u{00D7}", 'label' => ['de' => 'Maßkreuz / Malzeichen „4 × 3 m"', 'en' => 'Multiplication / dimension sign']],
					['value' => "\u{00B2}", 'label' => ['de' => 'Hochgestellte 2 – „m²"', 'en' => 'Superscript two']],
					['value' => "\u{00B3}", 'label' => ['de' => 'Hochgestellte 3 – „m³"', 'en' => 'Superscript three']],
					['value' => '°', 'label' => ['de' => 'Gradzeichen „20 °C"', 'en' => 'Degree sign']],
					['value' => '±', 'label' => ['de' => 'Plusminuszeichen', 'en' => 'Plus-minus sign']],
					['value' => '‰', 'label' => ['de' => 'Promillezeichen', 'en' => 'Per mille sign']],
					['value' => "\u{2032}", 'label' => ['de' => 'Minutenzeichen / Fuß (Prime)', 'en' => 'Prime (minutes / feet)']],
					['value' => "\u{2033}", 'label' => ['de' => 'Sekundenzeichen / Zoll (Double Prime)', 'en' => 'Double prime (seconds / inches)']],
					['value' => '€', 'label' => ['de' => 'Euro-Zeichen', 'en' => 'Euro sign']],
				],
			],
			[
				'label' => [
					'de' => 'Rechtliche & sonstige Zeichen',
					'en' => 'Legal & misc',
				],
				'characters' => [
					['value' => '§', 'label' => ['de' => 'Paragraphenzeichen (Impressum, Rechtstexte)', 'en' => 'Section sign']],
					['value' => '©', 'label' => ['de' => 'Copyrightzeichen', 'en' => 'Copyright sign']],
					['value' => '®', 'label' => ['de' => 'Eingetragenes Warenzeichen', 'en' => 'Registered trademark']],
					['value' => '™', 'label' => ['de' => 'Markenzeichen', 'en' => 'Trademark']],
					['value' => '…', 'label' => ['de' => 'Auslassungspunkte', 'en' => 'Ellipsis']],
					['value' => '•', 'label' => ['de' => 'Aufzählungspunkt', 'en' => 'Bullet']],
					['value' => '·', 'label' => ['de' => 'Mittelpunkt (z. B. Trennung)', 'en' => 'Middle dot']],
				],
			],

		],
	],
];
