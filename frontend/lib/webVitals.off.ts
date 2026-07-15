import { type Metric, onCLS, onINP, onLCP } from "web-vitals"

/**
 * Reports Core Web Vitals to Plausible as custom events
 * @remarks Requires the "Web Vitals" custom event goal in Plausible
 */
const report = (metric: Metric): void => {
	window.plausible?.("Web Vitals", {
		props: {
			metric: metric.name,
			value: Math.round(metric.name === "CLS" ? metric.value * 1000 : metric.value),
			rating: metric.rating,
			path: window.location.pathname
		}
	})
}

export const install = (): void => {
	onCLS(report)
	onINP(report)
	onLCP(report)
}
