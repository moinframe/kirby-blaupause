#!/usr/bin/env bash
#
# test-template.sh — generate a fresh instance of this template into a local
# sandbox (like GitHub's "Use this template") and install it from scratch, so
# you can test template generation end to end: composer install, pnpm install,
# pnpm build — and optionally the `kirby init` command.
#
# Nothing is committed to git and nothing is deleted: each run creates a fresh
# unique sandbox under .init-sandbox/ (gitignored) for you to inspect or remove.
#
# Usage:
#   scripts/test-template.sh            # generate + full install
#   scripts/test-template.sh --init     # ... then run `kirby init`
#   scripts/test-template.sh --dir DIR  # use a custom sandbox path
#   scripts/test-template.sh --help
#
set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SANDBOX=""
INIT=0

while [ $# -gt 0 ]; do
	case "$1" in
		--init) INIT=1 ;;
		--dir) shift; SANDBOX="${1:?--dir needs a path}" ;;
		-h|--help) sed -n '2,15p' "$0" | sed 's/^#\{0,1\} \{0,1\}//'; exit 0 ;;
		*) echo "Unknown option: $1 (try --help)" >&2; exit 1 ;;
	esac
	shift
done

command -v composer >/dev/null 2>&1 || { echo "composer not found on PATH." >&2; exit 1; }
command -v pnpm     >/dev/null 2>&1 || { echo "pnpm not found on PATH." >&2; exit 1; }

# Default to a fresh, unique sandbox under .init-sandbox/ so no run ever
# overwrites or deletes another. Old runs pile up there — remove them yourself.
if [ -z "$SANDBOX" ]; then
	mkdir -p "$REPO/.init-sandbox"
	SANDBOX="$(mktemp -d "$REPO/.init-sandbox/run-$(date +%Y%m%d-%H%M%S)-XXXXXX")"
fi

if [ "$SANDBOX" = "$REPO" ]; then
	echo "Refusing to use the repo root as the sandbox." >&2
	exit 1
fi

# Never delete anything: require a fresh/empty target and only create it.
if [ -e "$SANDBOX" ] && [ -n "$(ls -A "$SANDBOX" 2>/dev/null)" ]; then
	echo "Sandbox path already exists and is not empty: $SANDBOX" >&2
	echo "Remove it yourself or pass --dir with a fresh path." >&2
	exit 1
fi

echo "▸ Creating sandbox at $SANDBOX"
mkdir -p "$SANDBOX"

# 1. Copy tracked files from the working tree (mirrors "Use this template").
#    `git ls-files` only lists tracked paths; cpio copies their current content.
#    Nothing is committed and no git objects are created.
echo "▸ Copying tracked files (working tree)"
( cd "$REPO" && git ls-files -z | cpio -pdm0 --quiet "$SANDBOX" )

# 2. Runtime dirs + a .env (Kirby reads it on boot).
mkdir -p "$SANDBOX"/storage/{content,accounts,sessions,logs} "$SANDBOX"/public/{media,cache}
[ -f "$SANDBOX/.env" ] || cp "$SANDBOX/.env.example" "$SANDBOX/.env"

# 3. Full install — exactly what a freshly generated project runs.
echo "▸ composer install"
( cd "$SANDBOX" && composer install --no-interaction )

echo "▸ pnpm install + build"
export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
# shellcheck disable=SC1091
[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"
(
	cd "$SANDBOX"
	# Match the project's node version if nvm is available (see .nvmrc).
	if command -v nvm >/dev/null 2>&1; then nvm install >/dev/null || true; fi
	pnpm install
	pnpm build
)

echo "✓ Template generated and installed at $SANDBOX"

if [ "$INIT" -eq 1 ]; then
	KIRBY="$SANDBOX/backend/vendor/bin/kirby"
	[ -x "$KIRBY" ] || KIRBY="$(command -v kirby || true)"
	[ -n "$KIRBY" ] || { echo "Kirby CLI not found in the sandbox." >&2; exit 1; }
	echo "▸ Running \`kirby init\`"
	echo "----------------------------------------------------------------"
	( cd "$SANDBOX" && "$KIRBY" init )
else
	cat <<EOF

Next steps:
  cd "$SANDBOX"
  backend/vendor/bin/kirby init     # test the init command
  # ...or serve it, inspect the generated files, etc.

Re-run this script any time for a clean sandbox.
EOF
fi
