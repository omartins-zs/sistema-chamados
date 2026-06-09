FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurações de performance
COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-local.ini
COPY docker/php/fpm-performance.conf /usr/local/etc/php-fpm.d/zz-performance.conf

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/scripts/start-app.sh /usr/local/bin/start-app.sh
COPY docker/scripts/rebuild-cache.sh /usr/local/bin/rebuild-cache.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/start-app.sh /usr/local/bin/rebuild-cache.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/local/bin/start-app.sh"]
