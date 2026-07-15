import { Renderer } from "@unseenco/taxi"
import { lazyLoad } from "unlazy"
import ScrollAnimations from "../services/ScrollAnimations"
import SplitWords from "../services/SplitWords"

export default class extends Renderer {
	initialLoad() {
		lazyLoad()
		ScrollAnimations.getInstance()
		new SplitWords()
	}

	onEnter(): void {
		const page = this.page as Document

		// Update template dataset
		const newTemplate = page.body.dataset.template
		document.body.dataset.template = newTemplate

		// Update canonical URL
		const canonical = page.querySelector('link[rel="canonical"]') as HTMLLinkElement | null
		const currentCanonical = document.querySelector('link[rel="canonical"]') as HTMLLinkElement | null
		if (canonical && currentCanonical) {
			currentCanonical.href = canonical.href
		}

	}

	onLeaveCompleted(): void {
		this.remove()
	}

	onEnterCompleted() {
		lazyLoad()
		new SplitWords()
		ScrollAnimations.reinitialize()
	}
}
