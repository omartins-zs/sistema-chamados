#!/bin/sh
# Primeira inicialização dentro do container (composer, key, migrate, assets).
set -e

cd /var/www/html

rm -f storage/app/.docker-ready

if [ ! -f .env ]; then
    echo "[bootstrap] .env não encontrado — copiando de .env.example..."
    cp .env.example .env
fi

mkdir -p storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

echo "[bootstrap] Limpando caches antigos..."
php artisan optimize:clear --no-interaction 2>/dev/null || rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php 2>/dev/null || true

echo "[bootstrap] Verificando dependências PHP..."
if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if grep -Eq '^APP_KEY=\s*$' .env 2>/dev/null; then
    echo "[bootstrap] Gerando APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

echo "[bootstrap] Aguardando MySQL..."
tentativa=0
until php artisan db:show --no-interaction >/dev/null 2>&1; do
    tentativa=$((tentativa + 1))
    if [ "$tentativa" -ge 60 ]; then
        echo "[bootstrap] ERRO: MySQL não respondeu."
        echo "[bootstrap] Confira DB_HOST=mysql no container e credenciais chamados/chamados."
        echo "[bootstrap] Se migrou de versão antiga do compose: docker compose down -v"
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
    echo "[bootstrap] Instalando dependências Node e build Vite (pode demorar na 1ª vez)..."
    if [ -f package-lock.json ]; then
        npm ci --no-audit --no-fund
    else
        npm install --no-audit --no-fund
    fi
    npm run build
fi

echo "[bootstrap] Concluído."
