# Deploy QRCoded on Railway

## 1. Create project

1. Push this repo to GitHub.
2. In [Railway](https://railway.app), create a **New Project** → **Deploy from GitHub repo**.
3. Select this repository.

## 2. Add services

Add these plugins to the same project:

| Service | Purpose |
|---------|---------|
| **PostgreSQL** | Main database |
| **Redis** | Cache + sessions (recommended) |

Optional second service for **Laravel Reverb** (WebSockets) if you need live dashboards in production.

## 3. Environment variables (web service)

Set on the Laravel web service:

```env
APP_NAME=QRCoded
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...   # php artisan key:generate --show
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=pgsql
# Railway injects DATABASE_URL — Laravel uses it automatically

CACHE_STORE=redis
CACHE_APP_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=database

REDIS_URL=${{Redis.REDIS_URL}}
# Or: REDIS_HOST=... REDIS_PORT=... from Redis plugin

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=your-reverb-service.up.railway.app
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

## 4. Build

Railway uses `nixpacks.toml` / `railway.toml`:

- `composer install --no-dev`
- `npm ci && npm run build`
- Migrations run on start

## 5. Reverb (optional, for real-time)

Deploy a **second** service from the same repo with start command:

```bash
php artisan reverb:start --host=0.0.0.0 --port=$PORT
```

Point `REVERB_HOST` to that service's public URL.

## 6. Post-deploy

```bash
php artisan db:seed   # first deploy only, via Railway shell
```

Health check: `GET /up`
