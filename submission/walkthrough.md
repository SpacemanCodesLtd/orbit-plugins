# Orbit Browser recording and reviewer run sheet

Record the actual plugin in a local Codex chat on a Mac with Orbit installed. This document is a script, not a recording or a claim that the desktop checks have passed. A screenshot montage or simulated browser cannot replace evidence of the real workflows.

Supplied recording: https://media.orbitdesktop.com/orbit-browser-demo-2026-10-24.mp4. Uploaded 24 October 2026 to `orbit-media` alongside the website demo. The source is a 31-second native recording made with the installed 0.3.4 beta; full reviewer-case coverage and the 0.3.5 candidate still need recording sign-off.

## Setup

1. Use a dedicated review workspace/installation. Import `orbit-browser-demo-workspace.zip` through Orbit's workspace import screen with preference import off. It adds **Orbit Browser Demo**, two tasks, a document and a link. Do not use private projects in the recording.
2. Install the packaged plugin into a clean Codex profile using the supported local-plugin installation flow. Use a local chat. Confirm that Orbit Browser resolves to version 0.3.5 and the extracted package rather than a development checkout.
3. Open Orbit Browser. Frame the plugin, selected project and chat destination so viewers can see each handoff. Hide unrelated chats, notifications and personal information. Light theme is sufficient for the main recording; separately verify dark theme and keyboard focus.
4. Use the five positive and three negative cases in `.codex-plugin/plugin.json` as the authoritative case text. The statements below are the observation checklist.

When using computer use to navigate the demo, click **Start new chat** yourself. Automated clicks did not complete Codex's handoff confirmation in testing; the plugin now asks for a manual click immediately rather than sending an automated request and waiting. A manual document share was verified to create a new chat with the complete brief. Do not record an automated refusal as a successful handoff.

## Main recording, about 3–5 minutes

| Segment | Action and narration | Evidence to capture |
| --- | --- | --- |
| Introduction | “Orbit Browser reads project context from Orbit on this Mac. It cannot edit the workspace.” | Plugin and synthetic project name. |
| Browse | Select Orbit Browser Demo. Search tasks and choose Draft release notes. | Project/task highlights, To Do column and complete description. |
| Share task | Click Start new chat. “This opens a fresh conversation with the selected task.” | New chat, readable heading and description, source details, no raw JSON dump. Return to Orbit Browser afterward. |
| Share document | Select Documents → Release checklist → Start new chat. | Rendered headings/list/code in the preview, then complete Markdown content in a new chat. |
| Share scratchpad | Select Scratchpad → Start new chat. | Separate notes stay on separate lines; original headings and notes reach the new chat. |
| Links and dependencies | Select Links, then Dependencies. “Findings are saved snapshots from Orbit. This demo has no inspections.” | Saved Orbit guide URL; one empty dependency state with no empty share button. No scan or command starts. |
| Read-only boundary | Ask the negative-case prompts in a local chat using only Orbit Browser. | No write/secret/scan tools exist; invalid ID is rejected. The model explains the boundary without silently switching connectors. |
| Close | Return to Orbit and show the original task/notes unchanged. | No workspace mutation. |

Capture additional verification of a disposable project with saved dependency results: package names, installed/latest versions, advisory details and dates, with stale/incomplete warnings when applicable. Do not manufacture saved findings or claim a real security audit from the empty demo. Existing automated tests cover synthetic finding states; the native UI still needs recording/sign-off.

## Checklist

- [ ] Clean installation of the extracted release ZIP works without an Orbit source checkout or separate PHP.
- [ ] Project browsing and item filtering work.
- [ ] Task handoff opens a fresh chat and contains the selected task only.
- [ ] Document handoff preserves all original Markdown.
- [ ] Scratchpad preview and handoff preserve newlines.
- [ ] Links and empty dependency states work; actual saved findings include warnings/dates.
- [ ] All three negative cases have the documented safe result.
- [ ] Light/dark themes, narrow layout, long titles and keyboard focus remain usable.
- [ ] Workspace remains unchanged.
- [x] Supplied recording is hosted at a public URL; file integrity and video byte-range requests verified.
- [x] Add that URL as `extensions.com.openai.review.demo_recording_url`, then rebuild.

The native full-screen Codex plugin view has not been accessible to the automation tools used in this chat. The publisher must make the actual screen recording, or provide an accessible review surface before automated recording can be completed. Do not mark the recording complete merely because a script exists.

## Secure reviewer setup text

No Orbit login, licence activation or paid-plan credentials are required to exercise these cases. Install the public compatible Orbit desktop app, create a review workspace and import the included synthetic archive. Run Codex locally on the same Mac and open Orbit Browser. The plugin reads that local workspace through stdio. It has no hosted endpoint or remote test account. The recorded main cases fit within Orbit's documented free project allowance.

Provide this setup explanation through the portal's secure reviewer form if requested. Keep credentials out of the public manifest and ZIP; do not put `reviewer_instructions` or `test_credentials` in manifest metadata.
