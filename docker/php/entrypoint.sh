#!/bin/sh
set -e

cd /var/www/html

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# APP_KEY persiste no volume storage (não depende de .env no host)
if [ -s storage/app/.app_key ]; then
    export APP_KEY="$(tr -d '\r\n' < storage/app/.app_key)"
elif [ -n "${APP_KEY}" ] && [ "${APP_KEY}" != "base64:" ]; then
  echo "$APP_KEY" > storage/app/.app_key
else
    php artisan key:generate --show | tr -d '\r\n' > storage/app/.app_key
    export APP_KEY="$(tr -d '\r\n' < storage/app/.app_key)"
fi

case "$1" in
    php-fpm*)
        echo "[app] Aguardando MySQL..."
        i=0
        until php artisan db:show --no-interaction >/dev/null 2>&1; do
            i=$((i + 1))
            [ "$i" -ge 30 ] && { echo "[app] ERRO: MySQL indisponível."; exit 1; }
            sleep 2
        done

        php artisan migrate --force --no-interaction

        if [ ! -f storage/app/.seeded ]; then
            php artisan db:seed --force --no-interaction
            touch storage/app/.seeded
        fi

        echo "[app] Pronto."
        ;;
esac

exec "$@"
