#!/bin/sh
set -e

cd /var/www/html

# ── Generate APP_KEY if not set ──────────────────────────
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));')
    export APP_KEY
fi

# ── Laravel setup ────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# ── Wait for DB if configured, then migrate ──────────────
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database $DB_HOST:$DB_PORT..."
    for i in $(seq 1 15); do
        nc -z "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null && break
        sleep 2
    done
    php artisan migrate --force 2>/dev/null || true
fi

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"
