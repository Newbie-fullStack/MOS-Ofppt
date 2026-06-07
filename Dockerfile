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
FROM php:8.3-fpm-alpine

# ── System dependencies ──────────────────────────────────
RUN apk add --no-cache \
    nginx \
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

# ── Laravel application ──────────────────────────────────
WORKDIR /var/www/html

COPY backend/ .

RUN composer install --no-dev --optimize-autoloader --no-interaction

# ── Frontend build into Laravel public/ ──────────────────
COPY --from=frontend-build /build/dist /var/www/html/public

# ── Storage permissions ─────────────────────────────────
RUN mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ── Nginx config (full nginx.conf, replaces Alpine default) ─
RUN rm -f /etc/nginx/nginx.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
RUN nginx -t

# ── Startup script ───────────────────────────────────────
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
