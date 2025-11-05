#!/usr/bin/env bash
set -euo pipefail

echo "Building and starting Docker stack..."
docker compose up -d --build

echo "Waiting for DB to accept connections..."
until docker compose exec -T db mysql -uttms_user -pttms_pass -e 'SELECT 1;' >/dev/null 2>&1; do
  sleep 1
  echo -n '.'
done
echo "\nDB ready"

echo "Running migrations and seeders"
docker compose exec -T workspace php artisan migrate --seed --force

echo "Open http://127.0.0.1:8000"
