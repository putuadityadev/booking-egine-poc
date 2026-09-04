# Booking Engine POC (Third-Party OAuth 2.1 Simulation)

A lightweight Proof of Concept (POC) demonstrating third-party integration (hotel booking engine) with the multi-tenant **Membership Platform** via **OAuth 2.1** (`/api/v2/oauth/*`).

---

## Architecture Overview

```
┌─────────────────────────────────┐           ┌─────────────────────────────────┐           ┌───────────────────────────────────────┐
│     POC Frontend (Vue 3)        │ ────────> │      POC Backend BFF (Laravel)   │ ────────> │       Membership Platform (API)       │
│     http://localhost:5173       │   JSON    │      http://localhost:8002      │   HTTP    │       http://myapp:8000               │
└─────────────────────────────────┘           └─────────────────────────────────┘           └───────────────────────────────────────┘
                                                   - Injects CLIENT_SECRET                       - Multi-Tenant DB (stancl/tenancy)
                                                   - Proxy for Member Auth                       - Header: X-Tenant-Domain
                                                   - Dynamic Member Rates                        - Issue RS256 JWT & Verify JWKS
```

---

## Project Structure

```
booking-engine-poc/
├── docker-compose.yml          # Connects to shared docker network: app-network
├── README.md                   # Setup and usage guide
├── backend/                    # Laravel 12 API / BFF Service
│   ├── .env.example
│   ├── .env                    # Local environment config
│   ├── .env.staging            # Staging environment template
│   ├── config/membership.php   # Membership integration config
│   └── routes/api.php          # API routes (/api/status, etc.)
└── frontend/                   # Vue 3 + Vite SPA
    ├── .env.example
    ├── .env                    # Local frontend config
    ├── .env.staging            # Staging frontend template
    ├── Dockerfile
    └── src/                    # Vue source components
```

---

## How to Run

### Option A: Using Docker Compose (Recommended)

Make sure the membership containers (`app-network`) are running:

```bash
cd booking-engine-poc

# Start both services
docker compose up -d

# Check status
docker compose ps

# View logs
docker compose logs -f
```

* **Frontend:** [http://localhost:5173](http://localhost:5173)
* **Backend Status:** [http://localhost:8002/api/status](http://localhost:8002/api/status)
* **Backend Welcome:** [http://localhost:8002](http://localhost:8002)

---

### Option B: Running Natively on Host (Without Docker)

#### 1. Backend (Laravel API):
```bash
cd booking-engine-poc/backend
php artisan serve --host=0.0.0.0 --port=8002
```

#### 2. Frontend (Vue 3 Vite):
```bash
cd booking-engine-poc/frontend
npm run dev
```

---

## Environment Configuration

### Local Environment (`.env`)
* Backend: `MEMBERSHIP_API_URL=http://localhost:8000` (or `http://myapp:8000` inside Docker)
* Backend: `MEMBERSHIP_TENANT_DOMAIN=jeevawasa.localhost`
* Frontend: `VITE_BFF_API_URL=http://localhost:8002`

### Staging Environment (`.env.staging`)
When deploying or testing against staging:
1. Copy `.env.staging` to `.env` in `backend/` and `frontend/`.
2. Fill in `MEMBERSHIP_API_URL`, `MEMBERSHIP_TENANT_DOMAIN`, `MEMBERSHIP_CLIENT_ID`, and `MEMBERSHIP_CLIENT_SECRET`.
