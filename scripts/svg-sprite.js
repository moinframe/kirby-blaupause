#!/usr/bin/env node

import svgstore from "svgstore"
import fs from "node:fs/promises"
import path from "node:path"
import { glob } from "glob"
import { optimize } from "svgo"
import yargs from "yargs"

const argv = yargs(process.argv.slice(2))
	.usage("Usage: $0 -i [string] -o [string] -p [string]")
	.demandOption(["i", "o"])
	.describe("i", "Input path to folder containing .svg, e.g. src/svg/")
	.describe("o", "Output path of sprite, e.g. public/sprite.svg")
	.describe("p", "Project abbreviation, e.g. project")
	.default("p", "project")
	.help().argv

async function createSvgSprite(inputPath, outputPath, projectAbbreviation) {
	try {
		const files = await glob.sync(path.join(process.cwd(), inputPath, "*.svg"))
		const sprite = svgstore({
			svgAttrs: {
				style: "display: none;",
				"aria-hidden": "true",
				xmlns: "http://www.w3.org/2000/svg"
			},
			copyAttrs: ["width", "height"]
		})

		const panelIcons = {}
		console.info("\x1b[35mTransforming svgs to sprite\x1b[0m")
		for (const filePath of files) {
			const name = path.basename(filePath, ".svg")
			console.info(`${name}.svg`)

			let svgFile = await fs.readFile(filePath, "utf8")
			svgFile = svgFile.replace(/width="(\d*)"/, 'width="100%"').replace(/height="(\d*)"/, 'height="100%"')

			const svgoResult = optimize(svgFile, getOptimizationOptions(name))
			const optimizedSvg = svgoResult.data.replace(
				/(<svg [a-z -"=\/\. \d:]*>)/g,
				`$1<title id="${name}-icon">${name}</title>`
			)

			sprite.add(`symbol-${name}`, optimizedSvg, {
				symbolAttrs: {
					"aria-labelledby": `${name}-icon`,
					role: "img"
				}
			})

			panelIcons[name] = optimizedSvg
		}

		await createCmsPluginFile(panelIcons, projectAbbreviation)
		await fs.writeFile(path.join(process.cwd(), outputPath), sprite.toString({ inline: true }))

		console.info("")
		console.info("\x1b[1;32mAll done!\x1b[0m")
	} catch (error) {
		console.error(`\x1b[31mError creating SVG sprite: ${error.message}\x1b[0m`)
	}
}
async function createCmsPluginFile(panelIcons, projectAbbreviation) {
	const panelIconsFile = `panel.plugin('${projectAbbreviation}/icons', {
    icons: ${JSON.stringify(panelIcons, null, 2)}
  });`

	const directoryPath = path.join(process.cwd(), `backend/site/plugins/${projectAbbreviation}-icons`)

	// Ensure the directory exists
	await fs.mkdir(directoryPath, { recursive: true })

	await fs.writeFile(path.join(directoryPath, "index.js"), panelIconsFile)
	console.info("")
	console.info(`\x1b[35mAdded icons to panel using plugin "${projectAbbreviation}-icons"\x1b[0m`)
}

function getOptimizationOptions() {
	return {
		plugins: [
			{
				name: "preset-default",
				params: {
					overrides: {}
				}
			},
			{
				name: "addCurrentColor",
				fn: () => ({
					element: {
						enter: node => {
							if (node.attributes.fill) {
								node.attributes.fill = "currentColor"
							}
							if (node.attributes.stroke) {
								node.attributes.stroke = "currentColor"
							}
						}
					}
				})
			}
		]
	}
}

createSvgSprite(argv.i, argv.o, argv.p)
