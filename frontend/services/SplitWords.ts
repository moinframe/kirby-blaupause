import DebugService from "./DebugService"

/**
 * Splits text into word spans for staggered animations
 * @remarks Works with elements having data-animation-prepare="split-words" attribute.
 * Screen readers get the original text via a visually hidden copy,
 * the word spans are hidden from the accessibility tree.
 */
export default class SplitWords {
	private elements: NodeListOf<HTMLElement>

	constructor(customSelector?: string) {
		const selector = customSelector || '[data-animation-prepare="split-words"]'
		this.elements = document.querySelectorAll(selector)
		this.init()
	}

	private init(): void {
		for (const element of this.elements) {
			this.splitElement(element)
		}
		DebugService.log(`Split text animation initialized for ${this.elements.length} elements`)
	}

	private splitElement(element: HTMLElement): void {
		if (element.dataset.split === "done") return

		const originalText = (element.textContent || "").trim()
		const words = originalText.split(/\s+/)
		const animationDelay = Number.parseFloat(element.getAttribute("data-delay") || "0.1")

		const screenReaderText = document.createElement("span")
		screenReaderText.className = "visually-hidden"
		screenReaderText.textContent = originalText

		const wordContainer = document.createElement("span")
		wordContainer.setAttribute("aria-hidden", "true")

		words.forEach((word, index) => {
			const span = document.createElement("span")
			span.className = "word"
			span.textContent = word
			span.style.setProperty("--delay", `${index * animationDelay}s`)
			wordContainer.appendChild(span)

			if (index < words.length - 1) {
				wordContainer.appendChild(document.createTextNode(" "))
			}
		})

		element.replaceChildren(screenReaderText, wordContainer)
		element.dataset.split = "done"

		DebugService.log("Split text prepared for element", element)
	}
}
