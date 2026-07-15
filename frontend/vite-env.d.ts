/// <reference types="svelte" />
/// <reference types="vite/client" />

import type htmx from "htmx.org"

declare global {
	interface Window {
		htmx: htmx
		_paq?: unknown
		CookieConsent?: { getUserConsent?: () => string[] }
		plausible?: (event: string, options?: { props?: Record<string, string | number> }) => void
	}
}
