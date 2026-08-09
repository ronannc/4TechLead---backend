# Laravel Cloud Deployment

This backend is the Laravel application that should be deployed to Laravel Cloud. The repository is a workspace, so choose `backend` as the application root when Cloud detects multiple projects.

## Application Setup

- Create an application from the existing Git repository.
- Select the `backend` directory as the deploy root.
- Use PHP `8.5` if available, matching the local Docker setup. PHP `8.3+` satisfies `composer.json`.
- Attach a PostgreSQL database resource.
- Keep the Flutter app deployed separately; it consumes this API through `APP_URL/api/v1`.

## Build Commands

Configure these commands in the environment deployment settings:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan optimize
```

This API currently serves only static documentation assets from `resources/docs`, so a Node/Vite build is not required for the first deploy. Add `npm ci && npm run build` only after committing `package-lock.json` and confirming Node is available in the Cloud build image.

Do not run `php artisan optimize:clear`, `php artisan queue:restart`, or `php artisan storage:link` during deployment. Laravel Cloud restarts workers automatically and the filesystem is ephemeral.

## Deploy Commands

Use only essential release-time commands:

```bash
php artisan migrate --force
```

Run one-off maintenance commands from the Laravel Cloud environment Commands tab when needed.

## Environment Variables

Use `.env.cloud.example` as the production checklist. Generate `APP_KEY` locally with:

```bash
php artisan key:generate --show
```

Laravel Cloud injects database credentials for attached resources. Do not copy Docker-only values such as `DB_HOST=postgres`, MinIO credentials, or local Redis passwords from `.env.example`.

Recommended production values:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://<cloud-domain-or-custom-domain>`
- `DB_CONNECTION=pgsql`
- `CACHE_STORE=database` or `redis` when a KV store is attached
- `SESSION_DRIVER=database` or `redis` when a KV store is attached
- `QUEUE_CONNECTION=database` unless a managed queue/KV strategy is configured
- `FILESYSTEM_DISK=local` until uploads need persistence; use `s3` only when Laravel Object Storage is attached

Avoid setting `LOG_CHANNEL` and `LOG_STACK` unless you intentionally want to override Laravel Cloud logging. The platform should keep logs on stderr for the Cloud UI.

## Workers And Scheduler

The current API does not define scheduled tasks or queued jobs, but the jobs tables exist and `QUEUE_CONNECTION=database` is production-safe. If background jobs are added, create a Worker in Laravel Cloud with:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

No scheduler process is required until commands are registered in `routes/console.php` or `app/Console`.

## Health And API Checks

Laravel exposes a health endpoint at:

```text
GET /up
```

After deploy, verify:

- `GET /up`
- `GET /docs`
- `GET /docs/openapi`
- `POST /api/v1/auth/login`
- Public webhooks with `Authorization: Bearer <webhook_token>` or `X-Integration-Token`.

## Frontend Configuration

After the backend domain is known, point Flutter API configuration to:

```text
https://<cloud-domain-or-custom-domain>/api/v1
```

For mobile builds, webhook integrations and authentication tokens are handled by the Laravel API; no Laravel Cloud secrets should be embedded in the Flutter app.
