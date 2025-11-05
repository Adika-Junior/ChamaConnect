# Secret Rotation Playbook

This document outlines the procedure for rotating secrets in the Team Task Management System.

## Overview

Regular secret rotation is critical for security. Rotate secrets at least quarterly or immediately after any security incident.

## Secrets to Rotate

1. **APP_KEY** - Laravel encryption key
2. **MPESA_WEBHOOK_SECRET** - M-Pesa webhook signature verification
3. **JANUS_SECRET / JANUS_ADMIN_SECRET** - Janus Gateway secrets
4. **Database passwords** - MySQL credentials
5. **Redis password** (if configured)
6. **SMS/Email API keys**

## Rotation Procedure

### 1. APP_KEY

```bash
# Generate new key
php artisan key:generate --show

# Update .env
APP_KEY=<new-key>

# In production, deploy with new key
# Old encrypted data will need re-encryption if stored
```

### 2. MPESA_WEBHOOK_SECRET

```bash
# Generate new secret (64 char random string)
openssl rand -hex 32

# Update .env
MPESA_WEBHOOK_SECRET=<new-secret>

# Update M-Pesa Daraja API webhook configuration
# Update webhook URL in Safaricom dashboard with new secret
```

### 3. Database Passwords

```bash
# 1. Create new MySQL user with new password
# 2. Grant same permissions
# 3. Update .env with new credentials
DB_PASSWORD=<new-password>

# 4. Test connection
php artisan migrate:status

# 5. Remove old user after verification
```

### 4. Janus Secrets

```bash
# Update .env
JANUS_SECRET=<new-secret>
JANUS_ADMIN_SECRET=<new-secret>

# Update Janus configuration file
# Restart Janus service
docker compose restart janus
```

## Post-Rotation Checklist

- [ ] All services restarted
- [ ] Webhooks verified working
- [ ] Database connections tested
- [ ] API integrations tested (M-Pesa, SMS, Email)
- [ ] Old secrets removed from .env files
- [ ] Backup of old secrets stored securely (if needed for data migration)

## Emergency Rotation

If a secret is compromised:

1. Rotate immediately (don't wait for maintenance window)
2. Notify all team members
3. Review access logs for unauthorized activity
4. Consider forced re-authentication for all users

## Automated Rotation (Future)

Consider implementing:
- AWS Secrets Manager integration
- HashiCorp Vault
- Automated rotation scripts with approval workflow

