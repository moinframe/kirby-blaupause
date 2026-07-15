import DebugService from "../services/DebugService"

class PrivacyVideo extends HTMLElement {
	private iframe: HTMLIFrameElement | null = null
	private video: HTMLVideoElement | null = null
	private provider: "Vimeo" | "Youtube" | "" = ""
	private src = ""
	private poster: string | null = null
	private message: string | null = null
	private buttonText: string | null = null

	connectedCallback(): void {
		this.init()
	}

	disconnectedCallback(): void {
		this.removeAutoplayListeners()
	}

	private async init(): Promise<void> {
		this.iframe = this.querySelector("iframe")
		this.video = this.querySelector("video")
		this.src = this.iframe?.dataset?.src ?? ""

		this.poster = this.getAttribute("poster")
		this.message = this.getAttribute("message")
		this.buttonText = this.getAttribute("button-text")

		if (this.video?.autoplay) {
			this.tryAutoplay()
		} else {
			this.setProvider()
			if (this.checkConsentGiven()) {
				this.allowPlayback()
			} else {
				await this.setOverlay()
			}
		}
	}

	/**
	 * Autoplay can be blocked until the user interacts with the page,
	 * so retry once on the first interaction
	 */
	private tryAutoplay(): void {
		this.video?.play().catch(() => {
			DebugService.log("Unable to play the video, user has not interacted yet.")
			document.addEventListener("pointerdown", this.retryAutoplay, { once: true })
			document.addEventListener("keydown", this.retryAutoplay, { once: true })
		})
	}

	private retryAutoplay = (): void => {
		this.removeAutoplayListeners()
		this.video?.play().catch(() => DebugService.log("Unable to play the video."))
	}

	private removeAutoplayListeners(): void {
		document.removeEventListener("pointerdown", this.retryAutoplay)
		document.removeEventListener("keydown", this.retryAutoplay)
	}

	private setProvider(): void {
		this.provider = this.src.includes("vimeo") ? "Vimeo" : "Youtube"
	}

	private getVideoId(): string {
		if (this.provider === "Vimeo") {
			const match = this.src.match(/vimeo\.com\/(?:video\/)?(\d+)/)
			if (!match?.[1]) throw new Error("Video id not found.")
			return match[1]
		}
		const match = this.src.match(/[-_\dA-Za-z]{11}(?=(&|\?|$))/)
		if (!match?.[0]) throw new Error("Video id not found.")
		return match[0]
	}

	/**
	 * Transform YouTube URL to YouTube-nocookie embed URL
	 */
	private transformYouTubeUrl(): void {
		if (this.provider === "Youtube") {
			const videoId = this.getVideoId()
			this.src = `https://www.youtube-nocookie.com/embed/${videoId}`
		}
	}

	private checkConsentGiven(): boolean {
		if (!this.provider) return false
		return Boolean(window.CookieConsent?.getUserConsent?.()?.includes(this.provider.toLowerCase()))
	}

	private async getVideoPoster(): Promise<string> {
		if (this.poster) return this.poster

		if (this.provider === "Vimeo") {
			try {
				const response = await fetch(`https://vimeo.com/api/oembed.json?url=${encodeURIComponent(this.src)}`)
				const result = (await response.json()) as { thumbnail_url?: string }
				return result.thumbnail_url ?? ""
			} catch {
				DebugService.error("Fetch of vimeo thumbnail failed")
				return ""
			}
		}
		if (this.provider === "Youtube") {
			return `https://img.youtube.com/vi/${this.getVideoId()}/maxresdefault.jpg`
		}
		return ""
	}

	private async setOverlay(): Promise<void> {
		this.poster = await this.getVideoPoster()
		if (!this.message) {
			this.message =
				"This video is hosted by {provider}. By playing this video you accept the privacy policy of {provider}."
		}
		if (!this.buttonText) {
			this.buttonText = "Allow playback"
		}
		this.render()
		this.setupEventListeners()
	}

	private render(): void {
		this.innerHTML = `
      <div class="privacy-overlay">
        <div class="privacy-overlay__background">
          ${this.poster ? `<img src="${this.poster}" alt="">` : ""}
        </div>
        <div class="privacy-overlay__content">
          <div class="privacy-overlay__content__inner">
            <p class="is-size-6-touch">
              ${this.message?.replace("{provider}", this.provider || "") || ""}
            </p>
            <button class="button mt-6">
              ${this.buttonText || ""}
            </button>
          </div>
        </div>
      </div>
    `
	}

	private setupEventListeners(): void {
		const button = this.querySelector("button")
		button?.addEventListener("click", this.allowPlayback.bind(this))
	}

	private allowPlayback(): void {
		DebugService.log("Playback allowed")
		this.transformYouTubeUrl()
		if (this.iframe) {
			this.iframe.src = this.src
		}
		this.innerHTML = ""
		this.appendChild(this.iframe || this.video || document.createElement("div"))
	}
}

customElements.define("privacy-video", PrivacyVideo)
