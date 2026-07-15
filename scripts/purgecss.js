#!/usr/bin/env node

import { exec } from "node:child_process"
import fs from "node:fs"
import path from "node:path"
import { glob } from "glob"
import purgeCSSConfig from "../purgecss.config.cjs"

const getFileSizeInKB = filePath => {
	const stats = fs.statSync(filePath)
	return stats.size / 1024
}

const formatSize = size => `${size.toFixed(2)} kB`

const logSizeDifferences = (originalSizes, files, maxPathLength, maxSizeLength) => {
	files.forEach(file => {
		const originalSize = originalSizes[file]
		const newSize = getFileSizeInKB(file)
		const fileName = path.basename(file)
		const filePath = file.replace(fileName, `\x1b[35m${fileName}\x1b[0m`)
		console.info(
			`${filePath.padEnd(maxPathLength)}${formatSize(originalSize).padStart(maxSizeLength + 2)} │ \x1b[1;32m🧹 ${formatSize(newSize)}\x1b[0m`
		)
	})
}

const getCSSFilesFromConfig = () => {
	const cssFiles = []
	let maxPathLength = 0
	let maxSizeLength = 0
	purgeCSSConfig.css.forEach(pattern => {
		const files = glob.sync(pattern)
		cssFiles.push(...files)
		files.forEach(file => {
			maxPathLength = Math.max(maxPathLength, file.length)
			const sizeLength = formatSize(getFileSizeInKB(file)).length
			maxSizeLength = Math.max(maxSizeLength, sizeLength)
		})
	})
	return { cssFiles, maxPathLength, maxSizeLength }
}

const { cssFiles, maxPathLength, maxSizeLength } = getCSSFilesFromConfig()

const originalSizes = {}
cssFiles.forEach(file => {
	originalSizes[file] = getFileSizeInKB(file)
})

exec("purgecss --config ./purgecss.config.cjs", (error, _stdout, stderr) => {
	if (error) {
		console.error(`\x1b[31mError running PurgeCSS: ${error.message}\x1b[0m`)
		return
	}
	if (stderr) {
		console.error(`\x1b[31mError: ${stderr}\x1b[0m`)
		return
	}

	logSizeDifferences(originalSizes, cssFiles, maxPathLength, maxSizeLength)
})
