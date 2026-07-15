import fs from "node:fs"
import { homedir } from "node:os"
import { resolve } from "node:path"
import { svelte } from "@sveltejs/vite-plugin-svelte"
import browserslist from "browserslist"
import { globSync } from "glob"
import laravel from "laravel-vite-plugin"
import { browserslistToTargets } from "lightningcss"
import { defineConfig, loadEnv } from "vite"

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
	const env = loadEnv(mode, process.cwd(), "APP")
	return {
		build: {
			cssMinify: "lightningcss",
			rolldownOptions: {
				output: {
					keepNames: true
				}
			}
		},
		css: {
			transformer: "lightningcss",
			lightningcss: {
				drafts: {
					customMedia: true
				},
				targets: browserslistToTargets(browserslist())
			}
		},
		plugins: [
			svelte(),
			laravel({
				input: [
					"frontend/index.ts",
					"frontend/panel.css",
					"frontend/panel.ts",
					...globSync("frontend/styles/blocks/**/!(_*).css"), // Add all CSS files in blocks folder, excluding those starting with '_'
					...globSync("frontend/styles/snippets/**/!(_*).css"),
					...globSync("frontend/blocks/**/!(_*).ts"), // Add all ts files in blocks folder, excluding those starting with '_'
					...globSync("frontend/snippets/**/!(_*).ts")
				],
				refresh: ["backend/site/snippets/**", "backend/site/templates/**"],
				detectTls: env.APP_URL?.replace(/https?:\/\//, "")
			})
		],
		resolve: {
			alias: {
				"@styles": resolve(__dirname, "frontend/styles/"),
				"@": resolve(__dirname, "frontend/")
			}
		},
		server: setServerConfig()
	}
})

function setServerConfig() {
	const host = "vite.test"
	const baseConfig = {
		open: false,
		cors: true,
		host,
		hmr: { host },
		port: 3000,
		strictPort: true
	}

	const keyPath = resolve(homedir(), `Library/Application Support/Herd/config/valet/Certificates/${host}.key`)
	const certificatePath = resolve(homedir(), `Library/Application Support/Herd/config/valet/Certificates/${host}.crt`)

	if (!fs.existsSync(keyPath)) {
		return baseConfig
	}

	if (!fs.existsSync(certificatePath)) {
		return baseConfig
	}

	return {
		...baseConfig,
		https: {
			key: fs.readFileSync(keyPath),
			cert: fs.readFileSync(certificatePath)
		}
	}
}
