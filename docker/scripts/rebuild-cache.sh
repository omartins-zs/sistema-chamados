#!/bin/sh
# Reconstrói caches do Laravel dentro do container (após mudar .env, rotas ou config)
set -e

echo "[rebuild-cache] Limpando caches..."
php artisan optimize:clear --no-interaction

echo "[rebuild-cache] Recriando config e routes..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction

echo "[rebuild-cache] Concluído."
