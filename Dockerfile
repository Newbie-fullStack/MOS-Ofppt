# ───────────────────────────────────────────────────────────
# Stage 1 : Build React Frontend
# ───────────────────────────────────────────────────────────
FROM node:20-alpine AS frontend-build

WORKDIR /build

COPY frontend/package*.json ./
RUN npm ci

COPY frontend/ .
RUN npm run build

# ───────────────────────────────────────────────────────────
# Stage 2 : Production — PHP-FPM + Nginx + Laravel
# ───────────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS production

# ── System dependencies ──────────────────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    libzip-dev \
    oniguruma-dev \
    netcat-openbsd \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        bcmath \
        zip

# ── Composer ─────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Application ──────────────────────────────────────────
WORKDIR /var/www/html

COPY backend/ .

RUN composer install --no-dev --optimize-autoloader --no-interaction

# ── Frontend build into Laravel public dir ───────────────
COPY --from=frontend-build /build/dist /var/www/html/public

# ── Storage permissions ─────────────────────────────────
RUN mkdir -p /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ── Nginx config (Alpine: /etc/nginx/http.d/) ───────────
RUN mkdir -p /etc/nginx/http.d
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ── Supervisor config ────────────────────────────────────
RUN mkdir -p /etc/supervisor.d
COPY docker/supervisord.conf /etc/supervisor.d/supervisord.conf

# ── Entrypoint script ────────────────────────────────────
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor.d/supervisord.conf"]
