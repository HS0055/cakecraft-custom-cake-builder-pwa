#!/bin/bash
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Seed only if DB is fresh (no users exist)
php artisan db:seed --force 2>/dev/null || true

# Link storage
php artisan storage:link 2>/dev/null || true

# Clear and cache configs for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
