#!/usr/bin/env python3
"""
Resume publishing backlog issues starting after the last entry in backlog/publish.log.
This avoids fetching all existing issues and creates remaining issues using the same
GitHub request helpers from generate_issue_backlog.py.

Usage: python3 scripts/resume_publish_from_log.py
"""
import importlib.util
import json
from pathlib import Path
import sys

ROOT = Path(__file__).resolve().parent.parent
BACKLOG = ROOT / 'backlog'
LOG = BACKLOG / 'publish.log'


def last_created_title(log_path: Path) -> str | None:
    if not log_path.exists():
        return None
    last = None
    for line in log_path.read_text(encoding='utf-8').splitlines():
        line = line.strip()
        if line.startswith('Created issue: '):
            last = line[len('Created issue: '):]
    return last


def main():
    spec = importlib.util.spec_from_file_location('gen', str(ROOT / 'scripts' / 'generate_issue_backlog.py'))
    mod = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(mod)

    last_title = last_created_title(LOG)
    issues_path = BACKLOG / 'github_issues.json'
    milestones_path = BACKLOG / 'github_milestones.json'

    if not issues_path.exists():
        print('Missing backlog/github_issues.json')
        sys.exit(1)

    issues = json.loads(issues_path.read_text(encoding='utf-8'))
    milestones = json.loads(milestones_path.read_text(encoding='utf-8')) if milestones_path.exists() else []

    start_index = 0
    if last_title:
        for i, it in enumerate(issues):
            if it['title'] == last_title:
                start_index = i + 1
                break
        else:
            print('Last created title not found in backlog; starting from index 0')

    print(f'Resuming publish from issue index {start_index} (0-based). Remaining: {len(issues)-start_index}')

    # Ensure milestones exist (GETs are fast)
    existing_ms = mod.github_request('/milestones?state=all') or []
    ms_map = {m.get('title'): m.get('number') for m in existing_ms}
    for ms in milestones:
        if ms['title'] in ms_map:
            continue
        created = mod.github_request('/milestones', method='POST', data={
            'title': ms['title'], 'state': ms['state'], 'description': ms.get('description', '')
        })
        if created:
            ms_map[ms['title']] = created['number']
            print('Created milestone:', ms['title'])

    created = 0
    failed = 0
    for it in issues[start_index:]:
        payload = {'title': it['title'], 'body': it['body'], 'labels': it.get('labels', [])}
        ms_num = ms_map.get(it.get('milestone'))
        if ms_num:
            payload['milestone'] = ms_num
        res = mod.github_request('/issues', method='POST', data=payload)
        if res is not None:
            print('Created issue:', it['title'])
            created += 1
        else:
            print('Failed to create issue:', it['title'])
            failed += 1
        # github_request enforces POST delay; we keep that behavior

    print(f'Done. created={created} failed={failed}')


if __name__ == '__main__':
    main()
