import "./index.css";
import DebugService from "./services/DebugService";
import "./assets/panel/favicon.svg";

/**
 * Installs modules from the './lib' directory
 * @remarks Uses Vite's import.meta.glob with eager loading
 */
const installModules = (): void => {
	const modules = import.meta.glob<{ install?: () => void }>("./lib/*.ts", { eager: true });

	for (const [path, module] of Object.entries(modules)) {
		const moduleName = path.split('/').pop()?.replace('.ts', '');
		DebugService.log(`Installing module: ${moduleName}`);
		module.install?.();
	}
};

installModules();
