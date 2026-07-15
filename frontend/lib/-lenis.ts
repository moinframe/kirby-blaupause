import Lenis from "lenis"

export const install = (): void => {
	new Lenis({
		autoRaf: true
	})
}
