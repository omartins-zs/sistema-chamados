#!/bin/sh
set -e

echo "[start-app] Subindo PHP-FPM..."

if [ -d /var/www/html/storage ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

if [ ! -f storage/app/.docker-ready ]; then
    echo "[start-app] ERRO: bootstrap não concluiu — rode: docker compose up init"
    exit 1
fi

if [ "${CACHE_STORE:-}" = "redis" ] || [ "${SESSION_DRIVER:-}" = "redis" ]; then
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
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
fi

exec php-fpm -F
