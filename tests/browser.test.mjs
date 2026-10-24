import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';
import { currentDependencyCheck, dependencyName } from '../plugins/orbit-browser/assets/dependency-check.mjs';

const source = readFileSync(new URL('../plugins/orbit-browser/assets/browser.js', import.meta.url), 'utf8');
const copy = (value) => JSON.parse(JSON.stringify(value));
const settle = () => new Promise((resolve) => setImmediate(resolve));
const projects = [{ id: 'one', name: 'Orbit <script>', status: 'Live' }, { id: 'two', name: 'Other', status: 'Idea' }];
const details = (id = 'one') => ({
    id, name: id === 'one' ? projects[0].name : 'Other', revision: 7, description: 'Project description',
    scratchpad: 'Private notes', scratchpad_html: '<p>Private notes</p>', secrets: [{ name: 'TOKEN' }],
    documents: [{ id: 'doc-one', title: 'Guide', body: 'Unrelated document', body_html: '<p>Unrelated document</p>', revision: 2, folder_id: 'nested' }],
    document_folders: [{ id: 'parent', name: 'Handbook' }, { id: 'nested', name: 'Setup', parent_id: 'parent' }],
    links: [{ id: 'link-one', label: 'Website', url: 'https://example.com', category: 'Production' }],
    board: [{ name: 'To do', tasks: [{ id: 'task-one', title: '<img onerror=alert(1)>', description: 'Task details' }] }],
});

function mount(overrides = {}) {
    const elements = new Map();
    class Element {
        constructor(tag = 'div') { this.style = { setProperty() {} }; this.tag = tag; this.children = []; this.events = {}; this.attributes = {}; this.value = ''; this.textContent = ''; this.disabled = false; this.hidden = false; }
        append(...children) { this.children.push(...children); }
        replaceChildren(...children) { this.children = children; }
        setAttribute(name, value) { this.attributes[name] = value; }
        addEventListener(name, handler) { this.events[name] = handler; }
        focus() { document.activeElement = this; }
        set innerHTML(value) {
            if (this !== document.getElementById('item-body')) { throw new Error('User content must never become HTML'); }
            this.html = value;
        }
    }
    const document = {
        getElementById(id) {
            if (!elements.has(id)) { elements.set(id, new Element()); }
            return elements.get(id);
        },
        createElement: (tag) => new Element(tag),
    };
    const calls = [];
    const contexts = [];
    const messages = [];
    const app = {
        async callServerTool(params) {
            calls.push(copy(params));
            return { content: [{ type: 'text', text: JSON.stringify(params.name === 'read-workspace-tool' ? projects : details(params.arguments.project_id)) }] };
        },
        async updateModelContext(context) { contexts.push(copy(context)); },
        async sendMessage(message) { messages.push(copy(message)); },
        getHostContext: () => ({}),
        onHostContextChanged() {},
        ...overrides,
    };
    let ready;
    runInNewContext(source, { currentDependencyCheck, dependencyName, document, window: { createMcpApp: (setup) => { ready = setup(app); return ready; } } });
    const get = (id) => document.getElementById(id);
    async function click(element) { await element.events.click(); await settle(); }
    return { ready, get, calls, contexts, messages, click, document, app };
}

async function selectFirstTask(current) {
    await current.click(current.get('projects').children[0]);
    await current.click(current.get('items').children[1]);
}

test('browser lists and filters projects, renders task data as text and sends only the selection', async () => {
    const current = mount();
    await current.ready;
    assert.equal(current.get('projects').children.length, 2);
    current.get('search').value = 'ORBIT';
    current.get('search').events.input();
    assert.equal(current.get('projects').children.length, 1);
    await selectFirstTask(current);

    assert.equal(current.get('item-title').textContent, '<img onerror=alert(1)>');
    assert.equal(current.document.activeElement, current.get('item-title'));
    assert.equal(current.get('preview').hidden, false);
    assert.deepEqual(current.contexts.at(-1).structuredContent, {
        project: { id: 'one', name: projects[0].name, revision: 7 },
        selection: { kind: 'task', id: 'task-one', title: '<img onerror=alert(1)>', body: 'Task details', column: 'To do' },
    });
    await current.click(current.get('share'));
    assert.equal(current.messages.length, 1);
    assert.match(current.messages[0].content[0].text, /Task details/);
    assert.doesNotMatch(JSON.stringify(current.messages), /Private notes|TOKEN|Unrelated document/);
    assert.equal(current.calls.every(({ name }) => ['read-workspace-tool', 'read-browser-content-tool'].includes(name)), true);
});

test('browser gives distinct empty workspace, filtered and empty task states', async () => {
    const current = mount();
    await current.ready;
    current.get('search').value = 'no-match';
    current.get('search').events.input();
    assert.equal(current.get('projects').children[0].textContent, 'No matching projects.');
    current.app.callServerTool = async () => ({ content: [{ type: 'text', text: JSON.stringify({ ...details(), board: [] }) }] });
    current.get('search').value = '';
    current.get('search').events.input();
    await current.click(current.get('projects').children[0]);
    assert.equal(current.get('content-empty').textContent, 'No tasks in this project.');
    current.app.callServerTool = async () => ({ content: [{ type: 'text', text: '[]' }] });
    await current.click(current.get('refresh'));
    assert.equal(current.get('projects').children[0].textContent, 'No projects in this workspace.');
    assert.deepEqual(current.contexts.at(-1).structuredContent, { project: null, selection: null });
});

test('project selection discards late responses from previously selected projects', async () => {
    const current = mount();
    await current.ready;
    const pending = new Map();
    current.app.callServerTool = ({ arguments: args }) => new Promise((resolve) => pending.set(args.project_id, resolve));
    await current.click(current.get('projects').children[0]);
    await current.click(current.get('projects').children[1]);
    pending.get('two')({ content: [{ type: 'text', text: JSON.stringify(details('two')) }] });
    await settle();
    pending.get('one')({ content: [{ type: 'text', text: JSON.stringify(details('one')) }] });
    await settle();

    assert.equal(current.get('project-title').textContent, 'Other');
    assert.equal(current.contexts.at(-1).structuredContent.project.id, 'two');
});

test('a project load failure clears stale task context and refresh retries that project', async () => {
    const current = mount();
    await current.ready;
    await selectFirstTask(current);
    const call = current.app.callServerTool;
    current.app.callServerTool = async () => ({ isError: true, content: [{ type: 'text', text: 'Project unavailable' }] });
    await current.click(current.get('projects').children[1]);
    assert.match(current.get('notice').textContent, /Project unavailable/);
    assert.equal(current.get('project-view').hidden, true);
    assert.equal(current.get('preview').hidden, true);
    assert.deepEqual(current.contexts.at(-1).structuredContent, { project: null, selection: null });
    current.app.callServerTool = call;
    await current.click(current.get('refresh'));
    assert.equal(current.get('project-title').textContent, 'Other');
});

test('refresh preserves the selected task and clears it when it is deleted', async () => {
    const current = mount();
    await current.ready;
    await selectFirstTask(current);
    await current.click(current.get('refresh'));
    assert.equal(current.get('preview').hidden, false);
    assert.equal(current.contexts.at(-1).structuredContent.selection.id, 'task-one');
    const call = current.app.callServerTool;
    current.app.callServerTool = (params) => params.name === 'read-browser-content-tool'
        ? Promise.resolve({ content: [{ type: 'text', text: JSON.stringify({ ...details(), board: [] }) }] })
        : call(params);
    await current.click(current.get('refresh'));
    assert.equal(current.get('preview').hidden, true);
    assert.equal(current.contexts.at(-1).structuredContent.selection, null);
});

test('workspace errors and malformed responses leave refresh available for retry', async () => {
    const current = mount({ callServerTool: async () => ({ content: [{ type: 'text', text: '{invalid' }] }) });
    await current.ready;
    assert.match(current.get('notice').textContent, /Could not load projects/);
    assert.equal(current.get('refresh').disabled, false);
    current.app.callServerTool = async () => ({ content: [{ type: 'text', text: JSON.stringify(projects) }] });
    await current.click(current.get('refresh'));
    assert.equal(current.get('projects').children.length, 2);
});

test('context failures can be retried and task sharing reports failure without leaving its button disabled', async () => {
    const current = mount({
        updateModelContext: async () => { throw new Error('Context unavailable'); },
        sendMessage: async () => { throw new Error('Chat unavailable'); },
    });
    await current.ready;
    await selectFirstTask(current);
    assert.match(current.get('notice').textContent, /Could not share this selection/);
    current.app.updateModelContext = async (context) => current.contexts.push(copy(context));
    await current.click(current.get('items').children[1]);
    assert.equal(current.contexts.at(-1).structuredContent.selection.id, 'task-one');
    await current.click(current.get('share'));
    assert.match(current.get('notice').textContent, /Chat unavailable/);
    assert.equal(current.get('share').disabled, false);
});

test('a late workspace refresh does not replace a newer project selection', async () => {
    const current = mount();
    await current.ready;
    const call = current.app.callServerTool;
    let finishRefresh;
    current.app.callServerTool = (params) => params.name === 'read-workspace-tool'
        ? new Promise((resolve) => { finishRefresh = resolve; })
        : call(params);
    await current.click(current.get('refresh'));
    await current.click(current.get('projects').children[1]);
    finishRefresh({ content: [{ type: 'text', text: JSON.stringify(projects) }] });
    await settle();

    assert.equal(current.get('project-title').textContent, 'Other');
    assert.equal(current.contexts.at(-1).structuredContent.project.id, 'two');
});

test('context updates finish in selection order when the host is slow', async () => {
    let release;
    const contexts = [];
    const current = mount({ updateModelContext: (context) => {
        contexts.push(copy(context));
        if (contexts.length === 1) { return new Promise((resolve) => { release = resolve; }); }
        return Promise.resolve();
    } });
    await current.ready;
    await selectFirstTask(current);
    assert.equal(contexts.length, 1);
    release();
    await settle();

    assert.equal(contexts.at(-1).structuredContent.selection.id, 'task-one');
});

test('scratchpad, nested documents and links share their source content without unrelated data', async () => {
    const current = mount();
    await current.ready;
    await current.click(current.get('projects').children[0]);
    await current.click(current.get('tab-scratchpad'));
    assert.equal(current.get('list-pane').hidden, true);
    assert.equal(current.get('item-body').html, '<p>Private notes</p>');
    await current.click(current.get('share'));
    let shared = JSON.parse(current.messages.at(-1).content[0].text.split('\n').slice(1).join('\n'));
    assert.equal(shared.selection.kind, 'scratchpad');
    assert.equal(shared.selection.body, 'Private notes');
    assert.doesNotMatch(JSON.stringify(shared), /TOKEN|Unrelated document|<p>/);

    await current.click(current.get('tab-documents'));
    assert.equal(current.get('item-meta').textContent, 'Handbook / Setup');
    assert.equal(current.get('item-body').html, '<p>Unrelated document</p>');
    await current.click(current.get('share'));
    shared = JSON.parse(current.messages.at(-1).content[0].text.split('\n').slice(1).join('\n'));
    assert.equal(shared.selection.kind, 'document');
    assert.equal(shared.selection.revision, 2);
    assert.equal(shared.selection.body, 'Unrelated document');
    assert.doesNotMatch(JSON.stringify(shared), /Private notes|TOKEN|<p>/);

    await current.click(current.get('tab-links'));
    await current.click(current.get('share'));
    shared = JSON.parse(current.messages.at(-1).content[0].text.split('\n').slice(1).join('\n'));
    assert.equal(shared.selection.kind, 'link');
    assert.equal(shared.selection.url, 'https://example.com');
    assert.equal(shared.selection.category, 'Production');
});

function inspectionData() {
    const root = (id, fingerprint = id) => ({
        id, relative_path: id === 'one' ? '.' : id, scan_state: 'Current', scan_error: null,
        snapshot: { fingerprint: id },
        outdated: { fingerprint, checked_at: '2026-10-24T10:00:00Z', checked: 1, unavailable: 0, skipped: 0,
            packages: [{ name: 'vue', ecosystem: 'npm', current: '3.0.0', latest: '3.5.0' }] },
        security: { fingerprint, checked_at: '2026-10-24T10:01:00Z', checked: 1, unavailable: 0, skipped: 0, packages: [] },
    });
    const first = root('one');
    first.security.packages = [{ name: 'vue', ecosystem: 'npm', current: '3.0.0', advisories: [{ id: 'GHSA-example', severity: 'High', title: 'Unsafe input', url: 'https://osv.dev/example' }] }];
    const unavailable = root('unavailable');
    unavailable.outdated.packages = [];
    unavailable.outdated.unavailable = 1;
    unavailable.security.skipped = 2;
    return [{ id: 'folder', path: '/projects/example', availability: 'Available', package_roots: [first, root('two'), root('stale', 'previous'), unavailable] }];
}

test('dependencies load lazily, merge update and security findings and keep separate installations', async () => {
    const current = mount();
    await current.ready;
    await current.click(current.get('projects').children[0]);
    assert.equal(current.calls.some(({ arguments: args }) => args.section === 'inspection'), false);
    const call = current.app.callServerTool;
    current.app.callServerTool = (params) => params.arguments.section === 'inspection'
        ? Promise.resolve({ content: [{ type: 'text', text: JSON.stringify(inspectionData()) }] }) : call(params);
    await current.click(current.get('tab-dependencies'));
    const selection = current.contexts.at(-1).structuredContent.selection;
    assert.equal(selection.kind, 'dependencies');
    assert.equal(selection.findings.length, 2);
    assert.equal(selection.findings[0].latest, '3.5.0');
    assert.equal(selection.findings[0].advisories[0].severity, 'High');
    assert.notEqual(selection.findings[0].id, selection.findings[1].id);
    assert.match(selection.body, /results are stale/);
    assert.match(selection.body, /1 unavailable/);
    assert.match(selection.body, /2 skipped/);
    assert.equal(selection.coverage[2].outdated.state, 'stale');
    assert.equal(selection.findings.some((pkg) => pkg.root_id === 'stale'), false);
    await current.click(current.get('share'));
    assert.doesNotMatch(JSON.stringify(current.messages.at(-1)), /Private notes|TOKEN|Unrelated document|snapshot/);

    await current.click(current.get('items').children[1]);
    assert.equal(current.contexts.at(-1).structuredContent.selection.kind, 'dependency');
    assert.equal(current.contexts.at(-1).structuredContent.selection.package.root_id, 'one');
    await current.click(current.get('refresh'));
    assert.equal(current.contexts.at(-1).structuredContent.selection.kind, 'dependency');
    assert.equal(current.contexts.at(-1).structuredContent.selection.package.root_id, 'one');
});

test('dependency failures clear the selection, can be retried, and late responses do not cross projects', async () => {
    const current = mount();
    await current.ready;
    await current.click(current.get('projects').children[0]);
    const call = current.app.callServerTool;
    current.app.callServerTool = (params) => params.arguments.section === 'inspection'
        ? Promise.resolve({ isError: true, content: [{ type: 'text', text: 'Inspection unavailable' }] }) : call(params);
    await current.click(current.get('tab-dependencies'));
    assert.match(current.get('notice').textContent, /Inspection unavailable/);
    assert.equal(current.contexts.at(-1).structuredContent.selection, null);
    let resolve;
    current.app.callServerTool = (params) => params.arguments.section === 'inspection'
        ? new Promise((done) => { resolve = done; }) : call(params);
    await current.click(current.get('tab-dependencies'));
    await current.click(current.get('tab-tasks'));
    await current.click(current.get('projects').children[1]);
    resolve({ content: [{ type: 'text', text: JSON.stringify(inspectionData()) }] });
    await settle();
    assert.equal(current.contexts.at(-1).structuredContent.project.id, 'two');
    assert.equal(current.contexts.at(-1).structuredContent.selection.kind, 'task');
});

test('item search clears hidden selection and empty scratchpads stay unshareable', async () => {
    const current = mount();
    await current.ready;
    await current.click(current.get('projects').children[0]);
    current.get('item-search').value = 'does-not-match';
    current.get('item-search').events.input();
    await settle();
    assert.equal(current.get('share').disabled, true);
    assert.equal(current.contexts.at(-1).structuredContent.selection, null);
    assert.equal(current.get('content-empty').textContent, 'No matches.');
    current.app.callServerTool = async () => ({ content: [{ type: 'text', text: JSON.stringify({ ...details(), scratchpad: '' }) }] });
    await current.click(current.get('projects').children[0]);
    await current.click(current.get('tab-scratchpad'));
    assert.equal(current.get('share').disabled, true);
    assert.equal(current.get('empty-view').hidden, false);
    assert.equal(current.get('content-empty').textContent, 'No scratchpad in this project.');
});

test('dependency identities match Orbit normalization and intentional update exclusions are not incomplete coverage', async () => {
    const current = mount();
    await current.ready;
    await current.click(current.get('projects').children[0]);
    const inspection = inspectionData();
    const root = inspection[0].package_roots[0];
    inspection[0].package_roots = [root];
    root.outdated.skipped = 3;
    root.outdated.packages = [{ name: 'Example.Package', ecosystem: 'python', current: '1.0.0', latest: '2.0.0' }];
    root.security.packages[0] = { ...root.security.packages[0], name: 'example_package', ecosystem: 'python', current: '1.0.0' };
    current.app.callServerTool = async () => ({ content: [{ type: 'text', text: JSON.stringify(inspection) }] });
    await current.click(current.get('tab-dependencies'));
    const selection = current.contexts.at(-1).structuredContent.selection;
    assert.equal(selection.findings.length, 1);
    assert.equal(selection.findings[0].latest, '2.0.0');
    assert.equal(selection.findings[0].advisories.length, 1);
    assert.equal(selection.coverage[0].outdated.incomplete, 0);
    assert.doesNotMatch(selection.body, /skipped/);
    assert.match(current.get('item-meta').textContent, /Last checked/);
});
