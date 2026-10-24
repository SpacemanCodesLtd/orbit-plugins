# Orbit Browser

Browse local Orbit projects in Codex and open a new chat with a readable brief of a task, document, scratchpad, saved link or dependency finding.

Requires macOS, a local Codex chat with plugin support, and an installed copy of Orbit with MCP Apps support (tested with Orbit 0.5.1). Open Orbit and create a workspace first. The launcher uses Orbit's bundled PHP runtime; there is no separate runtime download.

Follow the [Git marketplace instructions](https://github.com/captenmasin/orbit-plugins#install-orbit-browser) to install or update the beta. OpenAI public directory review is pending eligibility confirmation. This ZIP contains the plugin itself; the submission materials and demo workspace are separate files.

Select a project and an item, then click **Start new chat** yourself. Automated clicks cannot complete this handoff in the tested Codex setup; the plugin asks for a manual click without sending the brief or waiting for a timeout.

## Data and privacy

Connecting lets Codex request supported local project content. Browsing loads content through the host; selecting an item supplies context, and **Start new chat** sends its brief into a fresh conversation. Host data handling follows your account settings. Do not store credentials or sensitive material in ordinary notes intended for sharing.

Supported content includes project names, descriptions and status; tasks and board columns; scratchpad notes; documents; saved links; IDs and revisions; and saved dependency versions, advisories, dates and coverage warnings. Inspection results can include linked local folder paths and availability. Local execution does not mean your AI host processes this content only on your Mac.

The plugin is read-only and excludes Orbit secret values and secret metadata. It does not modify the installed app, change the workspace, run dependency checks or install updates. It writes runtime caches under `~/Library/Caches/orbit-browser`. Uninstalling the plugin does not delete your Orbit workspace or necessarily remove those caches.

SpacemanCodes LTD does not receive project content merely because the plugin reads it, and the plugin has no separate telemetry service. To remove its caches, stop the plugin before removing `~/Library/Caches/orbit-browser`. Your original workspace remains under Orbit's controls; copies shared with your AI host follow that host's retention and deletion controls.

## Setup and support

For a nonstandard installation, set `ORBIT_APP_PATH` to the installed `.app` and `ORBIT_DATABASE_PATH` to an existing Orbit SQLite database in the host environment. Standard installations require neither. Errors on startup explain missing app or workspace requirements.

Published by SpacemanCodes LTD. Support: [orbitdesktop.com/contact](https://orbitdesktop.com/contact/) or support@orbitdesktop.com. Website and support privacy: [orbitdesktop.com/privacy](https://orbitdesktop.com/privacy/). Source and issue reports: [captenmasin/orbit-plugins](https://github.com/captenmasin/orbit-plugins). Remove private project content before posting an issue.
