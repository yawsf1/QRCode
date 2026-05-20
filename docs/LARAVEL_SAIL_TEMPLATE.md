# Laravel + Sail + PostgreSQL + Inertia + Vue 3 + Pinia + SCSS

### Reusable Template — Battle-Tested Setup (2026)

**Stack:**

- Laravel 11
- Docker via Laravel Sail
- PostgreSQL
- Inertia.js v2
- Vue 3 (Composition API)
- Pinia
- SCSS / Sass
- Vite
- Pure JavaScript (no TypeScript, no Tailwind, no shadcn)

---

## PART 1 — First-Time Project Creation

> **Only do this section once**, when creating the template from scratch.  
> If you're cloning an existing repo, skip to **Part 2**.

---

### Step 1 — Create the Laravel project with PostgreSQL baked in

```bash
curl -s "https://laravel.build/my-project?with=pgsql,redis,mailpit" | bash
cd my-project
```

> ⚠️ **The `?with=pgsql` flag is critical.** It generates the correct `docker-compose.yml` automatically with PostgreSQL, avoiding all the manual YAML editing and the formatting errors you'd otherwise face.

---

### Step 2 — Set the alias (optional but highly recommended)

```bash
alias sail='./vendor/bin/sail'
```

To make it permanent across terminal sessions:

```bash
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc
```

From this point on, all commands use `sail` instead of `./vendor/bin/sail`.

---

### Step 3 — Configure your `.env` file

Open `.env` and set these values:

```env
APP_NAME=MyProject
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Change this if port 80 is already in use on your machine
APP_PORT=80

# Change this if port 5173 is already in use
VITE_PORT=5173

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=my_project
DB_USERNAME=sail
DB_PASSWORD=password
```

> ⚠️ **`DB_HOST` must be `pgsql`, not `localhost` or `127.0.0.1`.**  
> Inside Docker, `localhost` refers to the container itself, not the database container.  
> Laravel finds the database container by its service name: `pgsql`.

> ⚠️ **If port 80 is already in use** (by another project, Apache, or a previous Sail container):
>
> ```env
> APP_PORT=8080
> ```
>
> Then access your app at `http://localhost:8080`.

> ⚠️ **If port 5173 is already in use:**
>
> ```env
> VITE_PORT=5174
> ```

---

### Step 4 — Start Docker containers

```bash
sail up -d
```

Check that all containers are running:

```bash
sail ps
```

You should see all containers with status `Up` — specifically `pgsql`, `redis`, and `laravel.test`.

> ⚠️ **Wait 10–15 seconds after `sail up -d` before running migrations.**  
> PostgreSQL needs a few seconds to fully initialize inside the container.  
> Running `sail artisan migrate` too quickly results in a "Connection refused" error.

---

### Step 5 — Install Inertia server-side

```bash
sail composer require inertiajs/inertia-laravel
```

Generate the Inertia middleware:

```bash
sail artisan inertia:middleware
```

Register the middleware in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
})
```

---

### Step 6 — Install frontend dependencies

```bash
sail npm install vue @inertiajs/vue3 pinia
sail npm install -D @vitejs/plugin-vue sass
```

> ⚠️ **Always run `npm` commands through Sail (`sail npm ...`), never directly.**  
> `npm install` on your host installs packages into your local `node_modules`.  
> Vite runs _inside_ the Docker container, so it uses the container's `node_modules`.  
> They are not the same. If you run `npm` directly, Vite will not find the packages.

---

### Step 7 — Configure Vite

Replace `vite.config.js` entirely:

```js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/scss/app.scss", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
    ],

    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
});
```

---

### Step 8 — Create the SCSS structure

Create the folders and files:

```bash
mkdir -p resources/scss/base
touch resources/scss/app.scss
touch resources/scss/base/_reset.scss
touch resources/scss/base/_variables.scss
touch resources/scss/base/_global.scss
```

**`resources/scss/base/_reset.scss`**

```scss
*,
*::before,
*::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
```

**`resources/scss/base/_variables.scss`**

```scss
// Colors
$primary: #2563eb;
$secondary: #64748b;
$danger: #dc2626;
$success: #16a34a;
$white: #ffffff;
$black: #0f172a;

// Layout
$radius: 8px;
$shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
$transition: 0.3s ease;

// Typography
$font-base: "Arial", sans-serif;
$font-size: 16px;
```

**`resources/scss/base/_global.scss`**

```scss
@use "variables" as *;

body {
    font-family: $font-base;
    font-size: $font-size;
    background: #f8fafc;
    color: #1e293b;
    min-height: 100vh;
}

a {
    color: $primary;
    text-decoration: none;

    &:hover {
        text-decoration: underline;
    }
}

.container {
    width: min(1200px, 90%);
    margin: 0 auto;
}
```

**`resources/scss/app.scss`**

```scss
@use "./base/reset";
@use "./base/variables";
@use "./base/global";

// Add component-level SCSS imports below as your project grows:
// @use './components/button';
// @use './components/card';
// @use './pages/home';
```

---

### Step 9 — Create the Blade root template

Delete the default `welcome.blade.php` if it exists, then create:

**`resources/views/app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'App') }}</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

---

### Step 10 — Bootstrap Vue + Inertia + Pinia

Replace `resources/js/app.js` entirely:

```js
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { createPinia } from "pinia";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];

        // Debug helper — shows clearly what failed and what was available
        if (!page) {
            console.error(`[Inertia] Page not found: "${name}"`);
            console.error("[Inertia] Available pages:", Object.keys(pages));
        }

        return page;
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .mount(el);
    },
});
```

> ⚠️ **The folder name is `Pages` (capital P).** This must match exactly — Linux is case-sensitive.  
> `Pages/Home.vue` and `pages/Home.vue` are completely different paths on Ubuntu.  
> The glob pattern `./Pages/**/*.vue` will silently find nothing if the folder is named `pages`.

---

### Step 11 — Create the Pages folder and first page

```bash
mkdir -p resources/js/Pages
```

**`resources/js/Pages/Home.vue`**

```vue
<template>
    <div class="home">
        <div class="container">
            <h1>Laravel + Inertia + Vue is working</h1>
            <p>Count: {{ counter.count }}</p>

            <div class="actions">
                <button @click="counter.increment">+</button>
                <button @click="counter.decrement">−</button>
                <button @click="counter.reset">Reset</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useCounterStore } from "@/stores/counter";

const counter = useCounterStore();
</script>

<style scoped lang="scss">
.home {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    p {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .actions {
        display: flex;
        gap: 1rem;

        button {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s ease;

            &:hover {
                background: #1d4ed8;
                transform: translateY(-1px);
            }
        }
    }
}
</style>
```

---

### Step 12 — Create the Pinia store

```bash
mkdir -p resources/js/stores
```

**`resources/js/stores/counter.js`**

```js
import { defineStore } from "pinia";
import { ref } from "vue";

export const useCounterStore = defineStore("counter", () => {
    const count = ref(0);

    const increment = () => count.value++;
    const decrement = () => count.value--;
    const reset = () => (count.value = 0);

    return { count, increment, decrement, reset };
});
```

---

### Step 13 — Set up the route

**`routes/web.php`**

```php
<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});
```

> ⚠️ The string passed to `Inertia::render()` must exactly match the filename (without `.vue`).  
> `'Home'` → looks for `Pages/Home.vue`. Case matters.

---

### Step 14 — Run database migrations

```bash
sail artisan migrate
```

If Laravel asks "Would you like to create it?" — answer **Yes**.

---

### Step 15 — Start Vite and verify

```bash
sail npm run dev
```

Open your browser at `http://localhost` (or `http://localhost:8080` if you changed `APP_PORT`).

You should see the Home page rendered by Vue through Inertia.

> ⚠️ **The Vite dev server must stay running** in a separate terminal while you develop.  
> Do not close it. Laravel's `@vite()` directive connects to it in real time.  
> If Vite is not running, Laravel throws `ViteManifestNotFoundException`.

---

## PART 2 — Reusing the Template (Cloning from GitHub)

Every time you start a new project from this template:

```bash
git clone https://github.com/your-username/your-template.git new-project
cd new-project
```

Then run these **in this exact order**:

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Install PHP dependencies on your HOST machine (not through Sail)
#    This generates the vendor/ folder, which contains Sail itself
#    --ignore-platform-reqs bypasses PHP version mismatch between host and container
composer install --ignore-platform-reqs

# 3. Start Docker (now that vendor/bin/sail exists)
./vendor/bin/sail up -d

# 4. Generate app key (MANDATORY — each project needs its own unique key)
./vendor/bin/sail artisan key:generate

# 5. Install JS dependencies
./vendor/bin/sail npm install

# 6. Run migrations
./vendor/bin/sail artisan migrate

# 7. Start Vite
./vendor/bin/sail npm run dev
```

> ⚠️ **`composer install` must run on your host machine first**, before anything else.  
> The `vendor/` folder is not committed to Git (it's in `.gitignore`).  
> Sail lives inside `vendor/bin/sail` — if vendor doesn't exist, you get:  
> `bash: ./vendor/bin/sail: No such file or directory`  
> Run `composer install` on your machine once to bootstrap it, then use Sail for everything after.

> ⚠️ **`key:generate` is not optional.** Without it: sessions break, cookies break, auth breaks,  
> and you will get cryptic errors that seem unrelated to the key.

> ⚠️ **Wait ~15 seconds after `sail up -d` before running `migrate`.**  
> PostgreSQL takes a few seconds to fully initialize inside the container.

---

## PART 3 — Pushing to GitHub

### First push (from the original project)

```bash
# 1. Initialize git
git init

# 2. Stage all files
git add .

# 3. First commit
git commit -m "Initial template: Laravel + Sail + PostgreSQL + Inertia + Vue + SCSS"
```

Go to GitHub and create a **new empty repository**:

- No README
- No `.gitignore`
- No license

Then connect and push:

```bash
git remote add origin https://github.com/your-username/your-repo.git
git branch -M main
git push -u origin main
```

### What the `.gitignore` must exclude

Laravel's default `.gitignore` already covers most of this, but verify these are present:

```
/vendor
/node_modules
/.env
/storage/logs/*.log
/public/build
/public/hot
```

> ⚠️ **Never push `.env` to GitHub.** It contains secrets (app key, DB credentials).  
> Push `.env.example` instead, with empty or placeholder values.

---

## PART 4 — Folder Structure

```
project/
├── app/
├── bootstrap/
├── database/
│   └── migrations/
├── resources/
│   ├── js/
│   │   ├── Pages/           ← Vue pages (capital P, always)
│   │   │   └── Home.vue
│   │   ├── stores/          ← Pinia stores
│   │   │   └── counter.js
│   │   └── app.js           ← Inertia bootstrap
│   ├── scss/
│   │   ├── app.scss         ← Main entry (imported by Vite)
│   │   └── base/
│   │       ├── _reset.scss
│   │       ├── _variables.scss
│   │       └── _global.scss
│   └── views/
│       └── app.blade.php    ← Single Blade file, Inertia root
├── routes/
│   └── web.php
├── .env
├── .env.example             ← Committed to Git
├── .gitignore
├── vite.config.js
├── package.json
├── composer.json
└── docker-compose.yml       ← Generated by Sail, don't edit manually
```

---

## PART 5 — Troubleshooting Reference

> This section covers every real error encountered during the development of this template.  
> Read it top to bottom once so you know what to do when something breaks.

---

### Port already in use (general rule)

This is the most common error when running multiple Laravel projects on the same machine.  
Each project must use unique ports. Configure them in `.env` **before** running `sail up -d`.

| Service    | Internal port | `.env` variable                  | Recommended non-default |
| ---------- | ------------- | -------------------------------- | ----------------------- |
| App (HTTP) | 80            | `APP_PORT`                       | `8088`                  |
| Vite       | 5173          | `VITE_PORT`                      | `5174`                  |
| PostgreSQL | 5432          | `FORWARD_DB_PORT`                | `5433`                  |
| Redis      | 6379          | `FORWARD_REDIS_PORT`             | `6380`                  |
| Mailpit    | 8025          | `FORWARD_MAILPIT_DASHBOARD_PORT` | `8026`                  |

> ⚠️ The **internal** ports (`DB_PORT=5432`, `REDIS_PORT=6379`) must never be changed —  
> those are used by Laravel inside Docker. Only the `FORWARD_*` ports affect your host machine.

**Always run `sail down` when switching between projects** to free up ports:

```bash
cd ~/project-a && sail down
cd ~/project-b && sail up -d
```

---

### `failed to bind host port 0.0.0.0:80/tcp: address already in use`

Another container or process is holding port 80.

**Step 1 — stop all running Docker containers across all projects:**

```bash
docker stop $(docker ps -q)
sail up -d
```

**Step 2 — if something non-Docker is using port 80:**

```bash
sudo lsof -i :80
sudo kill -9 <PID>
sail up -d
```

**Step 3 — if it keeps happening, just use a different port permanently:**

```env
APP_PORT=8088
```

---

### `failed to bind host port 0.0.0.0:6379/tcp: address already in use`

Redis port conflict — usually a leftover container from another project.

```bash
docker stop $(docker ps -q)
sail up -d
```

Or permanently in `.env`:

```env
FORWARD_REDIS_PORT=6380
```

---

### `failed to bind host port 0.0.0.0:5432/tcp: address already in use`

PostgreSQL port conflict — same cause as Redis above.

```bash
docker stop $(docker ps -q)
sail up -d
```

Or permanently in `.env`:

```env
FORWARD_DB_PORT=5433
```

---

### `service "laravel.test" is not running`

This appears right after a port conflict error — the Laravel container failed to start because a port was taken.  
Fix the port conflict first (see above), then:

```bash
sail down
sail up -d
```

---

### `curl error 6: Could not resolve host: repo.packagist.org`

The Docker container has no internet access. This is a DNS problem inside Docker, very common on Ubuntu.

**Fix — set Docker DNS to use Google's servers:**

```bash
sail down
sudo nano /etc/docker/daemon.json
```

Paste:

```json
{
    "dns": ["8.8.8.8"]
}
```

Save with `Ctrl+X` → `Y` → `Enter`, then:

```bash
sudo systemctl restart docker
sail up -d
```

Test that it works before running Composer:

```bash
sail exec laravel.test curl -v https://google.com 2>&1 | head -5
```

You should see `Host google.com:443 was resolved`. Then run your Composer command.

> ⚠️ **Why this happens:** Ubuntu uses `systemd-resolved` for DNS, which Docker sometimes  
> cannot reach from inside containers. Forcing `8.8.8.8` in `daemon.json` bypasses this entirely.

> ⚠️ **Do not use your router IP** (e.g. `192.168.0.1`) as Docker DNS — it is not reachable  
> from inside Docker's bridge network.

---

### `ViteManifestNotFoundException`

```
Vite manifest not found at: /var/www/html/public/build/manifest.json
```

Vite is not running. Laravel cannot load any frontend assets.

**Fix for development:**

```bash
sail npm run dev
```

Keep this terminal open the entire time you are developing.

**Fix for a one-off test:**

```bash
sail npm run build
```

> ⚠️ `@vite()` in your Blade file requires either the dev server to be running  
> OR `npm run build` to have been executed. Without one of these, you get this error.

---

### Blank / white page

The page loads (no Laravel error) but the screen is empty.

**Cause:** Inertia could not find and load the Vue page component.

**Checklist — check these in order:**

1. Does `resources/js/Pages/Home.vue` exist?
2. Is the folder named `Pages` with a **capital P**? (Linux is case-sensitive — `pages` ≠ `Pages`)
3. Does `Inertia::render('Home')` in `web.php` match the filename exactly?
4. Is Vite running (`sail npm run dev`)?
5. Open browser DevTools → Console tab — the debug helper in `app.js` will print exactly which page name failed and list all pages it found.

---

### `Cannot read properties of null (reading 'component')`

```
Uncaught TypeError: Cannot read properties of null (reading 'component')
```

Inertia's `resolve()` function returned `null` — meaning it found no matching page file.

**Fix — open `app.js` and check the glob pattern:**

```js
const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
const page = pages[`./Pages/${name}.vue`];
```

Then verify the folder exists and is named correctly:

```bash
ls resources/js/Pages
```

If the folder is named `pages` (lowercase), rename it:

```bash
mv resources/js/pages resources/js/Pages
```

---

### `Cannot read properties of undefined (reading 'default')`

Older versions of this setup used `.default` at the end of the resolver. The current `app.js` in this template does not need it. If you see this error, make sure your `resolve()` looks exactly like the one in Step 10 of this guide.

---

### `SQLSTATE[HY000] [2002] Connection refused`

Laravel connected to PostgreSQL before the container finished starting up.

**Fix — wait 15 seconds, then try again:**

```bash
sail artisan migrate
```

If it still fails, check that the pgsql container is actually running:

```bash
sail ps
```

---

### `database "x" does not exist`

The PostgreSQL container was reset or recreated and the database is gone.

**Fix:**

```bash
sail artisan migrate
```

Laravel will ask: `"Would you like to create it?"` — answer **Yes**.

If that still fails, create it manually:

```bash
sail exec pgsql psql -U sail -d postgres -c "CREATE DATABASE your_db_name;"
sail artisan migrate
```

> ⚠️ When connecting to psql manually, always specify `-d postgres` as the initial database.  
> Running `psql -U sail` without `-d` tries to connect to a database named `sail`, which doesn't exist.

---

### `Bind for 0.0.0.0:1026 failed: port is already allocated`

A container from another project is still running and holding the port.

**Fix — stop all containers across all projects first:**

```bash
docker stop $(docker ps -q)
./vendor/bin/sail up -d
```

If you need both projects running simultaneously, give each project unique ports in `.env`:

```env
FORWARD_MAILPIT_PORT=1027
FORWARD_MAILPIT_DASHBOARD_PORT=8027
```

> ⚠️ This applies to any port conflict, not just Mailpit. The pattern is always the same:  
> stop all containers, or assign unique `FORWARD_*` ports per project.

---

### `could not translate host name "pgsql" to address: Name or service not known`

Laravel started but cannot find the PostgreSQL container — either it failed to start or it hasn't finished initializing yet.

**Step 1 — check what is actually running:**

```bash
./vendor/bin/sail ps
```

The `pgsql` container must show as `Up (healthy)`. If it is missing or shows `Exit`, the container failed to start — almost always due to a port conflict.

**Step 2 — stop everything and restart cleanly:**

```bash
docker stop $(docker ps -q)
./vendor/bin/sail up -d
```

**Step 3 — wait 15 seconds, verify pgsql is healthy, then migrate:**

```bash
./vendor/bin/sail ps
./vendor/bin/sail artisan migrate
```

> ⚠️ Never run `sail artisan migrate` immediately after `sail up -d`.  
> PostgreSQL needs ~15 seconds to fully initialize inside the container.  
> Running migrations too early causes this exact error even when the container eventually starts fine.

---

### `Your lock file does not contain a compatible set of packages`

```
symfony/clock v8.x requires php >=8.4 -> your php version (8.3.x) does not satisfy that requirement.
```

**Cause:** The `composer.lock` was generated on a machine with PHP 8.4, but your host machine runs PHP 8.3. The lock file and your local PHP are incompatible.

**Why it doesn't matter:** Your host PHP is only used to bootstrap Sail. Once Docker is running, everything executes inside the container which has the correct PHP version. Your host PHP version is irrelevant after that.

**Fix — ignore platform requirements just for the initial install:**

```bash
composer install --ignore-platform-reqs
```

Then continue with Sail as normal:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

> ⚠️ Always use `--ignore-platform-reqs` when cloning this template on a machine  
> whose local PHP version differs from the one used to generate `composer.lock`.

---

### `could not find driver (pgsql)`

PHP inside the container is missing the PostgreSQL extension.

**Fix — rebuild the container:**

```bash
sail down -v
sail build --no-cache
sail up -d
```

---

### Everything is broken and nothing works

Full clean reset — run these in order:

```bash
sail down -v
docker network prune -f
sail up -d
sail artisan migrate
sail npm install
sail npm run dev
```

Then hard-refresh the browser with `Ctrl + Shift + R`.

---

## PART 6 — Daily Development Workflow

Every day when you start working:

```bash
# Terminal 1 — start Docker
sail up -d

# Terminal 2 — start Vite (keep open)
sail npm run dev
```

When you're done:

```bash
sail down
```

> ⚠️ **Always run `sail down` when finished**, not just close the terminal.  
> Leaving containers running holds ports (80, 5173, 5432) and causes conflicts  
> when you start other projects or restart your machine.

---

## PART 7 — Connecting DBeaver to PostgreSQL

DBeaver connects to the **host**, not the container. Use these settings:

| Field    | Value                            |
| -------- | -------------------------------- |
| Host     | `localhost`                      |
| Port     | `5432`                           |
| Database | your `DB_DATABASE` value         |
| Username | your `DB_USERNAME` (e.g. `sail`) |
| Password | your `DB_PASSWORD`               |

> ⚠️ Inside Laravel (`.env`): `DB_HOST=pgsql`  
> Inside DBeaver (external tool): host = `localhost`  
> These are different because DBeaver runs on your machine, not inside Docker.

---

## Quick Command Reference

```bash
# Docker
sail up -d          # Start all containers
sail down           # Stop all containers
sail down -v        # Stop and delete volumes (full reset)
sail ps             # Check container status
sail logs           # View container logs
sail shell          # Open shell inside Laravel container

# Laravel
sail artisan migrate
sail artisan migrate:fresh
sail artisan migrate:fresh --seed
sail artisan make:controller UserController
sail artisan make:model Product -m
sail artisan route:list
sail artisan optimize:clear

# Composer
sail composer require vendor/package
sail composer install

# NPM / Vite
sail npm install
sail npm run dev
sail npm run build

# PostgreSQL
sail psql -U sail -d your_database
```
