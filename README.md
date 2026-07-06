# Suria Solar Calculator

Standalone solar savings calculator for **Suria Infiniti Sdn Bhd**, deployed at `calculator.suriainfiniti.com`.

## Stack

- **Frontend:** Next.js 15 (static export) + Tailwind CSS
- **Backend:** PHP 8 + MySQL
- **Hosting:** cPanel subdomain

## Project Structure

```
├── frontend/          Next.js app → builds to out/
├── backend/
│   ├── api/           REST endpoints
│   ├── admin/         Leads dashboard
│   ├── includes/      Shared PHP libs
│   └── db/schema.sql  Database schema
├── prd.md             Product requirements
├── design.md          UI design system
└── docs/superpowers/specs/  Design spec
```

## Local Development

### 1. Frontend

```bash
cd frontend
cp .env.local.example .env.local
npm install
npm run dev
```

Open http://localhost:3000

### 2. Backend

```bash
# Copy and edit config
cp backend/config.example.php backend/config.php

# Create MySQL database and import schema
mysql -u root -p suria_calc < backend/db/schema.sql

# Run PHP server
cd backend
php -S localhost:8000
```

API available at http://localhost:8000/api/

### 3. Setup admin user

```bash
cd backend
php setup.php
```

### 4. Environment variables

**frontend/.env.local:**
```
NEXT_PUBLIC_API_URL=http://localhost:8000
NEXT_PUBLIC_TURNSTILE_SITE_KEY=your_turnstile_site_key
```

**backend/config.php:** DB credentials, CSRF secret, Turnstile secret, SMTP settings.

## Build for Production

```bash
cd frontend
npm run build
# Output in frontend/out/
```

## cPanel Deployment

1. Create subdomain `calculator.suriainfiniti.com`
2. Upload `frontend/out/*` → subdomain document root
3. Upload `backend/api/`, `backend/admin/`, `backend/includes/` to same root
4. Place `backend/config.php` **outside** public web root (or protect with `.htaccess`)
5. Import `backend/db/schema.sql` via phpMyAdmin
6. Run `php setup.php` once to set admin password (then delete setup.php)
7. Enable AutoSSL for HTTPS
8. Register Cloudflare Turnstile keys at https://dash.cloudflare.com/

## Default Config Values

| Setting | Default |
|---|---|
| Tariff rate | RM 0.571/kWh |
| Cost per kWp | RM 4,200 |
| Sales email | taufik@suriainfiniti.com |
| WhatsApp | 60127075391 |

## Security Checklist (before ads)

- [ ] HTTPS enabled
- [ ] config.php outside web root
- [ ] CSRF secret changed from default
- [ ] Turnstile keys set (production, not test keys)
- [ ] Admin password changed via setup.php
- [ ] setup.php deleted from server
- [ ] display_errors = Off in PHP
- [ ] MySQL backup cron configured

## Admin Dashboard

URL: `https://calculator.suriainfiniti.com/admin/`

Features: view leads, filter by date/state/status, update lead status, export CSV.
