# ───────────────────────────────────────────────────────────
# Stage 1 : Build React Frontend
# ───────────────────────────────────────────────────────────
ARG VITE_API_URL=""
FROM node:20-alpine AS frontend-build

ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}

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
    postgresql-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        zip

# ── Composer ─────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Laravel application ──────────────────────────────────
WORKDIR /var/www/html

COPY backend/ .

# Create a temporary .env so Laravel scripts (package:discover, etc.) can run
RUN if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || true; fi

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Remove the temporary .env — start.sh will create it at runtime from env vars
RUN rm -f .env

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
