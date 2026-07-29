import DebugService from "../services/DebugService"

const OPEN_DELAY = 120
const CLOSE_DELAY = 250
const WIDE = "(min-width: 48rem)"

class MainMenu extends HTMLElement {
	private toggles: HTMLButtonElement[] = []
	private items: HTMLElement[] = []
	private backdrop: HTMLElement | null = null
	private timer: ReturnType<typeof setTimeout> | undefined
	private hover = matchMedia("(hover: hover) and (pointer: fine)")
	private wide = matchMedia(WIDE)

	connectedCallback(): void {
		this.toggles = Array.from(this.querySelectorAll<HTMLButtonElement>("button[aria-expanded]"))
		this.items = Array.from(this.querySelectorAll<HTMLElement>(".menubar__item"))
		this.backdrop = this.querySelector(".menubar__backdrop")

		this.addEventListener("click", this.onClick)
		this.addEventListener("keydown", this.onKeydown)
		this.addEventListener("mouseleave", this.onLeave)
		document.addEventListener("click", this.onOutside)
		document.addEventListener("focusin", this.onOutside)
		window.addEventListener("resize", this.onResize, { passive: true })

		for (const item of this.items) {
			item.addEventListener("mouseenter", this.onEnter)
		}

		this.restore()

		DebugService.log("Main menu initialized")
	}

	disconnectedCallback(): void {
		this.removeEventListener("click", this.onClick)
		this.removeEventListener("keydown", this.onKeydown)
		this.removeEventListener("mouseleave", this.onLeave)
		document.removeEventListener("click", this.onOutside)
		document.removeEventListener("focusin", this.onOutside)
		window.removeEventListener("resize", this.onResize)

		for (const item of this.items) {
			item.removeEventListener("mouseenter", this.onEnter)
		}

		clearTimeout(this.timer)

		DebugService.log("Main menu destroyed")
	}

	private onClick = (event: MouseEvent): void => {
		const toggle = (event.target as HTMLElement).closest<HTMLButtonElement>("button[aria-expanded]")
		if (!toggle || !this.toggles.includes(toggle)) return

		clearTimeout(this.timer)

		if (this.isExpanded(toggle)) {
			this.collapse(toggle)
		} else {
			this.expand(toggle)
		}
	}

	private onKeydown = (event: KeyboardEvent): void => {
		if (event.key !== "Escape") return

		const open = this.toggles.filter(toggle => this.isExpanded(toggle)).reverse()
		const innermost = open[0]
		if (!innermost) return

		// The disclosure around the focus, otherwise the one opened last
		const target = event.target as Node
		const toggle = open.find(t => t === target || this.panel(t)?.contains(target)) ?? innermost

		this.collapse(toggle)
		toggle.focus()
	}

	private onEnter = (event: MouseEvent): void => {
		if (!this.hover.matches || !this.wide.matches) return

		const item = event.currentTarget as HTMLElement
		const toggle = item.querySelector<HTMLButtonElement>(".menubar__disclosure")

		clearTimeout(this.timer)

		// Moving on to the next flyout should not wait again
		const delay = this.current() ? 0 : OPEN_DELAY

		this.timer = setTimeout(() => {
			if (toggle) {
				this.expand(toggle)
			} else {
				this.collapseAll()
			}
		}, delay)
	}

	private onLeave = (): void => {
		if (!this.hover.matches || !this.wide.matches) return

		clearTimeout(this.timer)
		this.timer = setTimeout(() => {
			// Someone navigating by keyboard keeps their flyout
			if (this.contains(document.activeElement)) return
			this.collapseAll()
		}, CLOSE_DELAY)
	}

	private onOutside = (event: Event): void => {
		if (this.contains(event.target as Node)) return
		this.collapseAll()
	}

	private onResize = (): void => {
		this.collapseAll()
		this.restore()
	}

	private restore(): void {
		if (this.wide.matches) return

		const section = this.toggles.find(toggle => toggle.getAttribute("aria-current") === "true")
		section?.setAttribute("aria-expanded", "true")
	}

	private expand(toggle: HTMLButtonElement): void {
		const panel = this.panel(toggle)

		for (const other of this.toggles) {
			if (other === toggle) continue
			if (this.panel(other)?.contains(toggle)) continue
			if (panel?.contains(other)) continue

			other.setAttribute("aria-expanded", "false")
		}

		toggle.setAttribute("aria-expanded", "true")
		this.sync()
	}

	/** Closes a disclosure and everything inside it */
	private collapse(toggle: HTMLButtonElement): void {
		const panel = this.panel(toggle)

		for (const other of this.toggles) {
			if (panel?.contains(other)) other.setAttribute("aria-expanded", "false")
		}

		toggle.setAttribute("aria-expanded", "false")
		this.sync()
	}

	private collapseAll(): void {
		for (const toggle of this.toggles) {
			toggle.setAttribute("aria-expanded", "false")
		}

		this.sync()
	}

	/** Moves the backdrop to the open flyout, or fades it out */
	private sync(): void {
		const backdrop = this.backdrop
		if (!backdrop) return

		// Below `m` there are no flyouts, the submenus are part of the drawer
		const toggle = this.wide.matches ? this.current() : null
		const panel = toggle && this.panel(toggle)

		if (!panel) {
			delete backdrop.dataset.open
			return
		}

		const menu = this.getBoundingClientRect()
		const flyout = panel.getBoundingClientRect()
		const jump = backdrop.dataset.open === undefined

		if (jump) backdrop.dataset.instant = ""

		backdrop.style.setProperty("--menubar-backdrop-x", `${Math.round(flyout.left - menu.left)}px`)
		backdrop.style.setProperty("--menubar-backdrop-y", `${Math.round(flyout.top - menu.top)}px`)
		backdrop.style.setProperty("--menubar-backdrop-w", `${Math.round(flyout.width)}px`)
		backdrop.style.setProperty("--menubar-backdrop-h", `${Math.round(flyout.height)}px`)

		if (jump) {
			backdrop.getBoundingClientRect()
			delete backdrop.dataset.instant
		}

		backdrop.dataset.open = ""
	}

	private current(): HTMLButtonElement | null {
		return this.toggles.find(t => t.classList.contains("menubar__disclosure") && this.isExpanded(t)) ?? null
	}

	private isExpanded(toggle: HTMLButtonElement): boolean {
		return toggle.getAttribute("aria-expanded") === "true"
	}

	private panel(toggle: HTMLButtonElement): HTMLElement | null {
		const id = toggle.getAttribute("aria-controls")
		return id ? this.querySelector<HTMLElement>(`#${CSS.escape(id)}`) : null
	}
}

customElements.define("main-menu", MainMenu)
