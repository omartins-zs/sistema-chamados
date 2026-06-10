#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

export APP_ENV=testing
export E2E_SEED_MINIMAL=true

if [[ ! -f .env.testing ]]; then
  echo "Arquivo .env.testing não encontrado."
  exit 1
fi

rm -f database/e2e.sqlite
mkdir -p database
touch database/e2e.sqlite

php artisan key:generate --env=testing --force --no-interaction
php artisan migrate --force --env=testing --seed --no-interaction
php artisan config:clear --env=testing --no-interaction

echo "Servidor E2E em http://127.0.0.1:8000"
php artisan serve --env=testing --host=127.0.0.1 --port=8000 --no-reload
