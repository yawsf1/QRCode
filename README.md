# QRCoded — Documentation complète du projet

**QRCoded** est une application web de **gestion de présence par QR code** pour entreprises. Un administrateur affiche un code QR dynamique sur un écran (borne) ; les employés scannent avec leur téléphone pour pointer. Le système calcule ponctualité, retards, avances, alimente des tableaux de bord temps réel et permet l’export CSV.

Ce document décrit **tout** ce que le projet contient et comment chaque partie fonctionne (orienté technique, lisible aussi par des profils métier).

---

## Table des matières

1. [Résumé métier](#résumé-métier)
2. [Stack technique complète](#stack-technique-complète)
3. [Rôles et permissions](#rôles-et-permissions)
4. [Fonctionnalités par module](#fonctionnalités-par-module)
5. [Architecture système](#architecture-système)
6. [Schéma de base de données](#schéma-de-base-de-données)
7. [Routes HTTP complètes](#routes-http-complètes)
8. [Backend Laravel](#backend-laravel)
9. [Frontend Vue / Inertia](#frontend-vue--inertia)
10. [Authentification et sessions](#authentification-et-sessions)
11. [Inscription avec code e-mail (OTP)](#inscription-avec-code-e-mail-otp)
12. [Flux QR et pointage](#flux-qr-et-pointage)
13. [Temps réel (Reverb + Echo)](#temps-réel-reverb--echo)
14. [Cache Redis](#cache-redis)
15. [Notifications admin](#notifications-admin)
16. [Rapports CSV](#rapports-csv)
17. [Page d’accueil et contact](#page-daccueil-et-contact)
18. [Toasts et UX globale](#toasts-et-ux-globale)
19. [Variables d’environnement](#variables-denvironnement)
20. [Docker / Laravel Sail](#docker--laravel-sail)
21. [Scripts NPM et commandes](#scripts-npm-et-commandes)
22. [Seeders et comptes de démo](#seeders-et-comptes-de-démo)
23. [Suite de tests](#suite-de-tests)
24. [Déploiement production](#déploiement-production)
25. [Arborescence utile](#arborescence-utile)
26. [Dépannage rapide](#dépannage-rapide)
27. [Documents annexes](#documents-annexes)

---

## Résumé métier

| Concept | Description |
|--------|-------------|
| **Borne QR** | Page admin qui affiche un QR dont le **token change** (~15 s), lié à l’admin connecté |
| **Pointage** | Scan du QR par l’employé → enregistrement dans `presences` |
| **Horaire** | Plage début/fin, jours ouvrés, tolérance de retard — **par employé** |
| **Statut** | `en_avance`, `a_lheure`, `en_retard`, `absent` (selon heure de scan vs horaire) |
| **OTP inscription** | Lors de la **création** d’un admin ou employé, un code à 6 chiffres est envoyé à l’e-mail saisi ; le compte n’est créé qu’après validation |

---

## Stack technique complète

### Backend

| Composant | Version / détail |
|-----------|------------------|
| PHP | ^8.3 |
| Laravel | ^13.7 |
| Inertia Laravel | ^3.1 (SPA sans API REST séparée pour les pages) |
| PostgreSQL | Base principale |
| Redis | Cache applicatif (`CACHE_STORE`, `CACHE_APP_STORE`) |
| Laravel Reverb | WebSockets (protocole compatible Pusher) |
| Predis | Client Redis PHP |
| Ziggy | Routes Laravel exposées au JS (`route()`) |

### Frontend

| Composant | Usage |
|-----------|--------|
| Vue 3 | Composition API, SFC `.vue` |
| Inertia Vue 3 | Navigation sans rechargement complet |
| Pinia | Store `ui` (sidebar mobile, barre de chargement) |
| SCSS | Styles par composant (pas de Tailwind en prod UI principale) |
| Vite 8 | Build + HMR |
| Chart.js + vue-chartjs | Graphiques dashboards |
| qrcode-vue3 | Affichage QR admin |
| html5-qrcode | Scan caméra employé |
| vue-toastification | Toasts (flash Laravel) |
| Laravel Echo + pusher-js | Client WebSocket |

### DevOps / outillage

| Outil | Rôle |
|-------|------|
| Laravel Sail | Docker Compose (PHP, PostgreSQL, Redis, Mailpit) |
| PHPUnit 12 | Tests feature |
| Laravel Pint | Style PHP |
| Railway + Nixpacks | Déploiement (`RAILWAY.md`, `nixpacks.toml`, `railway.toml`, `Procfile`) |
| Concurrently | Script `npm run start-all` (Sail + Vite + Reverb + queue) |

---

## Rôles et permissions

| Rôle | `users.role` | Middleware | Périmètre |
|------|--------------|------------|-----------|
| Super administrateur | `super_admin` | `auth`, `role:super_admin` | Plateforme : créer/supprimer admins, stats globales |
| Administrateur | `admin` | `auth`, `role:admin` | Son entreprise : employés, QR, dashboard, notifications, rapports |
| Employé | `employe` | `auth`, `role:employe` | Son espace : dashboard perso, scan QR |

**Hiérarchie :** `users.admin_id` lie un employé (ou admin créé) à son admin parent. Un admin ne voit que **ses** employés.

**Middleware `CheckRole` :** alias `role` dans `bootstrap/app.php` ; vérifie `User::role`.

---

## Fonctionnalités par module

### Super administrateur

- Dashboard plateforme (`SuperAdmin/Dashboard.vue`) — graphiques admins / employés par mois, répartition par département
- Liste des admins (`SuperAdmin/ListAdmin.vue`) — recherche, tri, pagination, suppression
- Création admin (`SuperAdmin/AdminRegister.vue`) — **OTP e-mail**, modal de vérification
- Temps réel : canal `platform-channel`, événement `PlatformStatsUpdated`

### Administrateur

- Dashboard (`Admin/Dashboard.vue`) — KPIs, graphiques mensuels, top/flop employés, fil d’activité live, cloche notifications
- Sidebar : tableau de bord, ajouter employé, liste employés, **Générer Borne QR**, déconnexion
- CRUD employés : register, list, show, update, delete
- QR dashboard (`Admin/QrDashboard.vue`) — token live, refresh auto 12 s + manuel
- Export rapport CSV par employé (`RapportController`)
- Notifications : lire / tout marquer lu
- Suppression de son propre compte (`admin.account.delete`)

### Employé

- Dashboard (`Employe/Dashboard.vue`) — historique, KPIs ponctualité, graphiques
- Scanner (`Employe/Scanner.vue`) — caméra, envoi token au check-in
- Une seule présence « valide » par jour (hors statut `absent` selon logique métier)
- Temps réel sur son propre scan via `attendance-channel`

### Public

- Page d’accueil (`Home.vue`) — marketing, FAQ, formulaire contact
- Login avec **Se souvenir de moi**

---

## Architecture système

```
┌─────────────────────────────────────────────────────────────┐
│  Navigateur (Vue 3 + Inertia)                                  │
│  Pages/, components/, stores/ui.js, utils/flashToasts.js     │
│  echo.js → Reverb (WebSocket)                                │
└───────────────────────────┬─────────────────────────────────┘
                            │ HTTP (Inertia JSON) + WS
┌───────────────────────────▼─────────────────────────────────┐
│  Laravel 13                                                  │
│  routes/web.php → Controllers → Services → Models / DB         │
│  Events → Reverb broadcast                                   │
│  Mail → SMTP / Mailpit                                       │
│  AppCache → Redis                                            │
└───────────────────────────┬─────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        ▼                   ▼                   ▼
   PostgreSQL            Redis              Mailpit/SMTP
```

**Pattern :** logique métier dans **Services** ; contrôleurs fins ; validation dans **FormRequest** ; pages Inertia via **RoutingController** ou services dédiés dashboard.

---

## Schéma de base de données

### `users`

| Colonne | Type | Notes |
|---------|------|--------|
| id | bigint | PK |
| nom, prenom | string | |
| email | string unique | |
| email_verified_at | timestamp nullable | Rempli à l’inscription OTP |
| password | string hashed | |
| role | enum | `admin`, `super_admin`, `employe` |
| departement, telephone | string nullable | |
| est_actif | boolean | |
| admin_id | FK users nullable | Parent admin |
| remember_token | | Login « remember me » |

### `horaires`

| Colonne | Notes |
|---------|--------|
| user_id | Employé |
| heure_debut, heure_fin | Format H:i |
| jours_ouvres | JSON array (`Lun`…`Dim`) |
| tolerance_retard | Minutes |
| admin_id | |

### `sessions_qr`

| Colonne | Notes |
|---------|--------|
| admin_id | Admin propriétaire de la borne |
| token | String aléatoire (~40 car.) |
| expires_at | ~15 secondes |

### `presences`

| Colonne | Notes |
|---------|--------|
| user_id | Employé |
| admin_id | Admin du QR |
| session_id | FK sessions_qr |
| statut | `a_lheure`, `en_retard`, `en_avance`, `absent` |
| date_heure_scan | datetime |
| ecart_minutes | int |
| adresse_ip | |

### `notifications` (modèle `AlertNotification`)

| Colonne | Notes |
|---------|--------|
| content, type | `retard`, `absence`, `info` |
| lu | boolean |
| user_id | Employé concerné |
| admin_id | Admin destinataire |

### `rapports`

Historique des exports CSV (période, type, employé, admin).

### `contacts`

Messages formulaire page d’accueil.

### `email_verification_codes`

OTP inscription (hash du code, expiration) — table liée à l’inscription, pas au login.

### Tables Laravel standard

`sessions`, `password_reset_tokens`, `cache`, `jobs` (migrations incluses).

---

## Routes HTTP complètes

| Méthode | URI | Nom | Rôle |
|---------|-----|-----|------|
| GET | `/` | home | Public |
| POST | `/contact` | contact.send | Public |
| GET | `/login` | login | Guest |
| POST | `/login` | user.login | Guest |
| POST | `/logout` | logout | Auth |
| POST | `/register/admin/send-verification-code` | register.admin.verification.send | super_admin |
| POST | `/register/employe/send-verification-code` | register.employe.verification.send | admin |
| GET | `/super-admin/dashboard` | super-admin.dashboard | super_admin |
| GET | `/admin/register` | admin.register.form | super_admin |
| POST | `/admin` | admin.register | super_admin |
| GET | `/admin` | admin.list | super_admin |
| DELETE | `/admin/{user}` | admin.delete | super_admin |
| GET | `/admin/dashboard` | admin.dashboard | admin |
| GET | `/employe/register` | employe.register.form | admin |
| POST | `/employe` | admin.employe.register | admin |
| GET | `/employe` | employe.list | admin |
| GET | `/employe/{user}` | employe.show | admin |
| GET | `/employe/{user}/edit` | employe.update.form | admin |
| PUT | `/employe/{user}` | admin.employe.update | admin |
| DELETE | `/employe/{user}` | admin.employe.delete | admin |
| DELETE | `/account` | admin.account.delete | admin |
| GET | `/admin/qr-dashboard` | admin.qr.show | admin |
| POST | `/admin/qr-refresh` | admin.qr.refresh | admin |
| POST | `/notifications/{notification}/read` | admin.notifications.read | admin |
| POST | `/notifications/read-all` | admin.notifications.read-all | admin |
| GET | `/employe/{user}/rapport` | admin.employe.rapport | admin |
| GET | `/mon-espace/dashboard` | employe.dashboard | employe |
| GET | `/mon-espace/scan` | employe.scan.form | employe |
| POST | `/mon-espace/check-in` | employe.checkin | employe |

**Santé :** `GET /up` (Laravel health).

---

## Backend Laravel

### Contrôleurs

| Fichier | Responsabilité |
|---------|----------------|
| `RoutingController` | Rendu pages Inertia (home, login, dashboards, listes, scan) |
| `AuthController` | Login, logout, remember me |
| `RegistrationVerificationController` | Envoi OTP après validation **complète** du formulaire |
| `AdminController` | Register/delete admin, delete own account |
| `EmployeController` | Register/update/delete employé |
| `QrSessionController` | Affichage / refresh session QR |
| `CheckInController` | Pointage scan |
| `NotificationController` | Marquer notifications lues |
| `RapportController` | Export CSV |
| `ContactController` | Formulaire contact |

### Services

| Service | Rôle |
|---------|------|
| `AdminDashboard` | Agrégations admin, cache, Inertia props, listes employés |
| `EmployeDashboard` | Stats employé, graphiques, historique |
| `SuperAdminDashboard` | Stats plateforme, liste admins |
| `QrService` | Création/refresh session QR (transaction DB, **pas** de cache modèle Eloquent) |
| `EmailVerificationService` | OTP inscription (cache Redis + e-mail) |
| `NotificationService` | Création + lecture notifications |
| `RapportService` | Export CSV + enregistrement `rapports` |

### Support

| Classe | Rôle |
|--------|------|
| `AppCache` | Wrapper `Cache::store(config('cache.app_store'))` |
| `CacheKeys` | Clés centralisées (dashboards, QR, scan du jour, OTP) |

### Events (broadcast)

| Event | Canal | Nom | Payload |
|-------|-------|-----|---------|
| `EmployeCheckedIn` | `attendance-channel` | `checked-in` | `presence` + user |
| `PlatformStatsUpdated` | `platform-channel` | `stats-updated` | type + payload (mois, département) |

`ShouldBroadcastNow` — diffusion immédiate sans queue.

### Mail

| Mailable | Usage |
|----------|--------|
| `EmailVerificationCodeMail` | Markdown `emails/verification-code` — OTP 6 chiffres |

### Form Requests (validation)

| Request | Usage |
|---------|--------|
| `LoginRequest` | email, password, remember |
| `Admin/RegisterRequest` | Champs admin + `verification_code` |
| `Employe/RegisterRequest` | Champs employé + horaire + OTP |
| `SendAdminRegistrationCodeRequest` | **Mêmes règles** que register sans OTP |
| `SendEmployeRegistrationCodeRequest` | Idem employé |
| `Employe/UpdateRequest` | Mise à jour employé |
| Traits `AdminRegistrationFields`, `EmployeRegistrationFields`, `ValidatesRegistrationEmailCode` | DRY règles |

### Middleware

| Middleware | Rôle |
|-----------|------|
| `HandleInertiaRequests` | Partage `auth`, `flash`, `checkedInToday`, `notifications` |
| `CheckRole` | Garde par rôle |

### Models principaux

`User`, `Horaire`, `SessionQR`, `Presence`, `AlertNotification`, `Rapport`, `Contact`, `EmailVerificationCode`.

**User :** `hasCheckedInToday()` (cache + requête), scopes `filter`, `sortBy`, relations `horaire`, `employes`, `admin`.

---

## Frontend Vue / Inertia

### Entrée `resources/js/app.js`

- Résolution pages : `./Pages/**/*.vue` (glob **sensible à la casse** : `Pages` majuscule)
- Layout par défaut : `MainLayout` ou `DashboardLayout` selon nom de page
- Pinia, Toast, `flashToasts.js` (anti-doublon), barre chargement globale

### Layouts

| Layout | Pages |
|--------|--------|
| `MainLayout` | Home, listes, formulaires — **barre horizontale** + logout |
| `DashboardLayout` | Dashboards, QR, scanner — plein écran, **sans** top bar |
| `LoginLayout` | Login |

### Pages Inertia (`resources/js/Pages/`)

| Fichier | Description |
|---------|-------------|
| `Home.vue` | Landing + contact |
| `Auth/Login.vue` | Connexion + remember me (toggle custom) |
| `SuperAdmin/Dashboard.vue` | Stats plateforme |
| `SuperAdmin/ListAdmin.vue` | Liste admins |
| `SuperAdmin/AdminRegister.vue` | Création admin + modal OTP |
| `Admin/Dashboard.vue` | Dashboard admin + Echo |
| `Admin/QrDashboard.vue` | Borne QR |
| `Admin/ListEmploye.vue` | Liste employés |
| `Admin/Employe.vue` | Fiche employé |
| `Admin/EmployeRegister.vue` | Création employé + modal OTP |
| `Admin/EmployeUpdate.vue` | Édition employé |
| `Employe/Dashboard.vue` | Dashboard employé |
| `Employe/Scanner.vue` | Scan QR |

### Composants notables

| Composant | Rôle |
|-----------|------|
| `RegistrationVerificationModal.vue` | Modal centrale OTP (X, clic extérieur, erreurs) |
| `AdminSidebar.vue` | Navigation admin + logout |
| `DashboardSidebarLogout.vue` | Bouton déconnexion sidebar |
| `NotificationBell.vue` | Cloche admin |
| `LineChart.vue`, `PieChart.vue` | Graphiques |
| `GlobalLoadingBar.vue` | Barre progression Inertia |
| `HorizontalBar.vue` | Nav + logout (pages MainLayout) |
| `DashboardMobileNav.vue` | Menu burger dashboards |

### Composables

| Fichier | Rôle |
|---------|------|
| `useRegistrationVerification.js` | 1) POST validation complète → ouvre modal 2) POST register avec code |

### Store Pinia

`stores/ui.js` : `mobileSidebarOpen`, `globalLoading`, actions open/close.

### Utilitaires

`utils/flashToasts.js` — toasts sur `router.on('finish')`, déduplication 5 s + `filterBeforeCreate`.

### Echo

`resources/js/echo.js` — configuration Reverb via variables `VITE_REVERB_*`.

---

## Authentification et sessions

- Guard session Laravel standard
- **Remember me :** `Auth::attempt($credentials, $remember)` ; cookie `remember_web_*`
- **Pas de OTP au login** — uniquement à l’inscription d’un nouvel utilisateur par un admin/super admin
- Redirect après login selon `role`
- Logout : invalidation session + redirect home + flash success

---

## Inscription avec code e-mail (OTP)

### UX (admin + employé)

1. L’utilisateur remplit **tout** le formulaire.
2. Clic sur **Ajouter l'administrateur** / **Ajouter l'employé**.
3. Le backend valide **tous** les champs (`SendAdminRegistrationCodeRequest` / `SendEmployeRegistrationCodeRequest`).
4. **Si erreurs** → messages sous chaque champ Inertia (`errors.*`), **pas de modal**.
5. **Si OK** → e-mail envoyé, formulaire en **opacité réduite**, **modal centrale** avec saisie du code.
6. Clic **Valider et créer le compte** → `POST` register avec `verification_code`.
7. Succès → redirect dashboard créateur ; code invalide → message **dans la modal**.
8. Fermer modal : **X** ou clic **à l’extérieur** → formulaire normal.

### Technique

- Code stocké dans **Redis** (`CacheKeys::registrationEmailVerify(email)`), TTL 15 min, hash bcrypt
- Après création : `email_verified_at = now()`, cache OTP supprimé
- Routes séparées : `register.admin.verification.send`, `register.employe.verification.send`

### Test local

Ouvrir **Mailpit** : `http://localhost:8026` (port `FORWARD_MAILPIT_DASHBOARD_PORT`).

---

## Flux QR et pointage

### Génération QR (`QrService`)

1. `getOrCreateActiveSession($adminId)` — session non expirée ou nouvelle (transaction + `lockForUpdate`)
2. Token 40 caractères, expiration ~15 s
3. **Pas de sérialisation modèle en cache** (évite `__PHP_Incomplete_Class` au double-clic)

### Refresh

`POST admin/qr-refresh` — supprime anciennes sessions, en crée une nouvelle ; front refresh toutes les 12 s.

### Scan employé

1. `GET mon-espace/scan` — redirige si déjà pointé aujourd’hui
2. `html5-qrcode` lit le token
3. `POST mon-espace/check-in` avec `{ token }`
4. Vérifications : token valide/non expiré, pas déjà pointé, horaire employé
5. Calcul statut vs `heure_debut` + `tolerance_retard`
6. `Presence::create`, cache invalidation, `EmployeCheckedIn` broadcast, notification admin
7. Redirect `employe.dashboard` + message flash

---

## Temps réel (Reverb + Echo)

**Démarrage local :**

```bash
./vendor/bin/sail artisan reverb:start
```

**Canaux :**

- `attendance-channel` — admin dashboard (tous les scans de son équipe), employé dashboard (son propre scan)
- `platform-channel` — super admin (création/suppression admin/employé)

**Événements Vue :** `.listen('.checked-in', …)` et `.listen('.stats-updated', …)` dans les dashboards concernés.

---

## Cache Redis

| Clé (via `CacheKeys`) | Contenu | Invalidation |
|------------------------|---------|--------------|
| `admin_dashboard_stats_{id}` | Métriques admin | CRUD employé, check-in |
| `employe_dashboard_stats_{id}` | Stats employé | Check-in |
| `super_admin_dashboard_stats` | Stats plateforme | CRUD admin/employé |
| `qr_session_{adminId}` | (forget seulement) | Refresh QR |
| `user_scanned_{userId}_{date}` | Flag scan du jour | Check-in |
| `registration_email_verify_{md5(email)}` | Hash OTP | Après register |

Config : `CACHE_APP_STORE=redis` dans `.env`.

---

## Notifications admin

- Créées dans `CheckInController` via `NotificationService::notifyAdminOfCheckIn`
- Types : retard, absence, info
- Partagées Inertia : `notifications.items`, `notifications.count`
- `NotificationBell.vue` — marquer une ou toutes lues

---

## Rapports CSV

- Route : `GET employe/{user}/rapport?type=mensuel` (ou autre type selon `RapportService`)
- Génère CSV (séparateur `;`) des présences sur la période
- Enregistre une ligne dans `rapports` pour traçabilité

---

## Page d’accueil et contact

- `Home.vue` — sections marketing, FAQ, formulaire
- `POST /contact` — `ContactController` → modèle `Contact`

---

## Toasts et UX globale

- Flash Laravel : `success`, `error`, `warning` dans session
- `HandleInertiaRequests` les expose à Inertia
- **Un seul** listener global `flashToasts.js` (pas de doublons)
- Barre de chargement : `GlobalLoadingBar` liée aux événements Inertia `start`/`finish`

---

## Variables d’environnement

Voir `.env.example`. Résumé :

| Variable | Rôle |
|----------|------|
| `APP_*` | Nom, URL, debug, clé |
| `APP_PORT` | Port HTTP Sail (ex. 8088) |
| `DB_*` | PostgreSQL (`DB_HOST=pgsql` dans Docker) |
| `FORWARD_DB_PORT` | Port hôte PostgreSQL |
| `REDIS_*`, `FORWARD_REDIS_PORT` | Redis |
| `CACHE_STORE`, `CACHE_APP_STORE` | Cache |
| `SESSION_DRIVER` | `database` par défaut |
| `MAIL_*` | SMTP ; Mailpit en local |
| `FORWARD_MAILPIT_*` | Ports Mailpit |
| `BROADCAST_CONNECTION` | `reverb` |
| `REVERB_*` | Serveur WebSocket |
| `VITE_REVERB_*` | Client Echo (préfixe VITE_ obligatoire) |
| `VITE_PORT` | Port dev Vite |
| `QUEUE_CONNECTION` | `database` |

Production : `SESSION_SECURE_COOKIE`, `TRUSTED_PROXIES`, `APP_URL` HTTPS — voir `RAILWAY.md`.

---

## Docker / Laravel Sail

Services typiques :

| Service | Rôle |
|---------|------|
| laravel.test | PHP + Nginx |
| pgsql | PostgreSQL |
| redis | Cache |
| mailpit | E-mails dev |

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run dev
./vendor/bin/sail artisan reverb:start   # terminal séparé
```

---

## Scripts NPM et commandes

| Script | Commande |
|--------|----------|
| Dev | `npm run dev` |
| Build prod | `npm run build` |
| Tout-en-un | `npm run start-all` (Sail + Vite + Reverb + queue) |

| Artisan utile | |
|----------------|--|
| Tests | `./vendor/bin/sail artisan test` |
| Migrations | `./vendor/bin/sail artisan migrate:fresh --seed` |
| Clear | `./vendor/bin/sail artisan optimize:clear` |

---

## Seeders et comptes de démo

Ordre : `SuperAdminUserSeeder` → `AdminUserSeeder` → `EmployeSeeder` → `PresenceSeeder` (selon `DatabaseSeeder`).

| Rôle | E-mail (exemple seed) | Mot de passe |
|------|------------------------|--------------|
| Super admin | `k8e6Y@example.com` | `admin123` |
| Admin Adidas | `k8e6ddYdd@example.com` | `admin123` |
| Employé | `k8e6sdsdddYdd@example.com` | `admin123` |

Vérifier les seeders pour la liste complète des comptes fictifs.

---

## Suite de tests

51 tests (`./vendor/bin/sail artisan test`), dont :

| Fichier | Sujet |
|---------|--------|
| `AuthTest` | Login, logout, remember |
| `RoleMiddlewareTest` | Accès par rôle |
| `AdminManagementTest` | CRUD admin + OTP |
| `EmployeManagementTest` | CRUD employé + OTP |
| `EmailVerificationTest` | Validation formulaire avant modal, OTP |
| `QrSessionTest` | Dashboard QR, refresh |
| `CheckInTest` | Pointage |
| `ScanAccessTest` | Accès page scan |
| `DashboardTest` | Pages dashboards |
| `NotificationTest` | Notifications |
| `RapportTest` | Export CSV |
| `ContactTest` | Contact |
| `EmployeCheckedInEventTest` | Broadcast |
| `AppSmokeTest` | Pages, update employé, logout, accès |

---

## Déploiement production

- Guide général : **[docs/DEPLOY.md](docs/DEPLOY.md)** (checklist, variables, processus, tests)
- Railway : **[RAILWAY.md](RAILWAY.md)**

Fichiers : `nixpacks.toml`, `railway.toml`, `Procfile`, `AppServiceProvider` (HTTPS), `trustProxies`.

**Reverb en production :** service séparé recommandé ; configurer `REVERB_HOST` / `VITE_REVERB_*`.

---

## Arborescence utile

```
app/
├── Events/
├── Http/Controllers/{Admin,Auth,Employe,Routing,}
├── Http/Middleware/
├── Http/Requests/
├── Mail/
├── Models/
├── Services/
└── Support/

resources/js/
├── Pages/
├── components/
├── composables/
├── Layouts/
├── stores/
└── utils/

database/migrations/
database/seeders/
routes/web.php
routes/channels.php
tests/Feature/
```

---

## Dépannage rapide

| Problème | Piste |
|----------|--------|
| Page blanche / `component` null | Vérifier dossier `Pages/` (P majuscule), Vite lancé |
| `ViteManifestNotFoundException` | `npm run dev` ou `npm run build` |
| QR double-clic erreur | Corrigé : plus de cache modèle Eloquent dans `QrService` |
| Pas de temps réel | `reverb:start` + variables `VITE_REVERB_*` |
| Code OTP introuvable | Mailpit 8026 ou logs mail SMTP |
| Modal OTP sans validation | Vérifier que tous les champs requis sont remplis |
| Erreurs sous les champs | Comportement normal si validation échoue avant modal |

---

## Documents annexes

- **[docs/DEPLOY.md](docs/DEPLOY.md)** — Déploiement production (général)
- **[RAILWAY.md](RAILWAY.md)** — Déploiement Railway
- **[docs/LARAVEL_SAIL_TEMPLATE.md](docs/LARAVEL_SAIL_TEMPLATE.md)** — Guide générique Sail / Vite / troubleshooting ports (hérité du template)

---

## Licence

Projet privé / éducatif — adapter selon votre organisation.
