import DebugService from "./DebugService"

/**
 * Manages scroll-based animations for elements
 * @remarks Uses IntersectionObserver for performance reasons
 */
export default class ScrollAnimations {
	private static instance: ScrollAnimations | null
	private observer: IntersectionObserver
	private animatedElements: Set<Element>

	private constructor() {
		this.animatedElements = new Set()
		this.observer = new IntersectionObserver(this.handleIntersection, {
			rootMargin: "0px 0px -10% 0px",
			threshold: 0.1
		})

		this.init()
	}

	public static getInstance(): ScrollAnimations {
		if (!ScrollAnimations.instance) {
			ScrollAnimations.instance = new ScrollAnimations()
		}
		return ScrollAnimations.instance
	}

	/**
	 * Initializes animations for elements with data-animation attribute
	 */
	public init(): void {
		const elements = document.querySelectorAll("[data-animation]")
		for (const element of elements) {
			this.addElement(element)
		}
	}

	public addElement(element: Element): void {
		// Content in or above the current viewport stays visible (the view
		// transition covers page entry), only content below animates on scroll
		if (element.getBoundingClientRect().top < window.innerHeight) {
			element.classList.add("animated")
			DebugService.log("Element within viewport, skipping animation", element)
			return
		}

		if (element instanceof HTMLElement && element.dataset.delay) {
			element.style.setProperty("--delay", `${element.dataset.delay}s`)
		}
		element.classList.add("ready")
		this.observer.observe(element)
		this.animatedElements.add(element)
		DebugService.log("Adding element to ScrollAnimations", element)
	}

	public addElements(elements: Element[] | NodeListOf<Element>): void {
		for (const element of elements) {
			this.addElement(element)
		}
	}

	/**
	 * Cleans up animations and resets the instance
	 */
	public destroy(): void {
		this.observer.disconnect()
		for (const element of this.animatedElements) {
			element.classList.remove("animated", "ready")
		}
		this.animatedElements.clear()
		ScrollAnimations.instance = null
	}

	private handleIntersection = (entries: IntersectionObserverEntry[]): void => {
		for (const { isIntersecting, target } of entries) {
			if (isIntersecting) {
				target.classList.add("animated")
				this.observer.unobserve(target)
				this.animatedElements.delete(target)
				DebugService.log("Element animated", target)
			}
		}
	}
}
