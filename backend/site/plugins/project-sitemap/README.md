# Sitemap Plugin

A flexible XML sitemap generator for Kirby CMS with built-in XSL stylesheet for browser viewing.

## Usage

### Basic Usage

After installation, the sitemap will be available at:
- `https://yoursite.com/sitemap.xml`
- `https://yoursite.com/sitemap` (redirects to XML)

### Configuration Options

Add these options to your `config.php` file:

```php
return [
    'project.sitemap' => [
        'include' => fn($page) => V::in($page->intendedTemplate(), ['home', 'page']),
        'exclude' => fn($page) => V::same($page->metaRobots(), 'noindex,nofollow'),
        'priority' => [
            'about' => 0.9, // Overwrites for template 'about'
        ],
        'changefreq' => [
            'home' => 'daily', // Overwrites for template 'home'
        ]
    ]
];
```

## Configuration Reference

### `include` (callable)
**Default:** `null`

Callback function that receives a `$page` object and returns `true` to include the page in the sitemap. **If not set or not callable, no pages will be included.**

```php
'include' => fn($page) => V::in($page->intendedTemplate(), ['home', 'about', 'blog', 'project'])
```

### `exclude` (callable)
**Default:** `null`

Callback function that receives a `$page` object and returns `true` to exclude the page from the sitemap. **This filter is applied after the include filter.**

```php
'exclude' => fn($page) => V::same($page->metaRobots(), 'noindex,nofollow')
```

### `priority` (array)
**Default:** `[]`

Set custom priority values (0.0 to 1.0) for specific templates. Pages without a defined priority will use automatic calculation based on page depth.

```php
'priority' => [
    'home' => 1.0,      // Homepage gets highest priority
    'about' => 0.9,     // About page high priority
    'blog' => 0.8,      // Blog posts medium priority
]
```

### `changefreq` (array)
**Default:** `[]`

Set change frequency hints for search engines. Valid values: `always`, `hourly`, `daily`, `weekly`, `monthly`, `yearly`, `never`.

```php
'changefreq' => [
    'home' => 'daily',     // Homepage changes daily
    'blog' => 'weekly',    // Blog posts weekly
    'about' => 'yearly',   // Static pages rarely change
]
```

## Advanced Usage

### Programmatic access to Sitemap (virtual) Page

```php
$sitemapPage = site()->sitemap();
```

## Requirements

- Kirby 4.0+
- PHP 8.0+

## License

MIT License - feel free to use in personal and commercial projects.
