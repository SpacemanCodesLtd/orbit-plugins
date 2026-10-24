# Orbit Browser privacy notice — draft for approval

**Unpublished draft.** This text describes the implementation in this release. Confirm the publisher details, support retention period, processing basis and any applicable rights/contact procedures before publishing. It can be added as an Orbit Browser section on the existing privacy page or published as a dedicated notice linked from the listing.

## Publisher and purpose

SpacemanCodes LTD provides Orbit Browser to let you read local Orbit project context in Codex. Contact support@orbitdesktop.com or https://orbitdesktop.com/contact/ with privacy questions.

## What the plugin reads

Depending on the tools requested and the project you browse, the plugin reads project names and descriptions, status, identifiers and revision numbers, task columns and descriptions, scratchpad notes, document titles and bodies, saved link details, and saved dependency versions, advisory information, check dates and coverage warnings. Workspace and inspection responses can include linked local folder paths and availability. Content you saved in those fields can include personal information.

The plugin excludes Orbit vault secret values, secret metadata and connection credentials. It does not need your OpenAI password, API keys, payment details or precise location. Do not place credentials or regulated sensitive information in ordinary project notes intended for sharing.

## Where content goes

The server runs locally and reads an existing Orbit SQLite workspace in read-only mode. It does not send project data to a separate Orbit Browser hosting, advertising or analytics service.

Local execution does not mean all processing remains on your Mac. Codex can request project lists and supported project sections through the local tools. Browsing loads content through Codex, selecting an item supplies model context, and **Start new chat** sends a brief into a new conversation. Your AI host/provider receives that information under its account terms, privacy policy and settings. Consult those settings for retention, training controls and processing locations; this plugin does not override them.

SpacemanCodes LTD receives information you choose to send to support, such as an email address, error description or redacted screenshot. The company's support/email service providers may process that correspondence to deliver and handle the request. Normal website hosting/analytics practices are described in the existing [Orbit privacy notice](https://orbitdesktop.com/privacy/).

## Retention

- Original workspace content remains in Orbit until you change or remove it using Orbit's controls, subject to backups you maintain. The plugin cannot delete it.
- The plugin does not maintain a separate project-content database. It creates runtime caches under `~/Library/Caches/orbit-browser`; these remain until removed or replaced. Uninstalling the plugin may leave them on disk.
- Host conversations and tool results follow the AI host's retention and deletion controls. Deleting a note in Orbit does not delete a copy already shared with a host.
- **Publisher decision required:** specify and approve a concrete support-correspondence retention period, including any legal retention exceptions, before publishing. No unverified retention promise is made in this draft.

## Your controls

You can avoid sharing by not connecting the plugin, disconnect or uninstall it, choose which project to browse, review selected content before starting a new chat, manage original content in Orbit, and use the host's own chat/deletion controls. After stopping the plugin, you can remove its runtime-cache directory. Removing the cache does not delete original Orbit content or host conversations.

For requests about information you have sent to SpacemanCodes LTD, contact support@orbitdesktop.com. Requests about an AI host's copies should use that host's controls or privacy contact. We cannot promise to delete information held by another service through this read-only plugin.

## Publisher review notes — remove before publication

Add the effective date and confirmed legal contact details. Confirm the legal basis, support providers, international-transfer disclosures and applicable privacy-rights procedure for the business's actual operations. Approve the support retention schedule. Publish the approved notice at a stable public HTTPS URL, then update the manifest and rebuild the ZIP if its URL changes.
