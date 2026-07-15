import htmx from "htmx.org"
import ScrollAnimations from "../services/ScrollAnimations"

export const install = () => {
	window.htmx = htmx
	htmx.on("htmx:load", (evt) => {
		const target = evt.target as HTMLElement
		const newElements = target.querySelectorAll("[data-animation]")
		const animation = ScrollAnimations.getInstance()
		animation.addElements(newElements)
	})
}
