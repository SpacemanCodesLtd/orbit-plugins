# Orbit Browser submission kit

Prepared 24 October 2026 for version 0.3.6. This is a preparation kit, not evidence of OpenAI approval or a submitted listing.

## Files

- `orbit-browser-0.3.6.zip`: plugin upload. Its root contains `.codex-plugin/plugin.json`, `.mcp.json`, the runtime launcher, read-only tools, UI and icons.
- `orbit-browser-demo-workspace.zip`: synthetic Orbit workspace for reviewer setup and recording. Import through Orbit Settings → Data → Import. It adds one project; importing twice adds duplicates. Use a separate review installation/workspace and leave preference import off. It contains no credentials, local folders, files or private data.
- `listing-copy.md`: text generated from the shipped manifest.
- `terms-draft.md` and `privacy-draft.md`: archived reference copies of the plugin legal copy from the website changes in [website PR #1](https://github.com/captenmasin/orbit-marketing/pull/1). The owner confirmed inbox-only support, retention for 12 months after resolution, and company-number/registry-link disclosure without a public postal address. The plugin terms page and plugin-specific privacy disclosures were removed from the marketing site in [website PR #2](https://github.com/captenmasin/orbit-marketing/pull/2) while submission access is pending. Restore them before resuming submission; the receiving mailbox provider and relevant transfer arrangements still need confirmation.
- `walkthrough.md`: recording and reviewer run sheet.
- `submission-status.json`: outstanding submission items.
- `SHA256SUMS`: checksums of the plugin, workspace and complete kit ZIPs, provided beside the ZIPs.

The complete review kit ZIP is for the publisher/reviewer. Upload **only the plugin ZIP** into the plugin package field. Keep the kit's legal drafts and setup notes outside that runtime package. Do not upload screenshots of a real workspace as review evidence.

## Build and validate

From the repository root:

```sh
node --test tests/browser.test.mjs
python3 -m unittest discover -s tests -p 'test_*.py' -v
python3 scripts/package-release.py
```

Outputs go to `dist/` and are ignored by Git. The builder uses an explicit list of runtime folders; it does not read a live database or include the source checkout, environment files, credentials, caches, tests or review notes. It creates deterministic ZIPs and a SHA-256 checksum file. The integration test extracts the upload ZIP and starts its local server against temporary data, and checks the sample archive with Orbit's own archive preview validator.

## Submission route

Orbit Browser is a local stdio MCP plugin. It needs a local Codex chat on the same Mac as the installed Orbit application. Do not submit it as a public HTTPS MCP endpoint.

1. Confirm the submitting organisation has approved-partner local MCP access. Send its organisation ID to your OpenAI representative and wait for allowlisting confirmation. Do not put the organisation ID or private access details in this public kit.
2. Complete developer/business identity verification and confirm owner or Apps Management Write access. The suggested publisher, **SpacemanCodes LTD**, follows Orbit's public website; select the matching verified identity. The portal controls the final displayed developer identity.
3. Restore the [plugin terms](https://orbitdesktop.com/terms/) and plugin-specific disclosures in the [privacy page](https://orbitdesktop.com/privacy/) before resuming submission. The Git-backed beta links to its repository privacy information and omits the unavailable terms URL. Restore the dedicated legal URLs in the manifest for public directory submission; [website PR #2](https://github.com/captenmasin/orbit-marketing/pull/2) removed the public plugin copy while submission access is pending. The receiving mailbox provider and relevant transfer arrangements remain unconfirmed. [GOV.UK guidance](https://www.gov.uk/running-a-limited-company/signs-stationery-and-promotional-material) requires the registered office address and registration jurisdiction on a limited-company website; the owner requested no public postal address, so that disclosure issue remains unresolved.
4. The [supplied recording](https://media.orbitdesktop.com/orbit-browser-demo-2026-10-24.mp4) is hosted on Orbit's public R2 media domain and included as `extensions.com.openai.review.demo_recording_url`. It is a 31-second clip recorded with the installed 0.3.4 beta. Review it against all cases and capture any missing workflows on the 0.3.6 candidate before final submission; full recording coverage has not been signed off.
5. Follow the desktop-only flow in the submission portal: **Create plugin → Skills only → Upload ZIP**. Despite the label, this is the documented route for approved local MCP submissions. Choose the verified identity and inspect all imported metadata. No skills are required for this local MCP package.
6. Resolve automated findings. The manifest contains exactly five positive and three negative review cases. Run them on the review setup and sign off in `walkthrough.md`. Do not represent automated protocol tests as native desktop UI evidence.
7. Add reviewer setup/access information through the portal's secure Review details form. No Orbit account sign-in or reviewer password is needed for this local plugin; the reviewer needs Orbit installed, sample local data and a local Codex chat. If the portal requires an account credential, ask OpenAI how to record this desktop setup rather than supplying a fabricated account.
8. Submit for review and complete the required attestations yourself. After approval, choose when to publish. This kit does not upload, attest, accept terms or publish on your behalf.

## Local runtime disclosure

The ZIP includes Orbit Browser's code and UI. It deliberately does **not** redistribute Orbit.app, PHP, Composer dependencies, database files, a licence key or a development checkout. The launcher finds the separately installed Orbit app in `/Applications` or `~/Applications` and uses its bundled runtime and read APIs. Tested against Orbit 0.5.1; it is not a standalone server independent of Orbit.

Confirm this installed-application dependency with the local-plugin review team. The desktop documentation asks for the server and dependencies in the package; the current integration depends on the publisher's separately installed application. Do not claim that dependency has been bundled or explicitly accepted until OpenAI confirms the arrangement. If required, a standalone runtime package is additional implementation work, not a packaging checkbox.

The plugin opens SQLite read-only, keeps reconnects read-only, excludes secret metadata and values, and exposes only `browse-orbit-tool`, `read-workspace-tool` and `read-browser-content-tool`. It creates its own runtime cache directories. Host-side model context and new-chat sharing are outbound to Codex; the plugin does not run an external telemetry or hosting service. Dependency results are saved snapshots, not live audits.

## Review status and outstanding work

- [x] Publisher name, email, product/support URLs, beta privacy information and starter prompts prepared.
- [x] Plugin, server and UI resource versions match; package uses existing supported Codex manifest format.
- [x] Five positive and three negative cases are embedded in the manifest.
- [x] Square 60 × 60 SVG light/dark icons are included.
- [x] Importable synthetic sample project and recording script prepared.
- [ ] Confirm local MCP allowlisting, verified identity and installed Orbit dependency eligibility.
- [x] Prepare website legal pages with the owner-confirmed support retention and company disclosure choices.
- [ ] Restore the plugin legal pages and verify the candidate manifest URLs before resuming submission.
- [ ] Resolve mailbox-provider/transfer arrangements and company-address disclosure.
- [x] Host the supplied recording, add its URL to the manifest and rebuild.
- [ ] Review recording coverage and capture missing workflows on the submission candidate.
- [ ] Complete fresh native desktop installation, five positive cases and three negative cases with sample data.
- [ ] Verify dark theme, narrow layout and actual saved dependency findings. Earlier user screenshots confirmed the light task/document previews; the final scratchpad line-break change still needs native visual sign-off.
- [ ] Upload the plugin ZIP, resolve portal findings, submit and publish after approval.

## Official references

- [Submit desktop-only plugins](https://developers.openai.com/plugins/deploy/desktop-only)
- [Submission metadata and review fields](https://developers.openai.com/plugins/deploy/submission#automatically-provide-submission-and-review-information)
- [Plugin guidelines](https://developers.openai.com/plugins/plugin-guidelines)
- [Submission portal](https://platform.openai.com/plugins)

The current guidelines say directory screenshots are no longer shown. No real workspace screenshots are packaged, and no screenshot fields are added to this local upload. The recording should demonstrate the actual plugin with synthetic content.
