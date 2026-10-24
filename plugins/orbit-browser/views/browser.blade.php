<x-mcp::app title="Orbit">
    <x-slot:head>
        <style>
            :root {
                color-scheme: light dark;
                font-family: var(--font-sans, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif);
                font-size: 13px;
                --fg: var(--color-text-primary, light-dark(#202020, #ededed));
                --muted: var(--color-text-secondary, light-dark(#767676, #a3a3a3));
                --bg: var(--color-background-primary, light-dark(#fff, #191919));
                --subtle: color-mix(in srgb, var(--fg) 4%, var(--bg));
                --selected: color-mix(in srgb, var(--fg) 9%, var(--bg));
                --line: var(--color-border-primary, light-dark(#e9e9e9, #333));
                color: var(--fg);
                background: var(--bg);
            }
            * { box-sizing: border-box; }
            body { margin: 0; }
            button, input { font: inherit; color: inherit; }
            button { appearance: none; border: 0; background: transparent; padding: 0; text-align: left; }
            button:disabled { opacity: .45; }
            :focus-visible { outline: 2px solid var(--fg); outline-offset: -2px; }
            h1, h2, h3, p { margin: 0; }
            h1 { font-size: 15px; font-weight: 600; letter-spacing: -.025em; }
            h2 { font-size: 23px; font-weight: 600; letter-spacing: -.035em; line-height: 1.3; }
            h3 { font-size: 12px; font-weight: 500; }
            small, .muted { color: var(--muted); }
            .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); white-space: nowrap; }
            #browser { height: 100dvh; padding-bottom: var(--safe-bottom, 0px); display: grid; grid-template-columns: 224px minmax(0, 1fr); }
            .sidebar { min-height: 0; display: flex; flex-direction: column; padding: 18px 12px 12px; border-right: 1px solid var(--line); }
            .brand { display: flex; align-items: center; justify-content: space-between; margin: 0 4px 16px; }
            .brand-name { display: flex; align-items: center; gap: 4px; }
            .brand-mark svg { display: block; width: 30px; height: 30px; }
            .brand-mark path[stroke] { stroke: var(--fg); }
            .brand-mark path[fill] { fill: var(--fg); }
            #refresh { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 50%; font-size: 21px; }
            input { appearance: none; width: 100%; min-width: 0; height: 36px; border: 1px solid transparent; border-radius: 999px; padding: 0 12px; background: var(--subtle); }
            input::placeholder { color: var(--muted); opacity: 1; }
            #projects { min-height: 0; overflow: auto; overscroll-behavior: contain; margin-top: 10px; padding: 2px 0; }
            .project { width: 100%; display: flex; flex-direction: column; gap: 3px; padding: 10px 12px; border-radius: 10px; margin: 1px 0; }
            .project span { max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
            .project small { font-size: 11px; }
            button[aria-current="true"] { background: var(--selected); }
            main { min-width: 0; min-height: 0; }
            #project-empty { height: 100%; display: grid; place-content: center; color: var(--muted); }
            #project-empty h2 { font-size: 17px; font-weight: 400; }
            #project-view { height: 100%; display: flex; flex-direction: column; min-width: 0; }
            .project-heading { padding: 26px 28px 0; }
            #project-title { overflow-wrap: anywhere; }
            #project-description { margin-top: 7px; max-width: 72ch; line-height: 1.6; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; }
            #project-description:empty { display: none; }
            .sections { display: flex; flex-shrink: 0; gap: 4px; padding: 0 20px; margin-top: 16px; border-bottom: 1px solid var(--line); overflow: auto; }
            .sections button { position: relative; flex-shrink: 0; padding: 6px 8px 13px; border-radius: 6px 6px 0 0; color: var(--muted); font-weight: 500; white-space: nowrap; }
            .sections button[aria-pressed="true"] { color: var(--fg); }
            .sections button[aria-pressed="true"]::after { content: ''; position: absolute; height: 2px; background: var(--fg); bottom: 0; left: 8px; right: 8px; border-radius: 2px; }
            .panes { flex: 1; min-height: 0; display: grid; grid-template-columns: minmax(220px, 310px) minmax(0, 1fr); }
            #list-pane { min-height: 0; min-width: 0; display: flex; flex-direction: column; border-right: 1px solid var(--line); padding: 16px 12px 24px; }
            #item-search { flex-shrink: 0; background: transparent; border-color: var(--line); }
            #items { min-height: 0; overflow: auto; overscroll-behavior: contain; margin-top: 12px; padding: 2px 0; }
            #items h3 { color: var(--muted); padding: 14px 10px 8px; }
            #items h3:first-child { padding-top: 2px; }
            .item { width: 100%; padding: 11px 12px; margin-bottom: 3px; border-radius: 10px; overflow-wrap: anywhere; }
            .item span { font-size: 13px; line-height: 1.5; font-weight: 500; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; }
            .item small { display: block; margin-top: 4px; font-size: 11px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
            #preview { min-width: 0; min-height: 0; overflow: auto; overscroll-behavior: contain; padding: 24px 28px 40px; }
            .preview-heading { display: flex; align-items: start; gap: 18px; justify-content: space-between; margin-bottom: 20px; }
            #item-title { font-size: 20px; font-weight: 600; line-height: 1.4; letter-spacing: -.025em; overflow-wrap: anywhere; }
            #item-meta { margin-top: 6px; font-size: 12px; line-height: 1.5; overflow-wrap: anywhere; }
            #item-meta:empty { display: none; }
            #share { flex-shrink: 0; min-height: 32px; border: 1px solid var(--line); padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 500; white-space: nowrap; }
            #item-body { max-width: 68ch; font-size: 14px; line-height: 1.65; overflow-wrap: anywhere; }
            #item-body > * + * { margin-top: 16px; }
            #item-body h1, #item-body h2, #item-body h3 { font-weight: 600; letter-spacing: -.02em; margin: 24px 0 10px; }
            #item-body h1 { font-size: 19px; } #item-body h2 { font-size: 17px; } #item-body h3 { font-size: 15px; }
            #item-body > :first-child { margin-top: 0; }
            #item-body ul, #item-body ol { padding-left: 24px; }
            #item-body li + li { margin-top: 5px; }
            #item-body a { color: inherit; text-underline-offset: 3px; }
            #item-body blockquote { margin-left: 0; border-left: 2px solid var(--line); padding-left: 16px; color: var(--muted); }
            #item-body pre { overflow: auto; background: var(--subtle); border-radius: 10px; padding: 16px; font-size: 12px; }
            #item-body code { font-family: ui-monospace, monospace; font-size: .9em; background: var(--subtle); border-radius: 4px; padding: 1px 4px; }
            #item-body pre code { background: none; padding: 0; }
            #item-body img { max-width: 100%; }
            #item-body table { display: block; width: 100%; overflow: auto; border-collapse: collapse; text-align: left; font-size: 12px; }
            #item-body th, #item-body td { padding: 10px 14px 10px 0; border-bottom: 1px solid var(--line); white-space: nowrap; }
            #item-body th { color: var(--muted); font-weight: 500; }
            .plain-text { white-space: pre-wrap; }
            #empty-view { padding: 32px 28px; color: var(--muted); }
            .reading { grid-template-columns: 1fr; }
            .reading #preview { max-width: 1000px; }
            #notice { position: fixed; left: 244px; bottom: calc(var(--safe-bottom, 0px) + 16px); max-width: min(600px, calc(100vw - 280px)); z-index: 1; background: var(--bg); border: 1px solid var(--line); border-radius: 10px; padding: 10px 14px; box-shadow: 0 4px 16px #0000000a; font-size: 12px; }
            #notice:empty { display: none; }
            [hidden] { display: none !important; }
            @media (hover: hover) and (pointer: fine) { .project:not([aria-current="true"]):hover, .item:not([aria-current="true"]):hover, #refresh:hover { background: var(--subtle); } #share:hover { background: var(--subtle); } }
            @media (max-width: 1050px) { .panes { grid-template-columns: minmax(190px, 250px) minmax(0, 1fr); } .preview-heading { flex-direction: column; gap: 14px; } .reading { grid-template-columns: 1fr; } }
            @media (max-width: 760px) { #browser { grid-template-columns: 180px minmax(0, 1fr); } .project-heading { padding: 20px 20px 0; } .sections { padding: 0 12px; } .panes { overflow: auto; display: flex; flex-direction: column; } #list-pane { max-height: 220px; flex-shrink: 0; border-right: 0; border-bottom: 1px solid var(--line); } #preview { overflow: visible; min-height: auto; padding: 20px; } #notice { left: 196px; max-width: calc(100vw - 212px); } }
            @media (max-width: 520px) { #browser { height: auto; min-height: 100dvh; grid-template-columns: 1fr; } .sidebar { border-right: 0; border-bottom: 1px solid var(--line); padding-top: 12px; } .brand { margin-bottom: 10px; } #projects { max-height: 140px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); } .project { min-width: 0; } #project-empty { min-height: 160px; } .panes { overflow: visible; } #list-pane { max-height: 210px; } .sections { margin-top: 18px; } #notice { left: 16px; right: 16px; max-width: none; } }
        </style>
        <script type="module">
            {!! $dependencyScript !!}
            {!! $browserScript !!}
        </script>
    </x-slot:head>

    <div id="browser">
        <aside class="sidebar" aria-label="Projects">
            <div class="brand">
                <div class="brand-name">
                    <span class="brand-mark" aria-hidden="true">{!! $icon !!}</span>
                    <h1>Orbit</h1>
                </div>
                <button id="refresh" type="button" aria-label="Refresh" title="Refresh" disabled><span aria-hidden="true">↻</span></button>
            </div>
            <label class="sr-only" for="search">Find a project</label>
            <input id="search" type="search" placeholder="Search projects" autocomplete="off" disabled>
            <nav id="projects" aria-label="Project list"></nav>
        </aside>
        <main>
            <div id="project-empty"><h2>Choose a project</h2></div>
            <section id="project-view" aria-labelledby="project-title" hidden>
                <div class="project-heading">
                    <h2 id="project-title"></h2>
                    <p id="project-description" class="muted"></p>
                </div>
                <nav class="sections" aria-label="Project content">
                    <button id="tab-tasks" type="button" aria-pressed="true">Tasks</button>
                    <button id="tab-scratchpad" type="button" aria-pressed="false">Scratchpad</button>
                    <button id="tab-documents" type="button" aria-pressed="false">Documents</button>
                    <button id="tab-dependencies" type="button" aria-pressed="false">Dependencies</button>
                    <button id="tab-links" type="button" aria-pressed="false">Links</button>
                </nav>
                <div id="panes" class="panes">
                    <div id="list-pane">
                        <input id="item-search" type="search" aria-label="Search tasks" placeholder="Search tasks" autocomplete="off">
                        <div id="items"></div>
                    </div>
                    <div id="empty-view" hidden><p id="content-empty"></p></div>
                    <section id="preview" aria-labelledby="item-title" hidden>
                        <div class="preview-heading">
                            <div>
                                <h2 id="item-title" tabindex="-1"></h2>
                                <p id="item-meta" class="muted"></p>
                            </div>
                            <button id="share" type="button" disabled>Start new chat</button>
                        </div>
                        <div id="item-body"></div>
                    </section>
                </div>
            </section>
        </main>
        <p id="notice" role="status" aria-live="polite">Connecting to Orbit…</p>
    </div>
</x-mcp::app>
