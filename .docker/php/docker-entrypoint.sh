#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    echo "[entrypoint] .env not found, copying .env.example"
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    echo "[entrypoint] vendor/ not found, running composer install"
    composer install --no-interaction --no-progress
fi

if [ -f .env ] && ! grep -q "^APP_KEY=.\+" .env; then
    echo "[entrypoint] APP_KEY empty, generating"
    php artisan key:generate --no-interaction
fi

exec "$@"
