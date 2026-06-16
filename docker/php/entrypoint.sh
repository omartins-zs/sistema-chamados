#!/bin/sh
set -e

cd /var/www/html

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Só o container app (php-fpm) prepara o banco
case "$1" in
    php-fpm*)
        if [ -f .env ] && grep -Eq '^APP_KEY=\s*$' .env; then
            php artisan key:generate --force --no-interaction
        fi

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
