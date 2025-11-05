# Team Task Management System

A collaboration platform for Kenyan institutions, SACCOs, and organizations. Laravel 12 + Docker. Includes real-time chat, video conferencing (Janus SFU), task management, contributions/collections, and M-Pesa payments.

## Key Capabilities

- Auth & Approvals: Institution-controlled users; invitations and approvals
- Chat: Groups, file sharing
- Meetings: Janus SFU, waiting room, host controls, recordings, transcripts, add-to-calendar (ICS/Google/Outlook)
- Tasks: CRUD with comments, attachments
- Contributions & Campaigns: Recurring rules, pledges, receipts, exports
- Payments: M-Pesa integration with webhook verification and idempotency; retry dashboard
- Admin & Ops: Health dashboard, metrics, activity log, backups, deploy verification

## Tech Stack

- Backend: Laravel 12 (PHP 8.2+)
- DB: MySQL 8.0
- Cache/Queue: Redis 7.2
- Realtime: Laravel Reverb
- Video: Janus Gateway (SFU)
- Frontend: Blade, JS, Tailwind CSS
- Containerization: Docker & Docker Compose

## Quick Start

```bash
# 1) Clone and build
git clone <repository-url>
cd Team-Task-Management-System
Docker build -t ttms_app:latest .

# 2) Configure env
cp .env.example .env.docker
# edit .env.docker: DB_*, APP_URL, MPESA_*, JANUS_*, REVERB_*

# 3) Initialize
docker compose run --rm init_app
docker compose up -d
docker compose exec app php artisan migrate --force

# 4) Access
# app:     http://localhost:8000
# health:  http://localhost:8000/healthz
```

For complete setup, see SETUP.md.

## Project Structure

```
app/            # Controllers, Models, Policies, Events, Services, Providers
bootstrap/
database/       # migrations, seeders
resources/      # Blade templates & assets
routes/         # web.php
```

## Useful Admin Routes

- Health: /admin/health
- Metrics: /admin/metrics
- Deploy Verify: /admin/deploy/verify
- Webhooks: /admin/payments/webhooks
- Recurring Rules: /admin/recurring-rules
- SACCO Role Templates: /admin/sacco-role-templates
- Group Ledger Export: /admin/groups/{group}/ledger

## Environment

- APP_KEY: Laravel key (php artisan key:generate)
- APP_URL: Base URL used in links and verification checks
- DB_*: MySQL
- REVERB_*: WebSocket
- JANUS_*: Janus Gateway
- MPESA_*: Daraja API + MPESA_WEBHOOK_SECRET
- BACKUP_DISK: local, s3, etc.

## Operations

- Backups: daily DB dump at 02:00; manual `php artisan backup:db --disk=local`
- Deploy check: `php artisan deploy:verify --base-url=$APP_URL`
- Metrics: Prometheus format at /admin/metrics

## Remaining Features

See REMAINING_FEATURES.md.

## License

MIT
