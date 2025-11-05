# Setup Guide

This guide helps you run the Team Task Management System locally with Docker.

## Requirements
- Docker 24+
- Docker Compose v2+

## 1) Clone & Build
```bash
git clone <repository-url>
cd Team-Task-Management-System
docker build -t ttms_app:latest .
```

## 2) Configure Environment
```bash
cp .env.docker.example .env.docker
```
Edit `.env.docker` with your actual secrets:
- **DB_ROOT_PASSWORD**: Secure MySQL root password
- **DB_PASSWORD**: Secure database user password
- **APP_KEY**: Generate with `php artisan key:generate` (run in workspace container)
- **APP_URL**: http://localhost:8000
- **DB_HOST**: db (Docker service name)
- **REDIS_HOST**: redis (Docker service name)
- **REVERB_***: WebSocket configuration
- **JANUS_***: Video conferencing secrets
- **MPESA_***: M-Pesa Daraja API credentials
- **SMS_*****: SMS provider credentials
- **BACKUP_DISK**: local (or s3 if configured)

**Important**: Never commit `.env.docker` to git. It contains sensitive credentials.

## 3) Initialize
```bash
docker compose run --rm init_app
docker compose up -d
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --force
```

## 4) Access
- App: `http://localhost:8000`
- Health: `http://localhost:8000/healthz`
- Metrics (admin): `/admin/metrics`

## 5) Common Tasks
```bash
# Open a shell
docker compose exec app bash

# Run database migrations
docker compose exec app php artisan migrate

# Seed data
docker compose exec app php artisan db:seed

# Tail logs
docker compose logs -f app
```

## 6) Webhooks (M-Pesa)
- Expose `/api/webhooks/mpesa/callback` publicly
- Set MPESA_WEBHOOK_SECRET in environment

## 7) Janus & Reverb
- Ensure Janus Gateway is reachable at JANUS_URL / JANUS_WS_URL
- Configure Laravel Reverb for realtime events

## 8) Backups & Verification
- Daily DB backup at 02:00; manual: `php artisan backup:db --disk=local`
- Post-deploy verification: `php artisan deploy:verify --base-url=$APP_URL`

## 9) Admin Navigation
- Health: `/admin/health`
- Metrics: `/admin/metrics`
- Deploy Verify: `/admin/deploy/verify`
- Webhooks: `/admin/payments/webhooks`
- Recurring Rules: `/admin/recurring-rules`
- SACCO Role Templates: `/admin/sacco-role-templates`
- Group Ledger Export: `/admin/groups/{group}/ledger`

## Notes
- For production, configure HTTPS, secure secrets, S3 backups, and webhook IP allowlists.
