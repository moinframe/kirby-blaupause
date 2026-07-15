import { Core, type Transition, type Renderer } from "@unseenco/taxi"
import DebugService from "../services/DebugService"

/**
 * Creates a record from glob imports
 * @param imports - The result of import.meta.glob with eager: true
 * @param keyPrefix - The prefix to remove from the key
 * @returns Record of loaded modules
 */
const createModuleRecord = <T>(imports: Record<string, { default: T }>, keyPrefix: string): Record<string, T> => {
	return Object.fromEntries(
		Object.entries(imports).map(([key, module]) => [key.slice(keyPrefix.length, -3), module.default])
	)
}

DebugService.log("Initializing Taxi")

const rendererImports = import.meta.glob<{ default: typeof Renderer }>("../renderers/*.ts", { eager: true })
const transitionImports = import.meta.glob<{ default: typeof Transition }>("../transitions/*.ts", { eager: true })

window.Taxi = new Core({
	renderers: createModuleRecord(rendererImports, "../renderers/"),
	transitions: createModuleRecord(transitionImports, "../transitions/"),
	allowInterruption: true,
	removeOldContent: false
})

DebugService.log("Taxi initialized")
