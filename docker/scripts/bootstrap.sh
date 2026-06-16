#!/bin/sh
# Executado uma vez pelo serviço init — composer, migrate, seed e build Vite.
set -e

cd /var/www/html

rm -f storage/app/.docker-ready

if [ ! -f .env ]; then
    echo "[bootstrap] .env não encontrado — copiando de .env.example..."
    cp .env.example .env
fi

mkdir -p storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

echo "[bootstrap] Verificando dependências PHP..."
if [ ! -f vendor/autoload.php ]; then
    echo "[bootstrap] Executando composer install (1ª vez — pode demorar)..."
    COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist --optimize-autoloader \
        || { echo "[bootstrap] ERRO: composer install falhou."; exit 1; }
    echo "[bootstrap] Composer concluído."
else
    echo "[bootstrap] vendor/ já existe — pulando composer install."
fi

if grep -Eq '^APP_KEY=\s*$' .env 2>/dev/null; then
    echo "[bootstrap] Gerando APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

echo "[bootstrap] Limpando caches..."
php artisan optimize:clear --no-interaction 2>/dev/null || rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php 2>/dev/null || true

echo "[bootstrap] Aguardando MySQL..."
tentativa=0
until php artisan db:show --no-interaction >/dev/null 2>&1; do
    tentativa=$((tentativa + 1))
    if [ "$tentativa" -ge 60 ]; then
        echo "[bootstrap] ERRO: MySQL não respondeu."
        echo "[bootstrap] Rode: docker compose down -v && docker compose up -d --build"
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
    echo "[bootstrap] Instalando Node e build Vite (1ª vez — pode demorar)..."
    if [ -f package-lock.json ]; then
        npm ci --no-audit --no-fund \
            || { echo "[bootstrap] ERRO: npm ci falhou."; exit 1; }
    else
        npm install --no-audit --no-fund \
            || { echo "[bootstrap] ERRO: npm install falhou."; exit 1; }
    fi
    npm run build || { echo "[bootstrap] ERRO: npm run build falhou."; exit 1; }
    echo "[bootstrap] Build Vite concluído."
else
    echo "[bootstrap] public/build/ já existe — pulando npm."
fi

touch storage/app/.docker-ready

echo "[bootstrap] Gerando caches..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction

echo "[bootstrap] Concluído — aplicação pronta."
