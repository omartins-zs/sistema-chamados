# -----------------------------------------------------------------------------
# Base — PHP 8.3 + extensões Laravel
# -----------------------------------------------------------------------------
FROM php:8.3-fpm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-local.ini
COPY docker/php/fpm-performance.conf /usr/local/etc/php-fpm.d/zz-performance.conf

# -----------------------------------------------------------------------------
# Build — composer + vite (cache de camadas; roda só no docker build)
# -----------------------------------------------------------------------------
FROM base AS build

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm run build

# -----------------------------------------------------------------------------
# App — PHP-FPM
# -----------------------------------------------------------------------------
FROM base AS app

WORKDIR /var/www/html

COPY --from=build /var/www/html /var/www/html
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm", "-F"]

# -----------------------------------------------------------------------------
# Nginx — só os arquivos públicos
# -----------------------------------------------------------------------------
FROM nginx:1.27-alpine AS nginx

COPY --from=build /var/www/html/public /var/www/html/public
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN mkdir -p /var/www/html/storage/app/public \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage
