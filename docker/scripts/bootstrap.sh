#!/bin/sh
# Primeira inicialização dentro do container (composer, key, migrate, assets).
set -e

cd /var/www/html

echo "[bootstrap] Verificando dependências PHP..."
if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -f .env ] && grep -Eq '^APP_KEY=\s*$' .env; then
    echo "[bootstrap] Gerando APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

echo "[bootstrap] Aguardando MySQL..."
tentativa=0
until php artisan db:show --no-interaction >/dev/null 2>&1; do
    tentativa=$((tentativa + 1))
    if [ "$tentativa" -ge 45 ]; then
        echo "[bootstrap] ERRO: MySQL não respondeu."
        exit 1
    fi
    sleep 2
done

echo "[bootstrap] Executando migrations..."
php artisan migrate --force --no-interaction

if [ ! -f storage/app/.docker-initialized ]; then
    echo "[bootstrap] Populando banco (seeders)..."
    php artisan db:seed --force --no-interaction
    touch storage/app/.docker-initialized
fi

if [ ! -f public/build/manifest.json ]; then
    echo "[bootstrap] Instalando dependências Node e build Vite..."
    if [ -f package-lock.json ]; then
        npm ci --no-audit --no-fund
    else
        npm install --no-audit --no-fund
    fi
    npm run build
fi

echo "[bootstrap] Concluído."
