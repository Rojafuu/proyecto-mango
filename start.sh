#!/bin/sh
set -e

echo "===> Migrating..."
php artisan migrate --force

if [ "$RUN_SEED" = "true" ]; then
  echo "===> Seeding..."
  php artisan db:seed --force
fi

echo "===> Starting server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
