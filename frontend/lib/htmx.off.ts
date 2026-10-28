import htmx from "htmx.org"
import ScrollAnimations from "../services/ScrollAnimations"

export const install = () => {
	htmx.on("htmx:after:init", evt => {
		const target = evt.target as HTMLElement
		const newElements = target.querySelectorAll("[data-animation]")
		const animation = ScrollAnimations.getInstance()
		animation.addElements(newElements)
	})
}
