#!/bin/sh
set -e

cd /var/www/html

# ── Wait for database if needed ──────────────────────────
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database at $DB_HOST:$DB_PORT..."
    max_attempts=30
    attempt=0
    while ! nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge "$max_attempts" ]; then
            echo "Warning: Database not reachable after $max_attempts attempts, continuing..."
            break
        fi
        sleep 2
    done
fi

# ── Generate APP_KEY if not set ──────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    APP_KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));')
    export APP_KEY
fi

# ── Setup storage & cache ───────────────────────────────
php artisan storage:link --force 2>/dev/null || true

php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# ── Run migrations ───────────────────────────────────────
php artisan migrate --force 2>/dev/null && echo "Migrations applied." || true

echo "MOS OFPPT — Ready."
exec "$@"
