#!/usr/bin/env python3
"""Build the plugin ZIP and separate review kit without using a live workspace."""

import argparse
import hashlib
import json
from pathlib import Path
import zipfile

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / 'plugins/orbit-browser'


def write_zip(destination, files):
    with zipfile.ZipFile(destination, 'w', compression=zipfile.ZIP_DEFLATED) as archive:
        for name, source in sorted(files.items()):
            if source.is_symlink():
                raise ValueError(f'Symlinks cannot be packaged: {source}')
            info = zipfile.ZipInfo(name, date_time=(1980, 1, 1, 0, 0, 0))
            info.compress_type = zipfile.ZIP_DEFLATED
            info.create_system = 3
            info.external_attr = (0o100755 if name.endswith('.sh') else 0o100644) << 16
            archive.writestr(info, source.read_bytes())


def build(output):
    manifest = json.loads((PLUGIN / '.codex-plugin/plugin.json').read_text())
    review = manifest['extensions']['com.openai']['review']
    assert len(review['test_cases']['positive']) == 5
    assert len(review['test_cases']['negative']) == 3
    interface = manifest['interface']
    assert len(interface['displayName']) <= 30
    assert len(interface['shortDescription']) <= 30
    assert len(interface['longDescription']) <= 4000
    for case in review['test_cases']['positive']:
        assert all(case.get(key) for key in ['description', 'prompt', 'tools_triggered', 'expected_behavior'])
    for key in ['composerIcon', 'composerIconDark', 'logo', 'logoDark']:
        assert (PLUGIN / interface[key]).is_file()
    files = {name: PLUGIN / name for name in ['.codex-plugin/plugin.json', '.mcp.json', 'README.md']}
    for folder in ['assets', 'scripts', 'src', 'views']:
        for source in sorted((PLUGIN / folder).rglob('*')):
            if source.is_file() and source.suffix in {'.svg', '.js', '.mjs', '.php', '.sh'}:
                files[source.relative_to(PLUGIN).as_posix()] = source
    assert all(source.is_file() for source in files.values())
    output.mkdir(parents=True, exist_ok=True)
    version = manifest['version']
    plugin_zip = output / f'orbit-browser-{version}.zip'
    write_zip(plugin_zip, files)
    demo_zip = output / 'orbit-browser-demo-workspace.zip'
    write_zip(demo_zip, {'workspace.json': ROOT / 'submission/workspace.json'})
    listing = output / 'listing-copy.md'
    listing.write_text(f"# {interface['displayName']}\n\n{interface['shortDescription']}\n\n{interface['longDescription']}\n\n"
                       + '\n'.join(f"- {key}: {interface[key]}" for key in ['developerName', 'category', 'websiteURL', 'supportURL', 'privacyPolicyURL'])
                       + '\n\nStarter prompts:\n\n' + '\n'.join(f'- {prompt}' for prompt in interface['defaultPrompt']) + '\n')
    readiness = output / 'submission-status.json'
    readiness.write_text(json.dumps({
        'version': version, 'ready_for_submission': False,
        'pending': [
            'Confirm approved-partner local MCP allowlisting and verified publishing identity.',
            'Approve legal drafts, fill publisher details and retention decisions, and publish them.',
            'Add a working termsOfServiceURL and the final privacyPolicyURL to the manifest, then rebuild.',
            'Record the actual desktop workflows using the demo workspace and add review.demo_recording_url, then rebuild.',
            'Complete clean native desktop installation and reviewer-case sign-off; verify dark theme and saved dependency findings.',
        ],
        'submitted': False, 'published_to_directory': False,
    }, indent=2) + '\n')
    kit_files = {item.name: item for item in [plugin_zip, demo_zip, listing, readiness]}
    kit_files.update({source.name: source for source in (ROOT / 'submission').glob('*.md')})
    kit = output / f'orbit-browser-submission-{version}.zip'
    write_zip(kit, kit_files)
    artifacts = [plugin_zip, demo_zip, kit]
    (output / 'SHA256SUMS').write_text(''.join(f'{hashlib.sha256(item.read_bytes()).hexdigest()}  {item.name}\n' for item in artifacts))
    for item in artifacts:
        print(item)
    print('Prepared draft artifacts. See submission-status.json before submitting.')


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--output', type=Path, default=ROOT / 'dist')
    build(parser.parse_args().output)
