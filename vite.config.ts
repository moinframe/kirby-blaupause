import { defineConfig, loadEnv } from 'vite'
import { resolve } from 'node:path'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import laravel from 'laravel-vite-plugin';
import { browserslistToTargets } from 'lightningcss';
import browserslist from "browserslist"
import { homedir } from 'node:os'
import { globSync } from 'glob';
import fs from 'node:fs';


// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
	const env = loadEnv(mode, process.cwd(), "APP");
	return {
		build: {
			cssMinify: 'lightningcss',
			minify: 'terser',
			terserOptions: {
				keep_classnames: true
			}
		},
		css: {
			transformer: 'lightningcss',
			lightningcss: {
				drafts: {
					customMedia: true
				},
				targets: browserslistToTargets(browserslist(["last 2 versions", ">= 0.4%", "not dead", "Firefox ESR", "not op_mini all", "not and_uc > 0"]))
			}
		},
		plugins: [
			svelte(),
			laravel({
				input: [
					'frontend/index.ts',
					'frontend/panel.css',
					'frontend/panel.ts',
					...globSync('frontend/styles/blocks/**/!(_*).css'), // Add all CSS files in blocks folder, excluding those starting with '_'
					...globSync('frontend/styles/snippets/**/!(_*).css'),
					...globSync('frontend/blocks/**/!(_*).ts'), // Add all ts files in blocks folder, excluding those starting with '_'
					...globSync('frontend/snippets/**/!(_*).ts'),
				],
				refresh: [
					'backend/site/snippets/**',
					'backend/site/templates/**'
				],
				detectTls: env.APP_URL?.replace(/https?:\/\//, ""),
			})
		],
		resolve: {
			alias: {
				'@styles': resolve(__dirname, 'frontend/styles/'),
				'@': resolve(__dirname, 'frontend/')
			}
		},
		server: setServerConfig()
	}
})


function setServerConfig() {
	const host = "vite.test";
	const baseConfig = {
		open: false,
		cors: true,
		host,
		hmr: { host },
		port: 3000,
		strictPort: true,
	}

	const keyPath = resolve(homedir(), `.config/valet/Certificates/${host}.key`)
	const certificatePath = resolve(homedir(), `.config/valet/Certificates/${host}.crt`)

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
			cert: fs.readFileSync(certificatePath),
		},
	}
}
