export const getURLParameter = (name: string) => {
	const urlParams = new URLSearchParams(window.location.search)
	return urlParams.get(name)
}

export default getURLParameter
