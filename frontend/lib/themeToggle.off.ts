const STORAGE_KEY = "theme"

/**
 * Toggles between light and dark via data-theme on <html>
 * @remarks Binds to elements with a data-theme-toggle attribute. The stored
 * preference is applied after first paint — sites that enable this module
 * should move the restore into a small inline script in the head to avoid
 * a flash of the default theme.
 */
const isDark = (): boolean => {
	const theme = document.documentElement.dataset.theme
	if (theme === "dark" || theme === "light") return theme === "dark"
	return window.matchMedia("(prefers-color-scheme: dark)").matches
}

export const install = (): void => {
	const stored = localStorage.getItem(STORAGE_KEY)
	if (stored === "dark" || stored === "light") {
		document.documentElement.dataset.theme = stored
	}

	for (const toggle of document.querySelectorAll("[data-theme-toggle]")) {
		toggle.addEventListener("click", () => {
			const next = isDark() ? "light" : "dark"
			document.documentElement.dataset.theme = next
			localStorage.setItem(STORAGE_KEY, next)
		})
	}
}
