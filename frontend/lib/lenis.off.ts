import Lenis from "lenis"
import "lenis/dist/lenis.css"

export const install = (): void => {
	if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return

	new Lenis({
		autoRaf: true
	})
}
