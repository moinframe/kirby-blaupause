import ScrollAnimations from "../services/ScrollAnimations"
import SplitWords from "../services/SplitWords"

export const install = () => {
	if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return

	new SplitWords()
	ScrollAnimations.getInstance()
}
