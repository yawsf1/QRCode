# Déploiement QRCoded — guide production

Guide général pour héberger l’application (VPS, Docker, Railway, etc.). Pour **Railway** uniquement, voir aussi [RAILWAY.md](../RAILWAY.md) à la racine du projet.

---

## Prérequis

| Composant | Version / note |
|-----------|----------------|
| PHP | **8.4+** |
| PostgreSQL | 15+ recommandé |
| Redis | Cache applicatif + codes OTP inscription |
| Node.js | 20+ (build Vite uniquement) |
| Composer | 2.x |

Extensions PHP : `pdo_pgsql`, `redis`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.

---

## 1. Variables d’environnement

Copier `.env.example` vers `.env` et configurer au minimum :

```env
APP_NAME=QRCoded
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...          # php artisan key:generate --show
APP_URL=https://votre-domaine.com

DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

CACHE_STORE=redis
CACHE_APP_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=database

REDIS_HOST=...
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="${APP_NAME}"

# Temps réel (optionnel mais recommandé pour dashboards live)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=votre-domaine.com
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 2. Installation

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env   # si pas déjà fait
php artisan key:generate

npm ci
npm run build
```

---

## 3. Base de données

```bash
php artisan migrate --force
```

**Premier déploiement uniquement** (comptes de démo) :

```bash
php artisan db:seed --force
```

Comptes créés par les seeders (mot de passe : `admin123`) :

| Rôle | E-mail |
|------|--------|
| Super admin | `k8e6Y@example.com` |
| Admin (Adidas) | `k8e6ddYdd@example.com` |
| Employé (exemple) | `k8e6sdssdsdsd2dddYdd@example.com` |

En production réelle, **ne pas** lancer `db:seed` ou supprimer ces comptes après tests.

---

## 4. Optimisation Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Après chaque déploiement de code :

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Processus à faire tourner en continu

| Processus | Commande | Rôle |
|-----------|----------|------|
| **Web** | `php-fpm` + Nginx/Caddy, ou `php artisan serve` (dev) | HTTP |
| **Queue** | `php artisan queue:work --sleep=3 --tries=3` | Jobs (mails, etc.) |
| **Reverb** | `php artisan reverb:start --host=0.0.0.0 --port=8080` | WebSockets dashboards |
| **Scheduler** | Cron `* * * * * php artisan schedule:run` | Tâches planifiées (si ajoutées) |

Sans **Reverb**, l’app fonctionne ; seuls les mises à jour **temps réel** des tableaux de bord seront inactives (rechargement manuel de page).

---

## 6. Serveur web

- Document root : `public/`
- HTTPS obligatoire en production (`APP_URL` en `https://`)
- Proxy : Laravel fait confiance aux proxies via `bootstrap/app.php` / `trustProxies`

Health check : `GET /up`

---

## 7. Checklist avant mise en ligne

- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` défini
- [ ] `npm run build` exécuté (`public/build/` présent)
- [ ] PostgreSQL + Redis accessibles
- [ ] SMTP configuré (codes OTP à l’inscription admin / employé)
- [ ] Worker queue démarré
- [ ] Reverb démarré (si temps réel souhaité)
- [ ] Tester : login, QR refresh, check-in employé, export rapport CSV, formulaire contact

---

## 8. Tests automatisés (CI / local)

Avec Laravel Sail :

```bash
./vendor/bin/sail artisan test
```

Sans Sail (PHP 8.4+) :

```bash
php artisan test
```

51 tests feature + unit couvrent auth, rôles, OTP, QR, pointage, notifications, rapports, contact.

---

## 9. Dépannage rapide

| Problème | Solution |
|----------|----------|
| Page blanche | Vérifier `public/build/manifest.json`, relancer `npm run build` |
| OTP non reçu | Vérifier SMTP / logs `storage/logs/laravel.log` |
| QR expiré | Token 15 s — rafraîchir la borne admin |
| Pas de live dashboard | Démarrer Reverb + variables `VITE_REVERB_*` au build |
| CSV illisible dans Excel | Fichier UTF-8 avec `;` — ouvrir via import « délimité » |

---

## Documents liés

- [README.md](../README.md) — documentation technique complète
- [RAILWAY.md](../RAILWAY.md) — déploiement Railway
- [LARAVEL_SAIL_TEMPLATE.md](LARAVEL_SAIL_TEMPLATE.md) — développement local avec Sail
