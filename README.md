# dARK Dashboard Web

Laravel 8 + AdminLTE administration application of the dARK platform. It manages
authorities, institutions, NAANs, ARK identifiers, accounts and wallets, the
blockchain node configuration, and the metadata/replication worker overview. It
also hosts the platform liveness page ("dARK Liveness") and the end-to-end
platform tests.

## Stack

- PHP 8, deployed with the `ambientum/php:8.0-nginx` image (nginx + PHP-FPM)
- laravel/framework (8.x) with `jeroennoten/laravel-adminlte`, `laravel/ui`,
  `laravelcollective/html`, `nesbot/carbon`, `bensampo/laravel-enum`,
  `fruitcake/laravel-cors`, `guzzlehttp/guzzle`, `laravel/sanctum`,
  `laravel/tinker`
- MySQL 8.0 (`dashboard-mysql` service, database `dark`)
- node & npm; `leaflet` for maps

---

## Deployment

dashboard-web is deployed by the [dark-deployer](../../README.md) using the deployment v3 pipeline: an operator inventory (see [examples/operator-inventory/](../../examples/operator-inventory/)) is resolved, planned, and rendered into per-machine Docker Compose bundles, then applied with `./deploy.sh install` (locally) or `prepare`/`push`/`apply` (remote hosts). The runtime image (`ambientum/php:8.0-nginx`), the MySQL image and the component checkout branch (`main`) are pinned in `deployment_v3/catalog_data/dark-platform-baseline-v1.0.json`. There is no standalone docker-compose file in this component.

The rendered application bundle contains three services for this component:

| Service             | Role                                                                                                        |
| ------------------- | ----------------------------------------------------------------------------------------------------------- |
| `dashboard`         | Laravel application served by the pinned `ambientum/php:8.0-nginx` image; the component checkout is bind-mounted at `/var/www/app` |
| `dashboard-mysql`   | MySQL 8.0 database                                                                                          |
| `dashboard-migrate` | One-shot setup container: `php composer.phar install --no-dev`, `php artisan optimize:clear`, then `php artisan migrate --force --seed` |

Each service receives an env file rendered from the resolved inventory plus the
shared `dashboard-runtime-env` secret (private runtime values such as the
database password, shared by the dashboard and its database).

### Public URL prefix and proxy headers

The dashboard is never published directly. The edge proxy exposes it under the
route declared in the operator inventory (for the local-ha example, `/admin/`
on the loopback gateway, i.e. `http://localhost/admin/`). The gateway forwards
the standard proxy headers and sets `X-Forwarded-Prefix` to the public mount
path. The application's `TrustProxies` middleware (`app/Http/Middleware/TrustProxies.php`)
trusts all proxies and all `X-Forwarded-*` headers — including
`HEADER_X_FORWARDED_PREFIX` — so Laravel generates absolute URLs, redirects and
session cookies under the public prefix. The renderer pins `APP_URL` and
`ASSET_URL` to the public gateway URL and keeps the upstream cookie path at `/`
(nginx rewrites the cookie path to the public path with `proxy_cookie_path`).
The container is intentionally HTTP-only; TLS terminates at the edge proxy.

### Runtime configuration

Environment injected into `dashboard.env` by the renderer (every value is
derived from the inventory graph; local-ha example values shown):

| Variable                                            | Rendered value                        | Purpose                                              |
| --------------------------------------------------- | ------------------------------------- | ---------------------------------------------------- |
| `APP_URL`, `ASSET_URL`                              | `http://localhost/admin`              | Absolute base URL for generated links and assets      |
| `SESSION_PATH`                                      | `/`                                   | Upstream cookie path (rewritten by the gateway)       |
| `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`             | `dashboard-mysql`, `dark`, `dark`     | Database connection (password from the runtime secret) |
| `ADMIN_API_BASE_URL`                                | `http://admin-api:8000`               | Admin API                                            |
| `MINTER_BASE_URL`                                   | `http://minter-api:8001`              | Minter API                                           |
| `RESOLVER_BASE_URL`                                 | `http://resolver-api:8002`            | Resolver API                                         |
| `STORE_API_BASE_URL`                                | `http://store-api:8003`               | Metadata store API                                   |
| `WORKER_STATUS_URL`                                 | `http://minter-api:8001/api/v1/worker/status` | Worker status feed                            |
| `BLOCK_NUMBER`                                      | `http://rpc01:8545`                   | Primary RPC node URL (site-local RPC endpoint)        |
| `LIVENESS`                                          | `http://rpc01:8545/liveness`          | RPC liveness endpoint                                |
| `IPFS_API_BASE_URL`                                 | `http://ipfs-storage-a:5001`          | IPFS Kubo API                                        |
| `IPFS_CLUSTER_API_URL`                              | `http://cluster-storage-a:9094`       | IPFS Cluster API                                     |
| `BLOCK_EXPLORER_URL`                                | `http://localhost/explorer/`          | Public explorer route (empty when the plan has no explorer) |
| `GRAFANA_URL`                                       | `http://localhost:3000/dashboards`    | Grafana UI; set only for local monitoring stacks, left empty on remote hosts until an authenticated public monitoring endpoint is configured |
| `PROMETHEUS_URL`                                    | `http://localhost:9090/targets`       | Prometheus UI; same policy as `GRAFANA_URL`           |

### Liveness and status hub

The administrator menu links the **dARK Liveness** page (route
`dashboard/tests/api-health`), which:

- runs live checks against the platform endpoints configured through
  `BLOCK_NUMBER`, `LIVENESS`, `ADMIN_API_BASE_URL`, `MINTER_BASE_URL`,
  `RESOLVER_BASE_URL`, `STORE_API_BASE_URL`, `IPFS_API_BASE_URL` and
  `IPFS_CLUSTER_API_URL`, showing status, HTTP code and latency per endpoint,
  with an editable URL per row;
- shows observability links — **Grafana** and **Prometheus** (administrators
  only) and **Block Explorer** (all profiles) — taken from `GRAFANA_URL`,
  `PROMETHEUS_URL` and `BLOCK_EXPLORER_URL`; a link without a configured URL
  renders a disabled "Not configured" button.

### Default credentials

Created by the migrations/seeders run by `dashboard-migrate` (password
`password` for all accounts):

| Profile       | Email                        |
| ------------- | ---------------------------- |
| Administrator | `admin@darkpid.com`          |
| Institution   | `instituition@darkpid.com`   |
| User          | `user@darkpid.com`           |

### Legacy installer

`install.py` automated the previous docker-compose-based setup. That compose
file was removed when the platform moved to deployment v3, so the standalone
flow (creating `.env` from `.env.example` and running `docker compose up`) no
longer applies. The script retains a managed mode: with
`DARK_DEPLOYER_MANAGED_COMPOSE=1` it skips container startup and performs its
dependency/migration steps against deployer-managed containers. The v3
pipeline does not invoke this script — dependency installation and migrations
are handled by the `dashboard-migrate` service.