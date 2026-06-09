#!/bin/sh
set -e

cd /var/www/html

# ── Create .env if missing (for Laravel) ─────────────────
if [ ! -f .env ]; then
    {
        echo "APP_NAME=\"MOS OFPPT\""
        echo "APP_ENV=${APP_ENV:-production}"
        echo "APP_KEY=${APP_KEY:-}"
        echo "APP_DEBUG=${APP_DEBUG:-false}"
        echo "APP_URL=${APP_URL:-https://mos-ofppt.fly.dev}"
        echo "APP_LOCALE=fr"
        echo "APP_TIMEZONE=Africa/Casablanca"
        echo ""
        echo "DB_CONNECTION=${DB_CONNECTION:-pgsql}"
        echo "DB_HOST=${DB_HOST:-}"
        echo "DB_PORT=${DB_PORT:-3306}"
        echo "DB_DATABASE=${DB_DATABASE:-mos_ofppt}"
        echo "DB_USERNAME=${DB_USERNAME:-}"
        echo "DB_PASSWORD=${DB_PASSWORD:-}"
        echo ""
        echo "CACHE_DRIVER=${CACHE_DRIVER:-file}"
        echo "SESSION_DRIVER=${SESSION_DRIVER:-file}"
        echo "QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}"
        echo ""
        echo "SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS:-mos-ofppt.fly.dev}"
        echo "FRONTEND_URL=${APP_URL:-https://mos-ofppt.fly.dev}"
        echo ""
        echo "MAIL_MAILER=${MAIL_MAILER:-smtp}"
        echo "MAIL_HOST=${MAIL_HOST:-}"
        echo "MAIL_PORT=${MAIL_PORT:-587}"
        echo "MAIL_USERNAME=${MAIL_USERNAME:-}"
        echo "MAIL_PASSWORD=${MAIL_PASSWORD:-}"
        echo "MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}"
        echo "MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-noreply@mos-ofppt.ma}"
        echo "MAIL_FROM_NAME=\"${MAIL_FROM_NAME:-MOS OFPPT}\""
        echo ""
        echo "FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}"
        echo "LOG_LEVEL=${LOG_LEVEL:-warning}"
    } > .env
fi

# ── Generate APP_KEY if not set ──────────────────────────
if [ -z "$APP_KEY" ] && grep -q '^APP_KEY=$' .env 2>/dev/null; then
    APP_KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));')
    sed -i "s|^APP_KEY=\$|APP_KEY=$APP_KEY|" .env
    export APP_KEY
fi

# ── Laravel setup ────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# ── Wait for DB if configured, then migrate ──────────────
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    DB_HOST="${DB_HOST:-$(echo "$DATABASE_URL" | sed 's|.*@||;s|:.*||')}"
    DB_PORT="${DB_PORT:-5432}"
    echo "Waiting for database $DB_HOST:$DB_PORT..."
    for i in $(seq 1 30); do
        nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null && break
        sleep 2
    done
    php artisan migrate --force 2>/dev/null || true
    php artisan db:seed --force 2>/dev/null || true
fi

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"
