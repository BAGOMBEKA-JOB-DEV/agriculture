import csv
import json
import os
import random
import subprocess
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path
from typing import List, Dict, Any

ROOT = Path(__file__).resolve().parent.parent
BACKLOG_DIR = ROOT / 'backlog'
BACKLOG_DIR.mkdir(exist_ok=True)

FEATURE_AREAS = [
    {
        'slug': 'auth',
        'name': 'Authentication & Accounts',
        'backend': 'Authentication, account lifecycle, password reset, session handling, 2FA',
        'frontend': 'Login, registration, forgot/reset password, profile, two-factor UI',
        'completed': 'Registration, login, password reset, profile update, avatar upload, and 2FA challenge flow are present.',
        'remaining': 'Improve UX, validation, recovery flows, accessibility, and end-to-end tests.'
    },
    {
        'slug': 'marketplace',
        'name': 'Marketplace Catalog',
        'backend': 'Product listing, search, filters, public discovery, subscription gating',
        'frontend': 'Marketplace browsing, search, product cards, filters, detail pages',
        'completed': 'Products can be listed, filtered, searched, and viewed publicly with subscription-aware masking.',
        'remaining': 'Add richer filters, better product detail experience, pagination, and SEO.'
    },
    {
        'slug': 'inventory',
        'name': 'Vendor Inventory',
        'backend': 'Vendor product CRUD, inventory lifecycle, moderation hooks',
        'frontend': 'Inventory dashboards, create/edit forms, status controls',
        'completed': 'Vendors can create, edit, and manage products through API-backed flows.',
        'remaining': 'Add bulk import/export, stock tracking, and better validation.'
    },
    {
        'slug': 'rfq',
        'name': 'RFQ Workflow',
        'backend': 'RFQ creation, seller responses, message exchange, attachment handling',
        'frontend': 'RFQ list, create form, detail view, negotiation UI',
        'completed': 'RFQ creation, status updates, and message-based negotiation are implemented.',
        'remaining': 'Add history, templates, negotiation states, and stronger workflow automation.'
    },
    {
        'slug': 'subscriptions',
        'name': 'Subscriptions',
        'backend': 'Subscription plans, eligibility checks, active subscription status',
        'frontend': 'Plans, checkout, upgrade/downgrade UX, access gating',
        'completed': 'Subscription plans and status checks exist in the backend and UI.',
        'remaining': 'Add payments, plan comparisons, renewals, and billing history.'
    },
    {
        'slug': 'messaging',
        'name': 'Messaging',
        'backend': 'Conversation endpoints, read/unread updates, forwarding, attachments',
        'frontend': 'Inbox, conversation thread, attachment previews, message composer',
        'completed': 'Core messaging routes and message storage exist.',
        'remaining': 'Improve real-time updates, notification sync, and message threading.'
    },
    {
        'slug': 'notifications',
        'name': 'Notifications',
        'backend': 'Notification listing, reading, broadcast support',
        'frontend': 'Inbox UI, mark-as-read, notification detail pages',
        'completed': 'Notifications can be listed and marked read, with admin broadcast support.',
        'remaining': 'Add push-style delivery, preferences, and richer templates.'
    },
    {
        'slug': 'admin',
        'name': 'Admin Operations',
        'backend': 'Admin moderation, user management, product approval workflow',
        'frontend': 'Admin dashboards, user management tables, moderation screens',
        'completed': 'Admin endpoints and UI screens for moderation and user operations exist.',
        'remaining': 'Add audit trails, bulk operations UX, and reporting.'
    },
    {
        'slug': 'superadmin',
        'name': 'Super Admin Governance',
        'backend': 'Governance stats, role management, user exports, impersonation',
        'frontend': 'Governance dashboard, role management, user administration',
        'completed': 'Super-admin governance routes and management UI are present.',
        'remaining': 'Strengthen compliance workflows, export quality, and governance analytics.'
    },
    {
        'slug': 'roles',
        'name': 'RBAC & Roles',
        'backend': 'Dynamic permissions, role seeding, role assignment',
        'frontend': 'Role management screens and permission-aware navigation',
        'completed': 'RBAC core models and seeders are implemented.',
        'remaining': 'Add role inheritance, policy reviews, and permission audit pages.'
    },
    {
        'slug': 'agripulse',
        'name': 'AgriPulse Social Feed',
        'backend': 'Posts, reactions, comments, uploads, reposts, reports, saved posts',
        'frontend': 'Feed, profile, saved posts, moderation views',
        'completed': 'Core AgriPulse social feed and moderation APIs exist.',
        'remaining': 'Improve feed ranking, moderation workflows, and social UX.'
    },
    {
        'slug': 'moderation',
        'name': 'Content Moderation',
        'backend': 'Moderation logs, hide/delete/restore actions, reports',
        'frontend': 'Moderation queues and action screens',
        'completed': 'Moderation endpoints and logs are in place.',
        'remaining': 'Add workflow automation, reviewer queues, and escalation rules.'
    },
    {
        'slug': 'support',
        'name': 'Support Center',
        'backend': 'Knowledge base, tickets, admin assignment, reporting',
        'frontend': 'User support portal and agent dashboards',
        'completed': 'Support ticket and knowledge base foundations are present.',
        'remaining': 'Add SLA automation, canned responses, and richer analytics.'
    },
    {
        'slug': 'reports',
        'name': 'Reporting & Analytics',
        'backend': 'Vendor, buyer, admin, and system reporting endpoints',
        'frontend': 'Reporting dashboards and charts',
        'completed': 'Reporting endpoints exist for several roles.',
        'remaining': 'Expand charting, exports, drill-down filters, and scheduled reporting.'
    },
    {
        'slug': 'security',
        'name': 'Security & Compliance',
        'backend': '2FA, password reset, activity logs, signed exports',
        'frontend': 'Security settings, recovery codes, verification UI',
        'completed': '2FA, password reset, and activity logging are partially implemented.',
        'remaining': 'Add stronger auditing, breach detection, and policy enforcement.'
    },
    {
        'slug': 'performance',
        'name': 'Performance & Scalability',
        'backend': 'Efficient queries, queue workers, caching opportunities',
        'frontend': 'Lazy loading, component optimization, bundle reduction',
        'completed': 'The app has a modular structure and queue-based background jobs.',
        'remaining': 'Profile and optimize hotspots, caching, and asset delivery.'
    },
    {
        'slug': 'quality',
        'name': 'Testing & Quality Assurance',
        'backend': 'Feature and unit tests, regression coverage',
        'frontend': 'Component and integration tests',
        'completed': 'The project has test scaffolding and PHPUnit setup.',
        'remaining': 'Expand automated test coverage across backend and frontend flows.'
    },
    {
        'slug': 'docs',
        'name': 'Documentation & Dev Experience',
        'backend': 'Setup docs, API docs, developer conventions',
        'frontend': 'Component documentation, route conventions, onboarding guides',
        'completed': 'The repository includes setup documentation and a project plan.',
        'remaining': 'Create more complete architecture docs and contributor guides.'
    },
    {
        'slug': 'devops',
        'name': 'Deployment & Operations',
        'backend': 'Environment config, migrations, queue workers, storage',
        'frontend': 'Build pipeline, asset deployment, static hosting',
        'completed': 'The project has Laravel and Vite build tooling.',
        'remaining': 'Add CI/CD, staging environments, and deployment automation.'
    },
    {
        'slug': 'search',
        'name': 'Search & Discovery',
        'backend': 'Search endpoints, category and content search',
        'frontend': 'Search experience, suggestions, results pages',
        'completed': 'Search endpoints exist for the marketplace.',
        'remaining': 'Add full-text search, faceted search, and better relevance.'
    },
    {
        'slug': 'network',
        'name': 'Network Directory',
        'backend': 'Vendor, trader, and buyer directory APIs',
        'frontend': 'Directory pages and profile navigation',
        'completed': 'Network directory endpoints exist.',
        'remaining': 'Improve profiles, filters, and discovery UX.'
    },
    {
        'slug': 'payments',
        'name': 'Payments & Billing',
        'backend': 'Payment models and subscription hooks',
        'frontend': 'Checkout and billing UI',
        'completed': 'Payment and subscription models exist.',
        'remaining': 'Integrate real payment processing and invoice management.'
    },
    {
        'slug': 'files',
        'name': 'File Handling & Media',
        'backend': 'Avatar, post images, attachment storage',
        'frontend': 'Upload UI, previews, validation',
        'completed': 'Core file upload support is present.',
        'remaining': 'Add image optimization, virus scanning, and media library features.'
    },
    {
        'slug': 'data',
        'name': 'Data Models & Integrity',
        'backend': 'Database schema, relationships, migration quality',
        'frontend': 'Data-driven forms and forms validation',
        'completed': 'The core data model exists for users, products, RFQs, and social content.',
        'remaining': 'Add data cleanup scripts, constraints, and seed quality improvements.'
    },
    {
        'slug': 'ux',
        'name': 'User Experience & Accessibility',
        'backend': 'APIs that support richer client UX',
        'frontend': 'Responsive pages, keyboard navigation, accessibility',
        'completed': 'The app has a multi-page SPA structure.',
        'remaining': 'Improve accessibility, responsive design, and interaction polish.'
    },
    {
        'slug': 'analytics',
        'name': 'Business Analytics',
        'backend': 'Stats and reporting endpoints',
        'frontend': 'Dashboard widgets and KPI panels',
        'completed': 'Basic stats and reporting endpoints are present.',
        'remaining': 'Add deeper insights, trends, and real-time dashboards.'
    },
    {
        'slug': 'workflow',
        'name': 'Workflow Automation',
        'backend': 'Background jobs, triggers, status transitions',
        'frontend': 'Action buttons, workflow states',
        'completed': 'Background jobs and status transitions exist.',
        'remaining': 'Add automation rules, approvals, and event-driven steps.'
    },
    {
        'slug': 'internationalization',
        'name': 'Localization & Internationalization',
        'backend': 'Locale-aware APIs and messages',
        'frontend': 'Multi-language UI',
        'completed': 'The app uses Laravel localization conventions.',
        'remaining': 'Add complete multilingual support and locale switching.'
    },
    {
        'slug': 'observability',
        'name': 'Observability & Monitoring',
        'backend': 'Logs, metrics, tracing',
        'frontend': 'Error monitoring and performance tracking',
        'completed': 'The app has logging and queue support.',
        'remaining': 'Implement monitoring dashboards, alerting, and tracing.'
    },
    {
        'slug': 'compliance',
        'name': 'Compliance & Governance',
        'backend': 'Audit logs and governance controls',
        'frontend': 'Compliance views and controls',
        'completed': 'Governance and activity logging are partly implemented.',
        'remaining': 'Add formal compliance workflows and policy management.'
    },
    {
        'slug': 'integration',
        'name': 'Third-Party Integrations',
        'backend': 'External service hooks',
        'frontend': 'Connected service experiences',
        'completed': 'The project is structured for service integration.',
        'remaining': 'Add integrations for payments, messaging, and analytics platforms.'
    },
    {
        'slug': 'content',
        'name': 'Content Management',
        'backend': 'Announcements, support content, knowledge base',
        'frontend': 'Content pages and content editing UX',
        'completed': 'Announcement and knowledge base infrastructure exists.',
        'remaining': 'Add richer content authoring and publishing workflows.'
    },
    {
        'slug': 'mobile',
        'name': 'Mobile Readiness',
        'backend': 'Mobile-friendly API design',
        'frontend': 'Responsive and touch-friendly interfaces',
        'completed': 'The UI is responsive in structure.',
        'remaining': 'Add mobile-specific UX improvements and PWA support.'
    },
    {
        'slug': 'api',
        'name': 'API Platform',
        'backend': 'REST endpoints, request validation, API structure',
        'frontend': 'Client-side API consumption patterns',
        'completed': 'The app exposes a broad set of REST endpoints.',
        'remaining': 'Add API versioning, docs, rate limiting, and contract testing.'
    },
]

TEMPLATE_TYPES = [
    'implementation',
    'validation',
    'testing',
    'performance',
    'security',
    'accessibility',
    'documentation',
    'observability',
    'refactor',
    'production-readiness',
    'data-quality',
    'integration',
    'ux-polish',
    'compliance',
    'automation',
    'hardening',
    'analytics',
    'scalability',
    'api-contract',
    'migration',
]

LAYER_TYPES = [
    ('backend', 'Backend'),
    ('frontend', 'Frontend'),
    ('fullstack', 'Full Stack'),
]

ISSUE_BUCKETS = [
    ('completed', 'Completed / Verification'),
    ('remaining', 'Remaining / Implementation'),
]


def build_title(feature: Dict[str, Any], layer: str, bucket: str, template: str) -> str:
    prefix = '[Completed]' if bucket == 'completed' else '[Remaining]'
    return f"{prefix} {feature['name']} - {layer.title()} - {template.replace('-', ' ').title()}"


def build_body(feature: Dict[str, Any], layer: str, bucket: str, template: str) -> str:
    if layer == 'fullstack':
        layer_desc = f"Backend: {feature['backend']} | Frontend: {feature['frontend']}"
    else:
        layer_desc = feature[layer]
    summary = feature['completed'] if bucket == 'completed' else feature['remaining']

    if template == 'implementation':
        detail = f"Implement or verify the {feature['name'].lower()} capabilities in the {layer} layer."
    elif template == 'validation':
        detail = f"Validate business rules, request handling, and expected user flows for {feature['name'].lower()}."
    elif template == 'testing':
        detail = f"Add or extend automated tests for the {feature['name'].lower()} workflow in the {layer} layer."
    elif template == 'performance':
        detail = f"Improve performance and efficiency for the {feature['name'].lower()} feature area."
    elif template == 'security':
        detail = f"Review and harden the security posture of {feature['name'].lower()} across the {layer} layer."
    elif template == 'accessibility':
        detail = f"Improve accessibility and inclusive interaction for {feature['name'].lower()} in the {layer} layer."
    elif template == 'documentation':
        detail = f"Document the current behavior, expected usage, and implementation notes for {feature['name'].lower()}."
    elif template == 'observability':
        detail = f"Add or improve monitoring, logging, and visibility for {feature['name'].lower()}."
    elif template == 'refactor':
        detail = f"Refactor the {feature['name'].lower()} implementation to improve maintainability and consistency."
    elif template == 'production-readiness':
        detail = f"Prepare {feature['name'].lower()} for production release with deployment, resilience, and operations work."
    elif template == 'data-quality':
        detail = f"Improve data quality, integrity, and consistency for {feature['name'].lower()}."
    elif template == 'integration':
        detail = f"Integrate {feature['name'].lower()} with external systems or internal services."
    elif template == 'ux-polish':
        detail = f"Polish the user experience and interaction quality for {feature['name'].lower()}."
    elif template == 'compliance':
        detail = f"Review and enhance compliance and governance controls around {feature['name'].lower()}."
    elif template == 'automation':
        detail = f"Automate repetitive tasks and workflows for {feature['name'].lower()}."
    elif template == 'hardening':
        detail = f"Harden the implementation of {feature['name'].lower()} against runtime and operational risks."
    elif template == 'analytics':
        detail = f"Add insight, metrics, and reporting for {feature['name'].lower()}."
    elif template == 'scalability':
        detail = f"Improve the scalability of {feature['name'].lower()} for larger workloads."
    elif template == 'api-contract':
        detail = f"Define, document, and validate the API contract for {feature['name'].lower()}."
    elif template == 'migration':
        detail = f"Plan and execute data or platform migration activities for {feature['name'].lower()}."
    else:
        detail = f"Address the {feature['name'].lower()} backlog item in the {layer} layer."

    state = 'closed' if bucket == 'completed' else 'open'
    action = 'verify and close' if bucket == 'completed' else 'implement and ship'

    domain_focus = feature['backend'] if layer == 'backend' else feature['frontend'] if layer == 'frontend' else f"{feature['backend']} / {feature['frontend']}"

    return f"""## Summary
{summary}

## Feature area
- Name: {feature['name']}
- Domain focus: {domain_focus}
- Layer: {layer.title()}
- Bucket: {bucket.replace('-', ' ').title()}
- State: {state.upper()}

## Detailed description
This issue captures the {bucket} backlog work for {feature['name'].lower()} in the {layer} layer. It exists to preserve a clear implementation trail for both shipped behavior and planned enhancements across the agricultural marketplace platform. The task should be treated as a concrete product and engineering activity that can be reviewed, estimated, tested, and completed independently.

## Business context
The platform already includes substantial functionality for {feature['name'].lower()}, but the implementation still needs disciplined follow-through in the areas of correctness, resilience, completeness, and user experience. This issue should ensure that the work is documented in a way that supports developers, QA, and product owners during delivery.

## Scope of work
1. Review the current implementation or product gap for this area.
2. {detail}
3. Validate the behavior against the expected product rules and framework conventions.
4. Add or update tests, documentation, observability, or validation helpers where appropriate.
5. Prepare the work for review, handoff, or production readiness.

## Acceptance criteria
- The required behavior is implemented or verified for the relevant layer.
- The change is aligned with the current Laravel backend structure, Vue frontend patterns, or the combined full-stack experience.
- Any supporting tests, docs, or validation steps are updated accordingly.
- The work is ready for review, QA, or release planning.

## Implementation note
This issue should be handled as part of the broader roadmap for the agricultural marketplace platform and should {action} with clear ownership, measurable validation, and traceable outcomes.
"""


def build_issue(feature: Dict[str, Any], layer: str, bucket: str, template: str, index: int) -> Dict[str, Any]:
    state = 'closed' if bucket == 'completed' else 'open'
    return {
        'number': index,
        'title': build_title(feature, layer, bucket, template),
        'body': build_body(feature, layer, bucket, template),
        'labels': [
            feature['slug'],
            layer,
            bucket,
            template,
            'generated-by-script',
        ],
        'milestone': feature['name'],
        'state': state,
        'assignees': [],
    }


def generate_issues(limit: int = 3600) -> List[Dict[str, Any]]:
    issues = []
    index = 1
    for feature in FEATURE_AREAS:
        for bucket, _ in ISSUE_BUCKETS:
            for layer, _ in LAYER_TYPES:
                for template in TEMPLATE_TYPES:
                    if index > limit:
                        return issues
                    issues.append(build_issue(feature, layer, bucket, template, index))
                    index += 1
    return issues


def build_milestones() -> List[Dict[str, Any]]:
    milestones = []
    for index, feature in enumerate(FEATURE_AREAS, start=1):
        milestones.append({
            'number': index,
            'title': feature['name'],
            'description': (
                f"Backlog and delivery tracking for the {feature['name'].lower()} domain across backend, frontend, and full-stack work. "
                f"This milestone groups completed verification tasks and remaining implementation items for the agricultural marketplace platform."
            ),
            'state': 'open',
        })
    return milestones


def write_outputs(issues: List[Dict[str, Any]], milestones: List[Dict[str, Any]]) -> None:
    milestones_path = BACKLOG_DIR / 'github_milestones.json'
    issues_json_path = BACKLOG_DIR / 'github_issues.json'
    issues_csv_path = BACKLOG_DIR / 'github_issues.csv'
    milestones_csv_path = BACKLOG_DIR / 'github_milestones.csv'

    milestones_path.write_text(json.dumps(milestones, indent=2), encoding='utf-8')
    issues_json_path.write_text(json.dumps(issues, indent=2), encoding='utf-8')

    with milestones_csv_path.open('w', newline='', encoding='utf-8') as handle:
        writer = csv.DictWriter(handle, fieldnames=['number', 'title', 'description', 'state'])
        writer.writeheader()
        for milestone in milestones:
            writer.writerow(milestone)

    with issues_csv_path.open('w', newline='', encoding='utf-8') as handle:
        writer = csv.DictWriter(handle, fieldnames=['number', 'title', 'body', 'labels', 'milestone', 'state', 'assignees'])
        writer.writeheader()
        for issue in issues:
            writer.writerow({
                'number': issue['number'],
                'title': issue['title'],
                'body': issue['body'],
                'labels': ','.join(issue['labels']),
                'milestone': issue['milestone'],
                'state': issue['state'],
                'assignees': ','.join(issue['assignees']),
            })

    readme_path = BACKLOG_DIR / 'README.md'
    readme_path.write_text(
        f"""# Backlog Generation Output

- Generated milestones: {len(milestones)}
- Generated issues: {len(issues)}
- Target minimum: 3500 issues
- Output files:
  - [github_milestones.json](github_milestones.json)
  - [github_milestones.csv](github_milestones.csv)
  - [github_issues.json](github_issues.json)
  - [github_issues.csv](github_issues.csv)
""",
        encoding='utf-8',
    )

    print(f'Wrote {len(milestones)} milestones to {milestones_path}')
    print(f'Wrote {len(issues)} issues to {issues_json_path}')
    print(f'Wrote {len(issues)} issues to {issues_csv_path}')


def get_github_auth() -> tuple[str | None, str | None]:
    token = os.getenv('GITHUB_TOKEN') or os.getenv('GH_TOKEN')
    if token:
        return token, os.getenv('GITHUB_USER') or 'x-access-token'

    credential_file = Path.home() / '.git-credentials'
    if credential_file.exists():
        for line in credential_file.read_text(encoding='utf-8').splitlines():
            line = line.strip()
            if not line.startswith('https://') or 'github.com' not in line:
                continue
            remainder = line[len('https://'):]
            if '@' not in remainder:
                continue
            userpass, host = remainder.rsplit('@', 1)
            if ':' in userpass:
                username, token = userpass.split(':', 1)
                return token, username
            return userpass, userpass

    try:
        completed = subprocess.run(
            ['git', 'credential', 'fill'],
            input='protocol=https\nhost=github.com\n',
            text=True,
            capture_output=True,
            check=False,
        )
        if completed.returncode == 0:
            values = {}
            for raw_line in completed.stdout.splitlines():
                if '=' in raw_line:
                    key, value = raw_line.split('=', 1)
                    values[key] = value
            if values.get('username') and values.get('password'):
                return values['password'], values['username']
    except FileNotFoundError:
        pass

    return None, None


def parse_github_error(exc: urllib.error.HTTPError) -> tuple[str, str]:
    body = exc.read().decode('utf-8', 'ignore')
    try:
        payload = json.loads(body)
        message = payload.get('message') or body
    except json.JSONDecodeError:
        message = body
    return body, message


REQUEST_DELAY_SECONDS = float(os.getenv('GITHUB_REQUEST_DELAY', '10'))
MAX_RETRY_WAIT_SECONDS = int(os.getenv('GITHUB_RETRY_WAIT_SECONDS', '120'))


def github_request(path: str, method: str = 'GET', data: Dict[str, Any] | None = None, retry: int = 5) -> Dict[str, Any] | None:
    token, _ = get_github_auth()
    if not token:
        print('No GitHub credentials available for publish step.')
        return None

    repo = os.getenv('GITHUB_REPOSITORY') or 'BAGOMBEKA-JOB-DEV/agriculture'
    url = f'https://api.github.com/repos/{repo}{path}'
    payload = None
    headers = {
        'Authorization': f'token {token}',
        'Accept': 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
    }

    if data is not None:
        payload = json.dumps(data).encode('utf-8')
        headers['Content-Type'] = 'application/json'

    # Only apply the per-request delay for non-GET methods (POST/PUT/etc.).
    # This lets GETs (listing existing issues/milestones) run quickly.
    if method.upper() != 'GET' and REQUEST_DELAY_SECONDS > 0:
        delay = REQUEST_DELAY_SECONDS + random.uniform(0, 3)
        time.sleep(delay)
    request = urllib.request.Request(url, data=payload, headers=headers, method=method)
    try:
        with urllib.request.urlopen(request, timeout=60) as response:
            body = response.read().decode('utf-8')
            return json.loads(body) if body else {}
    except urllib.error.HTTPError as exc:
        body, message = parse_github_error(exc)
        print(f'GitHub API error for {path}: {exc.code} {message}')
        if retry > 0 and exc.code in {403, 429} and ('abuse detection' in body.lower() or 'rate limit' in body.lower() or 'secondary rate limit' in body.lower()):
            wait = min(MAX_RETRY_WAIT_SECONDS, REQUEST_DELAY_SECONDS * 6)
            print(f'GitHub abuse/rate-limit response; waiting {wait}s and retrying ({retry} attempts left)...')
            time.sleep(wait)
            return github_request(path, method, data, retry - 1)

        if exc.code == 422 and method == 'POST' and 'validation failed' in body.lower() and 'already exists' in body.lower():
            print(f'GitHub duplicate object detected for {path}; skipping creation.')
            return {}
        return None


def get_existing_github_items(path: str, item_key: str) -> Dict[str, Dict[str, Any]]:
    existing = {}
    page = 1
    print(f'Scanning existing GitHub {item_key} from {path}...')
    while True:
        print(f'Fetching page {page} of existing GitHub {item_key}...')
        batch = github_request(f'{path}&per_page=100&page={page}')
        if not batch:
            break
        for item in batch:
            title = item.get('title')
            if title:
                existing[title] = item
        if len(batch) < 100:
            break
        page += 1
    return existing


def publish_to_github(issues: List[Dict[str, Any]], milestones: List[Dict[str, Any]]) -> None:
    token, _ = get_github_auth()
    if not token:
        print('No GitHub credentials available for publish step.')
        return

    existing_issues = get_existing_github_items('/issues?state=all', 'issues')
    existing_milestones = get_existing_github_items('/milestones?state=all', 'milestones')
    created_milestones = {title: item['number'] for title, item in existing_milestones.items()}

    print(f'Starting issue creation for {len(issues)} backlog issues...')
    for milestone in milestones:
        if milestone['title'] in created_milestones:
            continue

        payload = {
            'title': milestone['title'],
            'state': milestone['state'],
            'description': milestone['description'],
        }
        created = github_request('/milestones', method='POST', data=payload)
        if created:
            created_milestones[milestone['title']] = created['number']
            print(f'Created milestone: {milestone["title"]}')
        else:
            print(f'Warning: milestone creation failed for {milestone["title"]}; will retry later if needed.')

    created_count = 0
    skipped_count = 0
    failed_count = 0

    for issue in issues:
        if issue['title'] in existing_issues:
            skipped_count += 1
            continue

        payload = {
            'title': issue['title'],
            'body': issue['body'],
            'labels': issue['labels'],
        }
        milestone_number = created_milestones.get(issue['milestone'])
        if milestone_number is not None:
            payload['milestone'] = milestone_number

        created = github_request('/issues', method='POST', data=payload)
        if created:
            created_count += 1
            print(f'Created issue: {issue["title"]}')
        else:
            failed_count += 1
            print(f'Failed to create issue: {issue["title"]}')
            print('Pausing 300 seconds after failure to recover from secondary rate limit...')
            time.sleep(300)
        if REQUEST_DELAY_SECONDS > 0:
            print(f'Waiting {REQUEST_DELAY_SECONDS}s before creating the next issue...')
            time.sleep(REQUEST_DELAY_SECONDS)

    print(f'Publishing summary: created={created_count}, skipped={skipped_count}, failed={failed_count}')


if __name__ == '__main__':
    publish = '--publish' in sys.argv
    if len(sys.argv) > 1 and sys.argv[1] not in {'--publish'}:
        try:
            limit = int(sys.argv[1])
        except ValueError:
            print('Usage: python scripts/generate_issue_backlog.py [count] [--publish]')
            sys.exit(1)
    else:
        limit = 3600

    issues = generate_issues(limit)
    milestones = build_milestones()
    write_outputs(issues, milestones)
    if publish:
        publish_to_github(issues, milestones)
