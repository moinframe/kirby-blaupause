<?php

use Kirby\Template\Snippet;
use Kirby\Toolkit\V;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Dom;

/**
 * Returns the icon snippet
 * @param string $name Name of the icon
 * @param string $size Size of the icon
 * @return Snippet|string|null
 */
function icon(string $name, string $size = '1em')
{
	return snippet('icon', ['name' => $name, 'size' => $size], true);
}

Kirby\Cms\App::plugin('project/helper', [
	'fieldMethods' => [
		'convertShy' => function (Kirby\Content\Field $field) {
			return Str::replace($field, '&amp;', '&');
		},
		/**
		 * Adds animation props to list items
		 * @param string $animation Animation type
		 * @return string Modified HTML
		 */
		'addListAnimation' => function (Kirby\Content\Field $field, string $animation = 'fade-in-up'): string {
			$dom = new Dom($field->value());

			$listItems = $dom->query('//li');
			foreach ($listItems as $index => $li) {
				if ($li instanceof \DOMElement) {
					$li->setAttribute('data-animation', $animation);
				}
			}

			return $dom->toString();
		}
	],
	'blockMethods' => [
		'getWidthClass' => function () {
			$classMap = [
				'default' => 'span-content',
				'full' => 'span-full'
			];

			if ($this->width()->isNotEmpty()) {
				$value = $this->width()->value();
				if (isset($classMap[$value]) && !empty($classMap[$value])) {
					return $classMap[$value];
				}
			}

			return 'span-content';
		},

		'getTextAlignClass' => function () {
			$classMap = [
				'left' => 'has-text-left',
				'center' => 'has-text-center',
				'right' => 'has-text-right',
				'justify' => 'has-text-justify'
			];

			if ($this->textalign()->isNotEmpty()) {
				$value = $this->textalign()->value();
				if (isset($classMap[$value]) && !empty($classMap[$value])) {
					return $classMap[$value];
				}
			}

			return 'has-text-left';
		},

		'getVerticalAlignClass' => function () {
			$classMap = [
				'top' => 'has-justify-start',
				'bottom' => 'has-justify-end',
				'center' => 'has-justify-center',
				'stretch' => 'has-justify-stretch'
			];

			if ($this->verticalalign()->isNotEmpty()) {
				$value = $this->verticalalign()->value();
				if (isset($classMap[$value]) && !empty($classMap[$value])) {
					return $classMap[$value];
				}
			}

			return 'has-justify-start';
		},

		'getSpaceTopClass' => function () {
			$classMap = [
				'none' => 'has-gap-collapse',
				'small' => 'has-mt-l',
				'medium' => 'has-mt-2xl-3xl',
				'large' => 'has-mt-xl-4xl'
			];

			if ($this->spacetop()->isNotEmpty()) {
				$value = $this->spacetop()->value();
				if (isset($classMap[$value]) && !empty($classMap[$value])) {
					return $classMap[$value];
				}
			}

			return '';
		},

		'getSpaceBottomClass' => function () {
			$classMap = [
				'none' => '',
				'small' => 'has-mb-l',
				'medium' => 'has-mb-2xl-3xl',
				'large' => 'has-mb-xl-4xl'
			];

			if ($this->spacebottom()->isNotEmpty()) {
				$value = $this->spacebottom()->value();
				if (isset($classMap[$value]) && !empty($classMap[$value])) {
					return $classMap[$value];
				}
			}

			return '';
		},
		'getAllAlignmentClasses' => function () {
			return implode(' ', [
				$this->getWidthClass(),
				$this->getTextAlignClass(),
				$this->getVerticalAlignClass(),
				$this->getSpaceTopClass(),
				$this->getSpaceBottomClass()
			]);
		}
	],
	'pageMethods' => [
		'mediaPage' => function () {
			$mediaPage = page('page://globalmedia');
			if (!V::in($this->intendedTemplate(), [])) return $mediaPage ?? $this;
			return $this;
		},
	],
	'siteMethods' => [
		'mediaPage' => function () {
			return $this->children()->first()->mediaPage();
		},
	],
], version: '1.0.0', info: ['license' => 'MIT']);
