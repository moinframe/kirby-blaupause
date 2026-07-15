/* biome-ignore-all lint/suspicious/noConsole: console output is this service's purpose */

/**
 * Module for handling debug logging
 * @remarks Uses console methods for output
 */
const DebugService = (() => {
	let debugMode = false

	return {
		/**
		 * Enables debug mode
		 */
		enableDebug(): void {
			debugMode = true
		},

		/**
		 * Disables debug mode
		 */
		disableDebug(): void {
			debugMode = false
		},

		/**
		 * Logs messages if debug mode is enabled
		 * @param message - The message to log
		 * @param optionalParams - Additional parameters to log
		 */
		log(message: string, ...optionalParams: unknown[]): void {
			if (debugMode) {
				console.log(message, ...optionalParams)
			}
		},

		/**
		 * Logs errors if debug mode is enabled
		 * @param message - The error message to log
		 * @param optionalParams - Additional parameters to log
		 */
		error(message: string, ...optionalParams: unknown[]): void {
			if (debugMode) {
				console.error(message, ...optionalParams)
			}
		}
	}
})()

export default DebugService
