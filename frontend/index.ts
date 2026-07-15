import "./index.css"
import DebugService from "./services/DebugService"
import "./assets/panel/favicon.svg"

/**
 * Installs modules from the './lib' directory
 * Rename a module to `<name>.off.ts` to disable it
 * @remarks Uses Vite's import.meta.glob with eager loading; install order follows the alphabetical glob order
 */
const installModules = (): void => {
	const modules = import.meta.glob<{ install?: () => void }>(["./lib/*.ts", "!./lib/*.off.ts"], { eager: true })

	for (const [path, module] of Object.entries(modules)) {
		const moduleName = path.split("/").pop()?.replace(".ts", "")
		DebugService.log(`Installing module: ${moduleName}`)
		module.install?.()
	}
}

installModules()
