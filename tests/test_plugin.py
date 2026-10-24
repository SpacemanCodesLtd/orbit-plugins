import hashlib
import json
import os
from pathlib import Path
import shutil
import sqlite3
import subprocess
import tempfile
import unittest
import zipfile

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / 'plugins/orbit-browser'
APP = Path(os.environ.get('ORBIT_APP_PATH', '/Applications/Orbit.app'))
BUILD = APP / 'Contents/Resources/build'
PROJECT_ID = 'e38a342a-e071-4c13-9d03-f4b1caa8641b'


class PluginTests(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory(prefix='Orbit plugin test ')
        self.addCleanup(self.temp.cleanup)
        self.home = Path(self.temp.name).resolve()
        self.plugin = self.home / 'relocated plugin'
        shutil.copytree(PLUGIN, self.plugin)
        self.database = self.home / 'workspace #1.sqlite'
        self.env = dict(os.environ, HOME=str(self.home), ORBIT_APP_PATH=str(APP),
                        ORBIT_DATABASE_PATH=str(self.database))

    def run_server(self, requests, command=None):
        messages = [dict(jsonrpc='2.0', id=1, method='initialize', params={
            'protocolVersion': '2025-11-25', 'capabilities': {},
            'clientInfo': {'name': 'orbit-plugin-tests', 'version': '1'},
        }), dict(jsonrpc='2.0', method='notifications/initialized')]
        messages += [dict(jsonrpc='2.0', id=i + 2, **request) for i, request in enumerate(requests)]
        result = subprocess.run(command or ['/bin/sh', str(self.plugin / 'scripts/start.sh')],
                                input='\n'.join(map(json.dumps, messages)) + '\n',
                                text=True, capture_output=True, env=self.env, timeout=20)
        self.assertEqual(result.returncode, 0, result.stderr + result.stdout[:1000])
        self.assertEqual(result.stderr, '')
        return [json.loads(line) for line in result.stdout.splitlines()]

    def fixture(self):
        with sqlite3.connect(self.database) as db:
            db.executescript('''
                CREATE TABLE projects (id TEXT PRIMARY KEY, name TEXT, status TEXT,
                    description TEXT, scratchpad TEXT, revision INTEGER, position INTEGER,
                    asset_files TEXT, document_folders TEXT);
                CREATE TABLE project_folders (id TEXT, project_id TEXT, path TEXT, repository_id TEXT);
                CREATE TABLE repositories (id TEXT, project_id TEXT, name TEXT);
                CREATE TABLE tags (id TEXT, name TEXT);
                CREATE TABLE project_tag (project_id TEXT, tag_id TEXT);
                CREATE TABLE project_links (id TEXT, project_id TEXT, position INTEGER,
                    label TEXT, url TEXT, category TEXT, description TEXT, important INTEGER);
                CREATE TABLE project_documents (id TEXT, project_id TEXT, position INTEGER,
                    title TEXT, body TEXT, revision INTEGER, important INTEGER, folder_id TEXT);
                CREATE TABLE board_columns (id TEXT, project_id TEXT, name TEXT, color TEXT, position INTEGER);
                CREATE TABLE tasks (id TEXT, board_column_id TEXT, title TEXT,
                    description TEXT, position INTEGER, attachment_files TEXT);
                CREATE TABLE project_secrets (id TEXT, project_id TEXT, environment TEXT, name TEXT,
                    service TEXT, description TEXT, management_url TEXT, revision INTEGER);
                CREATE TABLE package_roots (id TEXT, project_folder_id TEXT, relative_path TEXT,
                    snapshot TEXT, outdated TEXT, security TEXT, dependency_check_token TEXT);
            ''')
            db.execute('INSERT INTO projects VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                       (PROJECT_ID, 'Example project', 'Live', 'Project description',
                        '# Notes\n\n**Useful** <script>alert(1)</script>', 7, 0, '[]', '[]'))
            db.execute('INSERT INTO board_columns VALUES (?, ?, ?, ?, ?)',
                       ('column', PROJECT_ID, 'To do', 'blue', 0))
            db.execute('INSERT INTO tasks VALUES (?, ?, ?, ?, ?, ?)',
                       ('task', 'column', 'Example task', '**Task details**', 0, '[]'))
            db.execute('INSERT INTO project_documents VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                       ('doc', PROJECT_ID, 0, 'Guide', '[Unsafe](javascript:alert(1))\n\n**Steps**', 2, 0, None))
            db.execute('INSERT INTO project_secrets VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                       ('secret', PROJECT_ID, 'Production', 'DO_NOT_SHARE', '', '', '', 1))
            db.execute('INSERT INTO project_folders VALUES (?, ?, ?, ?)',
                       ('folder', PROJECT_ID, str(self.home), None))
            db.execute('INSERT INTO package_roots VALUES (?, ?, ?, ?, ?, ?, ?)',
                       ('root', 'folder', '.', '{}', json.dumps({'packages': [{'name': 'vue'}]}), '{}', None))

    def test_missing_app_fails_without_creating_workspace(self):
        self.env['ORBIT_APP_PATH'] = str(self.home / 'missing.app')
        result = subprocess.run(['/bin/sh', str(self.plugin / 'scripts/start.sh')],
                                text=True, capture_output=True, env=self.env)
        self.assertEqual(result.returncode, 1)
        self.assertIn('needs Orbit installed', result.stderr)
        self.assertEqual(result.stdout, '')
        self.assertFalse(self.database.exists())

    def test_missing_workspace_fails_without_creating_database(self):
        result = subprocess.run(['/bin/sh', str(self.plugin / 'scripts/start.sh')],
                                text=True, capture_output=True, env=self.env)
        self.assertEqual(result.returncode, 1)
        self.assertIn('Open Orbit and create a workspace', result.stderr)
        self.assertEqual(result.stdout, '')
        self.assertFalse(self.database.exists())

    def test_relocated_plugin_reads_safe_content_and_refuses_write_tools(self):
        self.fixture()
        before = hashlib.sha256(self.database.read_bytes()).digest()
        responses = self.run_server([
            {'method': 'tools/list'},
            {'method': 'resources/read', 'params': {'uri': 'ui://orbit/browser-v0.3.6'}},
            {'method': 'tools/call', 'params': {'name': 'read-workspace-tool', 'arguments': {}}},
            {'method': 'tools/call', 'params': {'name': 'read-browser-content-tool', 'arguments': {'project_id': PROJECT_ID}}},
            {'method': 'tools/call', 'params': {'name': 'read-browser-content-tool', 'arguments': {'project_id': PROJECT_ID, 'section': 'inspection'}}},
            {'method': 'tools/call', 'params': {'name': 'manage-project-tool', 'arguments': {'action': 'delete', 'project_id': PROJECT_ID}}},
            {'method': 'tools/call', 'params': {'name': 'read-browser-content-tool', 'arguments': {'project_id': 'bad'}}},
            {'method': 'tools/call', 'params': {'name': 'read-browser-content-tool', 'arguments': {'project_id': PROJECT_ID, 'section': 'activity'}}},
            {'method': 'tools/call', 'params': {'name': 'read-browser-content-tool', 'arguments': {'project_id': '00000000-0000-4000-8000-000000000000'}}},
        ])
        results = {r['id']: r for r in responses}
        tools = results[2]['result']['tools']
        self.assertEqual({t['name'] for t in tools}, {'browse-orbit-tool', 'read-workspace-tool', 'read-browser-content-tool'})
        self.assertTrue(all(t['annotations']['readOnlyHint'] for t in tools))
        self.assertEqual(tools[0]['_meta']['openai/ui']['entrypoints'], [{'type': 'global'}])
        html = results[3]['result']['contents'][0]['text']
        for expected in ['Find a project', 'Start new chat', 'ui/initialize', 'currentDependencyCheck']:
            self.assertIn(expected, html)
        self.assertNotIn('file_get_contents', html)
        workspace = json.loads(results[4]['result']['content'][0]['text'])
        self.assertEqual(workspace[0]['id'], PROJECT_ID)
        content = json.loads(results[5]['result']['content'][0]['text'])
        self.assertIn('<strong>Useful</strong>', content['scratchpad_html'])
        self.assertNotIn('<script>', content['scratchpad_html'])
        self.assertIn('<strong>Steps</strong>', content['documents'][0]['body_html'])
        self.assertNotIn('javascript:', content['documents'][0]['body_html'])
        self.assertIn('<strong>Task details</strong>', content['board'][0]['tasks'][0]['description_html'])
        self.assertTrue({'secrets', 'repositories', 'folders'}.isdisjoint(content))
        self.assertNotIn('DO_NOT_SHARE', json.dumps(content))
        self.assertIn('vue', results[6]['result']['content'][0]['text'])
        self.assertIn('error', results[7])
        for response_id in [8, 9, 10]:
            self.assertTrue(results[response_id].get('error') or results[response_id]['result'].get('isError'))
        self.assertEqual(hashlib.sha256(self.database.read_bytes()).digest(), before)

    def test_database_refuses_writes_after_connect_and_reconnect(self):
        self.fixture()
        responses = self.run_server([
            {'method': 'tools/call', 'params': {'name': 'read-workspace-tool', 'arguments': {}}},
        ], command=[str(BUILD / 'php/php'), str(ROOT / 'tests/read-only-probe.php'),
                    str(BUILD / 'app'), str(self.plugin / 'scripts/server.php')])
        result = json.loads(responses[-1]['result']['content'][0]['text'])
        self.assertEqual(result, ['readonly', 'readonly'])

    def test_release_zip_starts_after_extraction_and_demo_archive_passes_orbit_preview(self):
        output = self.home / 'release output'
        build = [shutil.which('python3'), str(ROOT / 'scripts/package-release.py'), '--output', str(output)]
        subprocess.run(build, check=True, capture_output=True, text=True)
        manifest = json.loads((PLUGIN / '.codex-plugin/plugin.json').read_text())
        archive_path = output / f"orbit-browser-{manifest['version']}.zip"
        digest = hashlib.sha256(archive_path.read_bytes()).hexdigest()
        subprocess.run(build, check=True, capture_output=True, text=True)
        self.assertEqual(hashlib.sha256(archive_path.read_bytes()).hexdigest(), digest)
        self.assertIn(digest, (output / 'SHA256SUMS').read_text())
        with zipfile.ZipFile(archive_path) as archive:
            names = archive.namelist()
            self.assertIn('.codex-plugin/plugin.json', names)
            self.assertIn('scripts/start.sh', names)
            self.assertIn('views/browser.blade.php', names)
            self.assertFalse(any(name.startswith(('submission/', 'tests/', '.git/')) or name.endswith(('.sqlite', '.env')) for name in names))
            self.plugin = self.home / 'extracted release'
            archive.extractall(self.plugin)
        self.fixture()
        responses = self.run_server([
            {'method': 'resources/read', 'params': {'uri': f"ui://orbit/browser-v{manifest['version']}"}},
            {'method': 'tools/list'},
        ])
        self.assertIn('Start new chat', responses[1]['result']['contents'][0]['text'])
        self.assertEqual(responses[0]['result']['serverInfo']['version'], manifest['version'])
        self.assertTrue(all(tool['annotations']['readOnlyHint'] for tool in responses[2]['result']['tools']))
        readiness = json.loads((output / 'submission-status.json').read_text())
        self.assertFalse(readiness['ready_for_submission'])
        self.assertFalse(readiness['submitted'])
        probe = self.home / 'archive-preview.php'
        probe.write_text('''<?php
namespace { require $argv[1]."/vendor/autoload.php"; }
namespace App\\Mcp\\Tools {
    class ReadWorkspaceTool extends \\Laravel\\Mcp\\Server\\Tool {
        public function handle(\\Laravel\\Mcp\\Request $request): \\Laravel\\Mcp\\Response {
            return \\Laravel\\Mcp\\Response::json(app(\\App\\WorkspaceArchive::class)->preview(getenv("ORBIT_REVIEW_ARCHIVE")));
        }
    }
}
namespace { require $argv[2]; }
''')
        self.env['ORBIT_REVIEW_ARCHIVE'] = str(output / 'orbit-browser-demo-workspace.zip')
        responses = self.run_server([
            {'method': 'tools/call', 'params': {'name': 'read-workspace-tool', 'arguments': {}}},
        ], command=[str(BUILD / 'php/php'), str(probe), str(BUILD / 'app'), str(self.plugin / 'scripts/server.php')])
        preview = json.loads(responses[-1]['result']['content'][0]['text'])
        self.assertEqual({key: preview[key] for key in ['projects', 'tasks', 'documents', 'files']},
                         {'projects': 1, 'tasks': 2, 'documents': 1, 'files': 0})

    @unittest.skipUnless(shutil.which('codex'), 'Codex CLI is needed for the marketplace install test')
    def test_fresh_marketplace_install_runs_from_codex_cache(self):
        self.fixture()
        self.env['CODEX_HOME'] = str(self.home / '.codex')
        Path(self.env['CODEX_HOME']).mkdir()
        for command in [
            ['codex', 'plugin', 'marketplace', 'add', str(ROOT), '--json'],
            ['codex', 'plugin', 'add', 'orbit-browser@orbit-beta', '--json'],
        ]:
            result = subprocess.run(command, env=self.env, text=True, capture_output=True, timeout=30)
            self.assertEqual(result.returncode, 0, result.stderr)
        installed_manifest = json.loads(result.stdout)
        manifest = json.loads((PLUGIN / '.codex-plugin/plugin.json').read_text())
        self.assertEqual(installed_manifest['version'], manifest['version'])
        servers = json.loads(subprocess.check_output(['codex', 'mcp', 'list', '--json'], env=self.env))
        transport = next(s['transport'] for s in servers if s['name'] == 'orbit-browser')
        installed = Path(transport['cwd']).resolve()
        self.assertTrue(installed.is_relative_to(self.home / '.codex/plugins/cache'))
        self.assertNotEqual(installed, PLUGIN)
        self.plugin = installed
        responses = self.run_server([
            {'method': 'tools/call', 'params': {'name': 'read-workspace-tool', 'arguments': {}}},
        ], command=[transport['command'], str(installed / transport['args'][0])])
        projects = json.loads(responses[-1]['result']['content'][0]['text'])
        self.assertEqual(projects[0]['name'], 'Example project')


if __name__ == '__main__':
    unittest.main()
