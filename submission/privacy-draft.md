# Orbit privacy — published reference

**Published reference:** https://orbitdesktop.com/privacy/. Website source and publication review: https://github.com/captenmasin/orbit-marketing/pull/1. The owner confirmed inbox-only support, 12-month retention after resolution, and company-number/registry-link disclosure without publishing a postal address. The receiving mailbox provider and relevant transfer arrangements still need confirmation. [GOV.UK guidance](https://www.gov.uk/running-a-limited-company/signs-stationery-and-promotional-material) requires the registered office address and registration jurisdiction on a limited-company website. A Companies House link alone does not satisfy that guidance. The owner has requested no public postal address, so this disclosure issue remains unresolved.

Last updated: 24 October 2026.

This notice covers this website, support email and Orbit Browser's local integration with Codex. Account, licence, payment and other connected services may provide additional privacy information for their own processing.

## Who is responsible

Orbit is made by SpacemanCodes LTD, company number **12193639**. The company's details are available on the <a href="https://find-and-update.company-information.service.gov.uk/company/12193639" target="_blank" rel="noopener noreferrer">Companies House register</a>.

SpacemanCodes LTD is responsible for personal information it receives through this website and support email. Contact [support@orbitdesktop.com](mailto:support@orbitdesktop.com) with privacy questions or requests. Your chosen AI host is responsible for its own processing under its terms and privacy policy.

## Visiting this website

Cloudflare hosts and delivers this website. Loading a page or downloading a file sends ordinary network request information, including an IP address and browser headers, to the services handling that request. See <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener noreferrer">Cloudflare's privacy policy</a> for information about its processing.

The main website at orbitdesktop.com uses Pirsch Analytics to understand website usage. Preview versions do not send Pirsch analytics. Pirsch describes its analytics as cookie-free: it uses an IP address, user-agent, date, and a site-specific salt to derive a daily visitor identifier. Its documented statistics include visited paths, referrers, browser and device information, and approximate location. Read <a href="https://docs.pirsch.io/privacy" target="_blank" rel="noopener noreferrer">Pirsch's privacy documentation</a> for the provider's explanation of the data it processes.

If you choose a light or dark theme, the website saves that choice under `theme` in your browser's local storage. This lets it remember your preference on your next visit. You can remove it by clearing this site's browser storage. The website does not ask you to upload your Orbit workspace to browse its pages.

## Your desktop workspace

Orbit saves projects, tasks, documents, notes, links, uploaded assets, and preferences locally on your Mac. Linked folders remain in their original locations. A Pro activation does not synchronise workspace content between machines. See [local storage and offline use](https://orbitdesktop.com/guide/settings/local-data/) and [export and import](https://orbitdesktop.com/guide/settings/export-import/) for details and backup boundaries.

Optional Git connections and AI features communicate with the services you configure. An AI client connected through MCP may send tool results to its own model provider under that client's settings. Review those settings before granting access. MCP does not expose vault secret values or connection credentials; the [MCP guide](https://orbitdesktop.com/guide/integrations/mcp/) explains the access boundaries.

## Orbit Browser for Codex

Orbit Browser reads supported content from an existing local Orbit workspace. Depending on what you request or browse, this includes project names, descriptions and status; task titles, descriptions and board columns; scratchpad notes; document titles and bodies; saved link details; identifiers and revision numbers; and saved dependency versions, advisories, check dates and coverage warnings. Workspace and inspection responses can include linked local folder paths and availability. Information you have saved in these fields may identify you or other people.

The integration excludes Orbit vault secret values, secret metadata and connection credentials. It does not need your OpenAI password, API keys, payment details or precise location. Avoid putting credentials or sensitive information in ordinary notes intended for sharing.

The server runs locally and opens the workspace database read-only. SpacemanCodes LTD does not receive your project content merely because Orbit Browser reads it, and the integration does not send that content to a separate Orbit Browser hosting, advertising or analytics service.

Codex can request project lists and supported project sections through the local tools. Browsing loads content through Codex, selecting an item supplies model context, and **Start new chat** sends the selected brief into a fresh conversation. These operations can share information with your AI host or model provider. They are not limited to processing on your Mac or to the final brief alone. The host's settings and policies govern its copies, training controls, retention and processing locations; Orbit Browser does not override them.

The integration is read-only. It cannot delete original notes, host conversations or copies already shared with another service. See the [Orbit Browser terms](https://orbitdesktop.com/terms/) for its supported use and limitations.

## Support email

When you email support, we receive your email address, the message and any attachments you choose to send. Authorised people handling support use that information to answer your request, troubleshoot the issue and manage follow-up. We use an email inbox, without a separate helpdesk or ticketing service.

Our email routing and mailbox providers process correspondence to deliver and store it. The domain uses Cloudflare Email Routing. Share only what is necessary: do not email passwords, licence keys, provider tokens, recovery codes, secret values or an entire workspace when a short description is enough. See [Contact Orbit](https://orbitdesktop.com/contact/) for reporting guidance.

## Why we use personal information

We use website request information to deliver and protect the site, and usage statistics to understand which pages and workflows are useful. Our legitimate interests are keeping the website reliable and secure, maintaining the product and understanding its use. The analytics and local theme preference described above serve those purposes.

We use support correspondence to provide the assistance you request. Where that is necessary to perform a contract with you, that is our basis for processing. For other enquiries and proportionate troubleshooting, we rely on our legitimate interests in answering questions, resolving problems and maintaining the product. We may also retain relevant records where necessary to meet a legal obligation or establish, exercise or defend a legal claim.

You do not need to send us workspace content to browse the public website. If you choose not to provide enough information to investigate a support request, we may be unable to resolve that particular issue. We do not use Orbit Browser to make decisions about people with legal or similarly significant effects.

## Retention and deletion

- **Support correspondence:** we delete or anonymise resolved enquiries within **12 months after resolution**, unless particular records are still needed for an active dispute or legal obligation. We retain those records only for the relevant purpose and review the need to keep them.
- **Original workspace content:** it remains in Orbit until you change or remove it using Orbit's controls, subject to backups you maintain. Orbit Browser does not maintain a separate project-content database.
- **Runtime caches:** Orbit Browser creates runtime caches under `~/Library/Caches/orbit-browser`. They remain until removed or replaced, and uninstalling the integration may leave them on disk. After stopping the integration, you can remove this directory without deleting the original Orbit workspace.
- **AI host copies:** conversation history and tool results follow the host's retention and deletion controls. Deleting a note in Orbit or uninstalling Orbit Browser does not delete a copy already shared with a host.
- **Local website preference:** the saved `theme` preference remains in your browser until you clear it or change the setting.

Website request and security information is handled by Cloudflare under its service policies. Pirsch uses a site-specific daily identifier; its documentation describes separate user-agent storage for up to three months and controls for deleting analytics data. These provider practices are separate from the 12-month period for support enquiries. We review information we control and remove or anonymise it when no longer needed for the purposes described here.

## Providers and international processing

The recipients of information described in this notice include our website hosting, analytics, email routing and mailbox service providers, and authorised people responding to support. We may also disclose necessary information to professional advisers or authorities where required for a legal obligation or a dispute.

These providers, and any AI host you choose to connect, may process information outside the UK or your country. Their linked privacy information explains their processing and locations. Contact us for details of the arrangements and any applicable safeguards for transfers we are responsible for; your AI host's own terms and privacy controls apply to information you send to that host.

## Your choices and rights

You can disconnect or uninstall Orbit Browser, choose which projects to browse, review material before starting a new chat, manage original content in Orbit and use the AI host's own chat and deletion controls. Removing local runtime caches does not delete original projects or host conversations.

Depending on the information and the applicable law, you may have rights to access or correct it, request erasure or restriction, object to processing based on legitimate interests, or receive certain information in a portable form. If processing relies on consent, you may withdraw that consent. These rights are subject to their applicable conditions and exceptions.

To make a request about information SpacemanCodes LTD holds, email [support@orbitdesktop.com](mailto:support@orbitdesktop.com). Requests about an AI host's copies should use that host's controls or privacy contact; this integration cannot delete data held by another service.

You can also raise a concern with the <a href="https://ico.org.uk/make-a-complaint/" target="_blank" rel="noopener noreferrer">UK Information Commissioner's Office</a> or your local data protection authority. You do not have to contact us first, although we welcome the opportunity to address a concern.

## Changes to this notice

We will update this page when these practices change and revise the date at the top. Where required, we will provide additional notice before using personal information for a new purpose.
