# Orbit plugins

The Git-backed beta marketplace for Orbit's local Codex plugins.

## Install Orbit Browser

Requires macOS, the Codex desktop app with local plugin support, and an installed copy of Orbit. Tested with Orbit 0.5.1. Open Orbit at least once and create a workspace before connecting. No Orbit source checkout, Composer, Node.js or separate PHP installation is needed to use the plugin.

Run in Terminal with the Codex CLI installed:

```sh
codex plugin marketplace add captenmasin/orbit-plugins --ref main
codex plugin add orbit-browser@orbit-beta
```

Restart Codex, then find **Orbit Browser** under the **Orbit Beta** marketplace in Plugins. Open its app, or ask Codex to browse your Orbit projects. It runs in local chats on the same Mac as Orbit.

Orbit Browser includes tasks, scratchpad, documents, links, and saved dependency update/security findings. Dependency results come from the last checks saved in Orbit; run checks in Orbit to refresh them.

## Data and permissions

The server reads your local Orbit workspace. Connecting it lets Codex read those project sections. Selecting an item supplies context to the host; **Start new chat** opens a fresh conversation with a readable brief: the item's title, project, original Markdown and source IDs/revisions. Dependency briefs include saved versions, advisories, check dates and coverage warnings. This avoids adding messages to a busy conversation's queue. Chat data is handled by the host under your account settings.

The plugin exposes only read tools, opens SQLite in read-only mode, and never runs migrations, package updates, dependency checks, or workspace edits. It excludes Orbit's secret values and secret metadata from project content. It has no separate telemetry service. It writes only its own runtime caches under `~/Library/Caches/orbit-browser`.

The launcher finds Orbit at `/Applications/Orbit.app` or `~/Applications/Orbit.app` and uses PHP and Laravel already bundled with the app. It reads `~/Library/Application Support/orbit/database/database.sqlite` by default. It does not modify the installed app.

For a nonstandard app or workspace location, the launcher honors `ORBIT_APP_PATH` and `ORBIT_DATABASE_PATH` from the host process environment. Set them before launching Codex, using the full `.app` path and the path to an existing Orbit SQLite database. Standard installations need no configuration. Never edit files inside Codex's plugin cache; an upgrade replaces them.

If startup reports that Orbit is missing, install it in one of the standard locations. If it reports a missing workspace, open Orbit first. If it reports missing MCP Apps support, update Orbit.

## Update or uninstall

```sh
codex plugin marketplace upgrade orbit-beta
codex plugin add orbit-browser@orbit-beta
```

Restart Codex after updating. To remove the plugin and its marketplace:

```sh
codex plugin remove orbit-browser@orbit-beta
codex plugin marketplace remove orbit-beta
```

This is a beta distribution through Git. It is separate from OpenAI's public plugin directory.

## Development

Plugin code lives in `plugins/orbit-browser`; the marketplace catalog lives in `.agents/plugins/marketplace.json`. The package uses the Codex compatibility manifest for current desktop clients. UI assets and read-only tools ship here; the installed Orbit app supplies its existing models, read APIs, Markdown renderer, framework and PHP runtime.

There is no build step or dependency download. With Node.js, Python 3 and Orbit installed, run:

```sh
node --test tests/browser.test.mjs
python3 -m unittest discover -s tests -p 'test_*.py' -v
```

Integration tests use a temporary workspace and a relocated copy of the plugin. They check protocol startup, rendered UI, safe Markdown, project and dependency reads, validation, missing-install errors, write-tool rejection, and SQLite write protection across reconnects. They never use your real workspace. Browser tests inspect the actual `ui/message` request, including the new-chat destination, parent acknowledgement, rejection, timeout and duplicate-click protection. Acknowledgement confirms the host accepted the request; it does not prove that a model has read it.

Before publishing an update, run both suites, bump the plugin and server version, and change the UI resource URI when its HTML changes. Open a GitHub issue here for beta feedback; remove private project content before posting logs or screenshots.
