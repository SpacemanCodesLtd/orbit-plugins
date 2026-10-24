# Orbit Browser

Browse local Orbit projects in Codex and open a new chat with a readable brief of a task, document, scratchpad, saved link or dependency finding.

Requires macOS, a local Codex chat with plugin support, and an installed copy of Orbit with MCP Apps support (tested with Orbit 0.5.1). Open Orbit and create a workspace first. The launcher uses Orbit's bundled PHP runtime; there is no separate runtime download.

Install through the OpenAI Plugin Directory when available, or follow the [Git marketplace instructions](https://github.com/captenmasin/orbit-plugins#install-orbit-browser). This ZIP contains the plugin itself; the submission materials and demo workspace are separate files.

Connecting lets Codex request supported local project content. Browsing loads content through the host; selecting an item supplies context, and **Start new chat** sends its brief into a fresh conversation. Host data handling follows your account settings. Do not store credentials or sensitive material in ordinary notes intended for sharing.

The plugin is read-only and excludes Orbit secret values and secret metadata. It does not modify the installed app, change the workspace, run dependency checks or install updates. It writes runtime caches under `~/Library/Caches/orbit-browser`. Uninstalling the plugin does not delete your Orbit workspace or necessarily remove those caches.

For a nonstandard installation, set `ORBIT_APP_PATH` to the installed `.app` and `ORBIT_DATABASE_PATH` to an existing Orbit SQLite database in the host environment. Standard installations require neither. Errors on startup explain missing app or workspace requirements.

Support: [orbitdesktop.com/contact](https://orbitdesktop.com/contact/) or support@orbitdesktop.com. Privacy: [orbitdesktop.com/privacy](https://orbitdesktop.com/privacy/). Source and issue reports: [captenmasin/orbit-plugins](https://github.com/captenmasin/orbit-plugins).
