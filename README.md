# 🏛️ CBUAE Magic Link Authentication System

> **Passwordless, CBUAE-compliant authentication for a Laravel-based Insurance Platform**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-Proprietary-red)](LICENSE)
[![Compliance](https://img.shields.io/badge/CBUAE-Compliant-009B3A)](https://www.centralbank.ae)

---

## 📋 Overview

This system implements a **secure, passwordless Magic Link authentication** flow for a Central Bank of UAE (CBUAE)-regulated insurance platform. Users receive a one-time secure login link via email — no passwords stored or required.

---

## 🔐 Security Architecture

```
User → Enter Email → Rate Limiter → Generate Token (512-bit) 
     → Hash (SHA-256) → Store Hash Only → Send Link via TLS Email
     → User Clicks Link → Validate Signature → Validate Hash 
     → Check Expiry (10 min) → Check One-time Use → IP/Device Check
     → If Suspicious → OTP via Email → Verify OTP
     → Login → Session (encrypted, 15 min inactivity) → Audit Log
```

---

## ✅ CBUAE Compliance Features

| Requirement | Implementation |
|---|---|
| Secure transport | HTTPS enforced, HSTS headers |
| Data residency | UAE-hosted DB, configurable |
| Audit trail | All actions logged with IP + timestamp |
| Session security | Encrypted, 15-30 min inactivity timeout |
| Token security | 512-bit random, SHA-256 stored, 10 min expiry |
| Rate limiting | 5 requests/60s per IP + per email |
| One-time tokens | Invalidated immediately on first use |
| Suspicious activity | IP/device change triggers OTP |
| Account lockout | 5 failed attempts → 30 min lock |
| Log retention | 7-year audit archive (CBUAE requirement) |

---

## 🗂️ Project Structure

```
app/
├── Console/Commands/
│   ├── ArchiveAuditLogs.php        # Archive old audit logs (7-year retention)
│   └── PurgeExpiredTokens.php      # Clean up expired tokens
├── Http/
│   ├── Controllers/
│   │   ├── Auth/MagicLinkController.php   # Core auth controller
│   │   ├── Admin/AuditLogController.php   # Audit log viewer
│   │   ├── Admin/UserController.php       # User management
│   │   └── DashboardController.php        # Post-login dashboard
│   ├── Middleware/
│   │   ├── ForceHttps.php           # HTTPS enforcement
│   │   ├── InactivityTimeout.php    # Auto-logout after inactivity
│   │   ├── RoleMiddleware.php       # RBAC
│   │   └── SecurityHeaders.php      # HSTS, CSP, XSS headers
│   └── Requests/
│       ├── MagicLinkRequest.php
│       └── OtpVerifyRequest.php
├── Mail/MagicLinkMail.php
├── Models/
│   ├── AuditLog.php
│   ├── MagicLoginToken.php
│   └── User.php
├── Notifications/OtpNotification.php
├── Providers/AppServiceProvider.php   # Rate limiters
└── Services/
    ├── AuditService.php               # CBUAE audit logging
    ├── MagicLinkService.php           # Core token logic
    └── OtpService.php                 # Secondary verification

database/migrations/
├── ..._create_users_table.php
├── ..._create_magic_login_tokens_table.php
└── ..._create_audit_logs_table.php

resources/views/
├── auth/
│   ├── login.blade.php               # Login form
│   └── otp.blade.php                 # OTP verification
├── dashboard/index.blade.php
├── admin/
│   ├── audit-logs.blade.php
│   └── users.blade.php
├── emails/
│   ├── magic-link.blade.php
│   └── otp.blade.php
└── layouts/app.blade.php

config/
├── magic_link.php                    # Token/security settings
├── session.php                       # Encrypted, HTTPS-only
└── logging.php                       # Audit channel (7-year retention)
```

---

## 🚀 Installation

### Requirements
- PHP 8.2+
- Laravel 11
- MySQL 8.0+ or PostgreSQL 14+ (UAE-hosted)
- Redis (sessions & rate limiting)
- SMTP server with TLS

### Steps

```bash
# 1. Clone and install dependencies
git clone https://github.com/your-org/cbuae-magic-auth.git
cd cbuae-magic-auth
composer install --no-dev --optimize-autoloader

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Configure .env
nano .env
# Set: DB_*, MAIL_*, APP_URL (must be https://), SESSION_DRIVER=redis

# 4. Database
php artisan migrate
php artisan db:seed   # Creates admin/agent/auditor/customer demo accounts

# 5. Production optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set up scheduler (crontab)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## ⚙️ Configuration

Edit `config/magic_link.php` or set these in `.env`:

| Variable | Default | Description |
|---|---|---|
| `MAGIC_LINK_EXPIRY_MINUTES` | `10` | Token validity window (CBUAE: 10-15 min) |
| `MAGIC_LINK_RATE_LIMIT` | `5` | Max requests per window |
| `MAGIC_LINK_RATE_LIMIT_DECAY` | `60` | Rate limit window in seconds |
| `INACTIVITY_TIMEOUT_MINUTES` | `15` | Auto-logout (CBUAE: 15-30 min) |
| `FORCE_HTTPS` | `true` | Redirect HTTP → HTTPS |
| `SUSPICIOUS_ACTIVITY_OTP` | `true` | Require OTP on IP/device change |
| `SESSION_ENCRYPT` | `true` | Encrypt session data |

---

## 🔑 Authentication Flow

### Normal Login
1. User enters email → POST `/auth/magic-link`
2. Rate limiter checks (5/min per IP, 3/min per email)
3. System generates 128-char hex token (64 random bytes)
4. SHA-256 hash stored in `magic_login_tokens`
5. Signed URL emailed to user (expires 10 min)
6. User clicks link → GET `/auth/magic-link/verify?token=...&uid=...`
7. Laravel verifies URL signature
8. Token hash validated, expiry checked, one-time use enforced
9. User logged in, session created, audit logged

### Suspicious Activity (IP/Device Change)
1. Steps 1-8 above — but IP subnet or device fingerprint differs
2. OTP (6-digit) generated and emailed
3. User enters OTP at `/auth/otp`
4. OTP verified (5 min window, 5 attempts max)
5. Login completed

---

## 🗄️ Database Schema

### `magic_login_tokens`
| Column | Type | Notes |
|---|---|---|
| `user_id` | FK | References `users.id` |
| `token_hash` | varchar(64) | **SHA-256 only — never raw token** |
| `is_used` | boolean | One-time use flag |
| `used_at` | timestamp | When consumed |
| `created_ip` | varchar(45) | Requesting IP (IPv4/IPv6) |
| `used_ip` | varchar(45) | Consuming IP |
| `device_fingerprint` | varchar(64) | Browser fingerprint hash |
| `otp_required` | boolean | Suspicious activity flag |
| `otp_hash` | varchar(64) | SHA-256 of OTP |
| `invalidated_at` | timestamp | When revoked |

### `audit_logs`
| Column | Type | Notes |
|---|---|---|
| `user_id` | FK nullable | |
| `email` | string | Even pre-authentication |
| `action` | string | See action constants |
| `status` | string | success / failure / warning |
| `ip_address` | varchar(45) | Required |
| `user_agent` | text | |
| `created_at` | timestamp | **Immutable — no updated_at** |

#### Audit Actions
- `link_generated` — Magic link created
- `email_sent` — Link dispatched
- `login_success` — Authenticated
- `login_failed` — Invalid/expired/used token
- `logout` — Session ended
- `token_expired` — Token validity window passed
- `token_revoked` — Manually or superseded
- `otp_sent` — Secondary OTP dispatched
- `otp_verified` — OTP confirmed
- `otp_failed` — Wrong OTP entered
- `suspicious_activity` — IP/device mismatch
- `rate_limited` — Too many requests
- `account_locked` — Too many failures

---

## 👥 Role-Based Access Control

| Role | Dashboard | Audit Logs | User Management |
|---|---|---|---|
| `admin` | ✅ | ✅ Full | ✅ Full |
| `agent` | ✅ | ❌ | ❌ |
| `auditor` | ✅ | ✅ Read | ❌ |
| `customer` | ✅ | ❌ | ❌ |

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# With coverage
php artisan test --coverage
```

Test coverage includes:
- ✅ Valid token login
- ✅ Expired token rejection
- ✅ Used token rejection (replay prevention)
- ✅ Inactive account rejection
- ✅ Rate limiting enforcement
- ✅ Token superseding on re-request
- ✅ User enumeration prevention
- ✅ 512-bit entropy verification
- ✅ SHA-256 hash storage verification
- ✅ Audit log creation

---

## 🛡️ Security Headers

All responses include:
```
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Content-Security-Policy: default-src 'self'; frame-ancestors 'none'
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

---

## 🔄 Scheduled Tasks

```bash
# Hourly — Purge expired tokens
php artisan tokens:purge-expired

# Daily 2 AM — Archive audit logs > 90 days
php artisan audit:archive

# Dry run (preview)
php artisan tokens:purge-expired --dry-run
php artisan audit:archive --days=30
```

---

## 📞 Support & Compliance Contacts

For CBUAE compliance queries, contact your designated compliance officer.

---

*Built for the Central Bank of UAE (CBUAE) regulated insurance sector.*
*All data is stored within UAE borders in compliance with UAE data residency laws.*
