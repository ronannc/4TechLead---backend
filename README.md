# ForTechLead Backend

Laravel API used by the ForTechLead Flutter app. It owns authentication, teams, people, dailies, 1:1, PDI, KPIs, integrations, external webhooks, notifications, and generated delivery metrics.

## Local Development

Start the Docker services:

```bash
make up
```

Run migrations:

```bash
make migrate
```

Run the full backend test suite:

```bash
make test
```

Format changed PHP files:

```bash
make pint
```

The local API is exposed at:

```text
http://localhost:8090/api/v1
```

## API Documentation

OpenAPI documentation is available through Laravel routes:

```text
GET /docs
GET /docs/openapi
```

## Laravel Cloud

Deploy this project from the `backend` directory, not from the workspace root. Use the production checklist in:

```text
docs/laravel-cloud.md
```

Use `.env.cloud.example` as the environment-variable checklist. Do not copy Docker-only values from `.env.example` into Laravel Cloud.

Initial build command:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan optimize
```

Initial deploy command:

```bash
php artisan migrate --force
```

Minimum post-deploy checks:

```text
GET /up
GET /docs
POST /api/v1/auth/login
```
