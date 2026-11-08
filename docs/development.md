# Dashboard development

For the complete local platform and initial login, follow
[developer onboarding](https://github.com/LA-Referencia-IOI/dark-deployer/blob/main/docs/developer-onboarding.md).
The managed deployment mounts a **staged committed snapshot**, not your original
component checkout. Editing `components/dashboard-web` does not automatically
change the running Dashboard.

## Dependencies and assets

Work from this component directory. Match the deployed PHP 8.0 runtime where
possible and use Composer with the committed `composer.lock`. Install the PHP
extensions required by Composer, including PDO MySQL for the application and
PDO SQLite for the isolated database tests. Install Node/npm for Laravel Mix;
the repository does not currently specify a tested Node version.

```bash
php --version
composer install
npm ci
npm run dev
# During asset development:
npm run watch
# Before producing deployable assets:
npm run production
```

Do not use `composer update` to bootstrap: it changes dependency selection.
The deployment migration container uses `--no-dev`, while local development
needs dev dependencies for PHPUnit and IDE helpers. Build assets before
committing the files used by the deployed application.

## Separate local application

Create a local `.env` from `.env.example`, replace its sample `APP_KEY` using
`php artisan key:generate`, and set `APP_URL=http://127.0.0.1:8081`. The example
contains historical service URLs and example wallet material: they are not the
configuration of your new lab. Set all upstream URLs to endpoints reachable from
the PHP process. Container DNS names only work inside the deployment network;
host localhost ports work only when explicitly published. Do not copy an admin
blockchain private key into a browser/client integration.

Provision a separate MySQL 8.0 development database and configure `DB_HOST`,
`DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` for it. Then:

```bash
php artisan config:clear
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8081
```

Migrations and seeds write data; use your separate development database. The
seeded admin account is `admin@darkpid.com` / `password`. Set upstream Admin,
Minter, Resolver and Store URLs using the variables in the README. For full
integration without publishing private APIs, run the PHP development process on
the lab Docker network, with separate database/configuration and port 8081.
The deployed Dashboard already provides a working reference through `/admin/`.

## Tests

`phpunit.xml` selects the testing application environment but does not select an
isolated database. Create `.env.testing` with a separate application key and
explicit test database settings before running tests. Feature tests can migrate
or reset their database. For SQLite-capable tests, a typical configuration is:

```dotenv
APP_ENV=testing
APP_KEY=REPLACE_WITH_A_SEPARATE_GENERATED_LARAVEL_KEY
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

Use a disposable MySQL test database for tests that require MySQL-specific
behavior. Clear cached configuration, then run:

```bash
php artisan config:clear
php artisan test
```

The unit/feature suite is distinct from the Dashboard's live platform-test page,
which calls upstream services and can create on-chain and Minter test records.

## Deliver a change

Commit in this repository, update the Dashboard source pin in the lab inventory,
and review a deployer update for service `dashboard`. Use `--data-compatible`
only for changes compatible with existing SQL/files without migrations. A normal
update does not rerun completed migrations. If the change needs schema setup,
validate it on a new disposable lab through the installation workflow before
planning an explicit migration for existing data. Never modify the generated
managed environment or staged source tree to deliver the change.
