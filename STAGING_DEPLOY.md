# Staging Deployment Guide — Booking Engine POC + CF Tunnel

> **Updated**: September 2026
> **Stack**: Laravel 11 (Backend BFF) + Vue 3 (Frontend) + Cloudflare Tunnel

---

## Pre-Deploy Checklist

### 1. Generate APP_KEY

```bash
cd booking-engine-poc/backend
php artisan key:generate --env=staging
# Salin hasilnya ke .env.staging → APP_KEY=base64:xxx...
```

### 2. Isi .env.staging (Backend)

```env
APP_NAME="Booking Engine POC API (Staging)"
APP_ENV=staging
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://YOUR-TUNNEL.trycloudflare.com

LOG_LEVEL=info

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none

CORS_ALLOWED_ORIGINS=*

SANCTUM_STATEFUL_DOMAINS=YOUR-TUNNEL.trycloudflare.com,localhost

QUEUE_CONNECTION=sync

# Membership API (Staging)
MEMBERSHIP_API_URL=https://staging-membership.yourdomain.com
MEMBERSHIP_TENANT_DOMAIN=your-tenant.mymembership.id
MEMBERSHIP_CLIENT_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MEMBERSHIP_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 3. Isi .env.staging (Frontend)

```env
VITE_APP_TITLE="Booking Engine (Staging)"
VITE_BFF_API_URL=https://YOUR-TUNNEL.trycloudflare.com
VITE_MEMBERSHIP_API_URL=https://staging-membership.yourdomain.com
VITE_MEMBERSHIP_TENANT_DOMAIN=your-tenant.mymembership.id
VITE_MEMBERSHIP_CLIENT_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

---

## Deploy Steps

### Backend

```bash
cd booking-engine-poc/backend

# 1. Install dependencies
composer install --no-dev --optimize-autoloader

# 2. Copy env
cp .env.staging .env

# 3. Generate key (jika belum)
php artisan key:generate

# 4. Create & migrate SQLite DB
touch database/database.sqlite
php artisan migrate --force

# 5. Clear & cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache

# 6. Set permissions
chmod -R 775 storage bootstrap/cache
```

### Frontend

```bash
cd booking-engine-poc/frontend

# 1. Install dependencies
npm install

# 2. Build untuk staging
npm run build -- --mode staging
# Output: dist/ folder

# 3. Serve dist/ via Nginx/Caddy atau serve langsung
npx serve dist -p 5173
```

### Cloudflare Tunnel

```bash
# Install cloudflared
# https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/get-started/

# Quick tunnel (tanpa akun — untuk quick test)
cloudflared tunnel --url http://localhost:8002

# Named tunnel (lebih stabil — rekomendasi untuk QA)
cloudflared tunnel create booking-engine-staging
cloudflared tunnel route dns booking-engine-staging YOUR-SUBDOMAIN.yourdomain.com
cloudflared tunnel run booking-engine-staging
```

---

## Jalankan Backend

```bash
cd booking-engine-poc/backend
php artisan serve --host=0.0.0.0 --port=8002 --env=staging
```

---

## QA Test Scenarios

### Health Check
```
GET https://YOUR-TUNNEL.trycloudflare.com/api/status
```
Expected: `{ "status": "online", "environment": "staging" }`

### Push Transaction
```
POST https://YOUR-TUNNEL.trycloudflare.com/api/booking/reserve
```

### Materialize Points
```
POST https://YOUR-TUNNEL.trycloudflare.com/api/booking/{code}/materialize
```

### Cron Manual Test
```bash
php artisan points:release-scheduled
```

---

## Troubleshooting

### "URL generated incorrectly" / HTTP instead of HTTPS

**Penyebab**: TrustProxies belum aktif  
**Fix**: Cek `bootstrap/app.php` → `$middleware->trustProxies(at: '*');`

### "419 CSRF / Session expired"

**Penyebab**: `SESSION_SECURE_COOKIE` atau `SANCTUM_STATEFUL_DOMAINS` belum di-set  
**Fix**: Pastikan domain CF tunnel ada di `SANCTUM_STATEFUL_DOMAINS`

### "CORS error dari frontend"

**Penyebab**: Backend menolak origin frontend  
**Fix**: Set `CORS_ALLOWED_ORIGINS=*` atau tambahkan URL frontend

### "Membership API tidak bisa dihubungi"

**Penyebab**: `MEMBERSHIP_API_URL` masih localhost atau salah  
**Fix**: Pastikan URL membership staging bisa diakses dari server booking engine

### "client_id invalid / OAUTH.CLIENT.INVALID"

**Penyebab**: Credentials staging belum di-generate di Membership  
**Fix**: Generate client credentials baru via Membership extranet untuk staging env
