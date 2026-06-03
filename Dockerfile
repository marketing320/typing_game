# ──────────────────────────────────────────────────────────────────────────────
# Stage 1 — Build Vite/JS assets
# ──────────────────────────────────────────────────────────────────────────────
FROM node:20-alpine AS assets

WORKDIR /app

COPY package*.json ./
RUN npm install --ignore-scripts --no-audit --no-fund

COPY . .
RUN npm run build


# ──────────────────────────────────────────────────────────────────────────────
# Stage 2 — PHP 8.4 production image (Nginx + PHP-FPM via Supervisor)
# ──────────────────────────────────────────────────────────────────────────────
FROM php:8.4-fpm-alpine AS app

# System packages
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    unzip \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        gd \
        zip \
        bcmath \
        opcache \
        pcntl \
        exif

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies (own layer for caching)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copy application source
COPY . .

# Overwrite with built frontend assets
COPY --from=assets /app/public/build ./public/build

# Finalise autoloader
RUN composer dump-autoload --optimize --no-dev

# Ensure required directories exist with correct ownership
RUN mkdir -p \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Run Nginx as www-data so it can read Laravel files
RUN sed -i 's/user nginx;/user www-data;/' /etc/nginx/nginx.conf 2>/dev/null || true

# Copy Docker config files
COPY docker/nginx.conf       /etc/nginx/nginx.conf
COPY docker/default.conf     /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini          /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/opcache.ini      /usr/local/etc/php/conf.d/99-opcache.ini
COPY docker/www.conf         /usr/local/etc/php-fpm.d/www.conf
COPY docker/entrypoint.sh    /entrypoint.sh
COPY docker/worker.sh        /worker.sh

RUN chmod +x /entrypoint.sh /worker.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
