#!/bin/sh
set -e

echo "[start-app] Iniciando container app..."

if [ -d /var/www/html/storage ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

if [ "${DOCKER_BOOTSTRAP:-true}" = "true" ]; then
    sh /usr/local/bin/bootstrap.sh
fi

if [ "${CACHE_STORE:-}" = "redis" ] || [ "${SESSION_DRIVER:-}" = "redis" ]; then
    echo "[start-app] Aguardando Redis..."
    tentativa=0
    until php -r "
        \$r = @fsockopen(getenv('REDIS_HOST') ?: 'redis', (int)(getenv('REDIS_PORT') ?: 6379), \$e, \$s, 2);
        if (\$r) { fclose(\$r); exit(0); }
        exit(1);
    " 2>/dev/null; do
        tentativa=$((tentativa + 1))
        if [ "$tentativa" -ge 15 ]; then
            echo "[start-app] AVISO: Redis indisponível — continuando."
            break
        fi
        sleep 1
    done
fi

if [ "${DOCKER_WARM_CACHE:-true}" = "true" ]; then
    php artisan optimize:clear --no-interaction 2>/dev/null || true

    echo "[start-app] Gerando config:cache..."
    php artisan config:cache --no-interaction

    echo "[start-app] Gerando route:cache..."
    php artisan route:cache --no-interaction
fi

touch storage/app/.docker-ready
echo "[start-app] Aplicação pronta (.docker-ready)."

echo "[start-app] Subindo PHP-FPM..."
exec php-fpm -F
