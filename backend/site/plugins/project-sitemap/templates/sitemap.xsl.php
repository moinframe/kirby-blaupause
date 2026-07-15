<?php

use Kirby\Cms\Url;

header('Content-Type: application/xslt+xml');

?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL ?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<xsl:output method="html" encoding="UTF-8" indent="yes" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN" doctype-system="http://www.w3.org/TR/html4/loose.dtd" />

	<xsl:template match="/">
		<html lang="en">

		<head>
			<title><?= $site->title() ?> - XML Sitemap</title>
			<meta charset="UTF-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<style>
				:root {
					--color-base: var(--color-neutral-800);
					--color-base-background: var(--color-neutral-50);
					--color-neutral-50: oklch(0.99 0 300);
					--color-neutral-100: oklch(0.95 0 300);
					--color-neutral-200: oklch(0.8 0 300);
					--color-neutral-300: oklch(0.7 0 300);
					--color-neutral-400: oklch(0.6 0 300);
					--color-neutral-500: oklch(0.5 0 300);
					--color-neutral-600: oklch(0.4 0 300);
					--color-neutral-700: oklch(0.3 0 300);
					--color-neutral-800: oklch(0.2 0 300);
					--color-neutral-900: oklch(0.1 0 300);
					--color-blue: #6430F2;
					--color-lime: #DDFF33;
					--color-green: #29CCA3;
					--color-lilac: #AB8CFF;
					--color-black: #000000;
					--color-white: #FFFFFF;
					--color-accent: var(--color-blue);
					--color-accent-invert: var(--color-white);
					--space-l: 2rem;
					--space-m: 1rem;
					--space-s: 0.5rem;
					--space-xs: 0.25rem;
					--border-radius-s: 4px;
					--font-size-l: 1.25rem;
					--font-size-m: 1rem;
					--font-size-s: 0.8rem;
					--font-weight-bold: 600;
				}

				@media (prefers-color-scheme: dark) {
					:root {
						--color-neutral-50: oklch(0.1 0 300);
						--color-neutral-100: oklch(0.2 0 300);
						--color-neutral-200: oklch(0.3 0 300);
						--color-neutral-300: oklch(0.4 0 300);
						--color-neutral-400: oklch(0.5 0 300);
						--color-neutral-500: oklch(0.6 0 300);
						--color-neutral-600: oklch(0.7 0 300);
						--color-neutral-700: oklch(0.8 0 300);
						--color-neutral-800: oklch(0.9 0 300);
						--color-neutral-900: oklch(0.95 0 300);
						--color-accent: var(--color-lime);
						--color-accent-invert: var(--color-black);
					}
				}

				body {
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
					line-height: 1.6;
					margin: 0;
					padding: 2rem;
					background-color: var(--color-base-background);
					color: var(--color-base);
				}

				.container {
					max-width: 1200px;
					margin: 0 auto;
				}

				.header h1 {
					margin: 0 0 var(--space-xs) 0;
					font-size: var(--font-size-l);
					font-weight: var(--font-weight-bold);
				}

				.header p {
					margin: 0;
					color: var(--color-neutral-500);
					font-size: var(--font-size-m);
				}

				.sitemap-table {
					width: 100%;
					border-collapse: collapse;
					background: var(--color-neutral-100);
					margin-top: var(--space-m);
				}

				.sitemap-table th {
					background: var(--color-neutral-200);
					padding: var(--space-s);
					text-align: left;
					font-weight: var(--font-weight-bold);
					color: var(--color-neutral-600);
					font-size: var(--font-size-s);
					text-transform: uppercase;
					letter-spacing: 0.05em;
				}

				.sitemap-table td {
					padding: var(--space-s);
					border-bottom: 2px solid var(--color-neutral-50);
					vertical-align: center;
				}

				.url-link {
					color: var(--color-base);
					text-decoration: none;
					font-weight: 400;
					word-break: break-all;
				}

				.url-link:hover,
				.url-link:focus-visible {
					text-decoration: underline;
					color: var(--color-blue);
				}

				.priority {
					font-weight: var(--font-weight-bold);
					padding: var(--space-xs) var(--space-s);
					border-radius: var(--border-radius-s);
					font-size: var(--font-size-s);
				}

				.priority-high {
					background: var(--color-accent);
					color: var(--color-accent-invert);
				}

				.priority-medium {
					background: var(--color-lilac);
					color: var(--color-neutral-50);
				}

				.priority-low {
					background: var(--color-neutral-200);
					color: var(--color-neutral-700);
				}

				.changefreq {
					background: var(--color-green);
					color: var(--color-neutral-50);
					padding: 0.25rem 0.5rem;
					border-radius: 4px;
					font-size: var(--font-size-s);
					font-weight: 500;
				}

				.lastmod {
					font-size: var(--font-size-s);
					color: var(--color-neutral-500);
				}

				.lang-urls {
					margin-top: var(--space-s);
				}

				.lang-url {
					display: flex;
					align-items: center;
					gap: var(--space-xs);
					margin-bottom: var(--space-xs);
				}

				.lang-url:last-child {
					margin-bottom: 0;
				}

				.lang-code {
					font-size: var(--font-size-s);
					font-weight: var(--font-weight-bold);
					color: var(--color-neutral-500);
					text-transform: uppercase;
					min-width: 2rem;
				}

				.lang-link {
					font-size: var(--font-size-s);
					color: var(--color-neutral-400);
					text-decoration: none;
					word-break: break-all;
				}

				.lang-link:hover,
				.lang-link:focus-visible {
					text-decoration: underline;
					color: var(--color-blue);
				}

				.footer {
					padding: var(--space-m);
					text-align: center;
					color: var(--color-neutral-500);
					font-size: var(--font-size-s);
				}
			</style>
		</head>

		<body>
			<div class="container">
				<div class="header">
					<h1><?= $site->title() ?> - XML Sitemap</h1>
				</div>

				<table class="sitemap-table">
					<thead>
						<tr>
							<th>URL</th>
							<th>Last Modified</th>
							<th>Change Frequency</th>
							<th>Priority</th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="sitemap:urlset/sitemap:url">
							<xsl:sort select="sitemap:priority" order="descending" data-type="number" />
							<tr>
								<td>
									<a href="{sitemap:loc}" class="url-link" target="_blank">
										<xsl:value-of select="sitemap:loc" />
									</a>
									<xsl:if test="xhtml:link">
										<div class="lang-urls">
											<xsl:for-each select="xhtml:link[@rel='alternate']">
												<xsl:if test="@hreflang != 'x-default'">
													<div class="lang-url">
														<span class="lang-code"><xsl:value-of select="@hreflang" />:</span>
														<a href="{@href}" class="lang-link" target="_blank">
															<xsl:value-of select="@href" />
														</a>
													</div>
												</xsl:if>
											</xsl:for-each>
										</div>
									</xsl:if>
								</td>
								<td class="lastmod">
									<xsl:choose>
										<xsl:when test="contains(sitemap:lastmod, 'T')">
											<xsl:value-of select="substring(sitemap:lastmod, 1, 10)" />
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="sitemap:lastmod" />
										</xsl:otherwise>
									</xsl:choose>
								</td>
								<td>
									<span class="changefreq">
										<xsl:value-of select="sitemap:changefreq" />
									</span>
								</td>
								<td>
									<span>
										<xsl:attribute name="class">
											priority
											<xsl:choose>
												<xsl:when test="sitemap:priority &gt;= 0.8">priority-high</xsl:when>
												<xsl:when test="sitemap:priority &gt;= 0.5">priority-medium</xsl:when>
												<xsl:otherwise>priority-low</xsl:otherwise>
											</xsl:choose>
										</xsl:attribute>
										<xsl:value-of select="sitemap:priority" />
									</span>
								</td>
							</tr>
						</xsl:for-each>
					</tbody>
				</table>

				<div class="footer">
					<p><?= $site->url() ?> • <xsl:value-of select="count(sitemap:urlset/sitemap:url)" /> URLs</p>
				</div>
			</div>
		</body>

		</html>
	</xsl:template>
</xsl:stylesheet>
