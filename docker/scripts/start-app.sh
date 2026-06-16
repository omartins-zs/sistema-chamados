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

exec php-fpm -F
