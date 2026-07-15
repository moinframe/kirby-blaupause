module.exports = {
	content: [
		"./frontend/**/*.js",
		"./frontend/**/*.ts",
		"./frontend/**/*.svelte",
		"./backend/site/**/*.php",
		"./backend/site/**/**/*.php"
	],
	css: ["public/build/assets/!(panel*).css"],
	output: ["public/build/assets/"],
	// Default extractor splits on ":", which would purge cleacss responsive utilities like "is-hidden:m"
	defaultExtractor: content => content.match(/[A-Za-z0-9_:-]+(?<!:)/g) || [],
	fontFace: false, // Remove unused @font-face
	keyframes: true, // Remove unused @keyframes
	rejected: false, // Activate to see which css has been removed
	variables: false, // Remove unused css variables
	dynamicAttributes: [
		"data-layout",
		"data-theme",
		"data-style",
		"data-animation",
		"data-animation-prepare",
		"data-split",
		"data-alignment",
		"data-icon"
	],
	safelist: {
		standard: [/^block/, /^layout/, /^\[data-/, /^has-size-/, /^has-text-/],
		deep: [],
		greedy: [/view-transition/],
		keyframes: [],
		variables: []
	}
}
