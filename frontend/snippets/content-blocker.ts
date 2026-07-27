import DebugService from "../services/DebugService"

const STORAGE_PREFIX = "content-blocker:"
const ACCEPTED_EVENT = "content-blocker:accepted"
const CONSENT_EVENT = "consent:change"

/**
 * Custom element for blocking third party embeds until the visitor consents.
 * The overlay markup is rendered by the `content-blocker` snippet, the embed
 * itself sits in an inert `<template>` so nothing is requested beforehand.
 * @customElement content-blocker
 */
class ContentBlocker extends HTMLElement {
	private provider = ""
	private remember = true
	private template: HTMLTemplateElement | null = null
	private overlay: HTMLElement | null = null
	private button: HTMLButtonElement | null = null
	private unblocked = false

	connectedCallback(): void {
		this.provider = this.getAttribute("provider") ?? ""
		this.remember = this.getAttribute("remember") !== "none"
		this.template = this.querySelector("template[data-embed]")
		this.overlay = this.querySelector(".content-blocker__overlay")
		this.button = this.querySelector("[data-accept]")

		this.button?.addEventListener("click", this.onAccept)
		document.addEventListener(ACCEPTED_EVENT, this.onAccepted)
		document.addEventListener(CONSENT_EVENT, this.onConsentChange)

		if (this.hasConsent()) {
			this.unblock()
		}
	}

	disconnectedCallback(): void {
		this.button?.removeEventListener("click", this.onAccept)
		document.removeEventListener(ACCEPTED_EVENT, this.onAccepted)
		document.removeEventListener(CONSENT_EVENT, this.onConsentChange)
	}

	/**
	 * Storage can throw in private mode or when cookies are disabled
	 */
	private get storage(): Storage | null {
		try {
			return window.sessionStorage
		} catch {
			DebugService.log("sessionStorage is unavailable")
			return null
		}
	}

	/**
	 * Consent from a previous accept in this session takes precedence,
	 * an optional consent manager is asked second
	 */
	private hasConsent(): boolean {
		if (!this.provider) return false
		if (this.storage?.getItem(STORAGE_PREFIX + this.provider) === "granted") return true
		return Boolean(window.CookieConsent?.getUserConsent?.()?.includes(this.provider))
	}

	/**
	 * Announce the decision instead of unblocking directly, so every embed
	 * of the same provider on the page reacts through the same code path
	 */
	private onAccept = (): void => {
		if (this.remember && this.provider) {
			this.storage?.setItem(STORAGE_PREFIX + this.provider, "granted")
		}
		document.dispatchEvent(new CustomEvent(ACCEPTED_EVENT, { detail: { provider: this.provider } }))
	}

	private onAccepted = (event: Event): void => {
		const { provider } = (event as CustomEvent<{ provider?: string }>).detail ?? {}
		if (provider === this.provider) {
			this.unblock()
		}
	}

	/**
	 * Lets a cookie banner unblock embeds without a reload
	 */
	private onConsentChange = (): void => {
		if (this.hasConsent()) {
			this.unblock()
		}
	}

	private unblock(): void {
		if (this.unblocked || !this.template) return
		this.unblocked = true

		// Only follow the user into the embed when they actually clicked,
		// never when stored consent unblocks it on page load
		const takeFocus = this.contains(document.activeElement)

		this.append(this.template.content.cloneNode(true))
		this.overlay?.remove()
		this.template.remove()
		this.template = null
		this.dataset.state = "unblocked"

		const frame = this.querySelector("iframe")
		if (frame) {
			this.setAttribute("aria-busy", "true")
			frame.addEventListener("load", () => this.removeAttribute("aria-busy"), { once: true })
			if (takeFocus) frame.focus({ preventScroll: true })
		}

		DebugService.log(`Content blocker unblocked: ${this.provider}`)
	}
}

customElements.define("content-blocker", ContentBlocker)
