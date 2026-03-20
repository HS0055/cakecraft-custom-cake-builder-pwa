#!/bin/bash
set -e

echo "=== CakeCraft startup ==="

# ── Persistent volume setup (/data survives redeploys) ──────────
mkdir -p /data/uploads /data/db
mkdir -p /var/www/html/storage/app

# SQLite database lives on the persistent volume (DB_DATABASE env var points here)
if [ ! -f /data/db/database.sqlite ]; then
    echo "Creating fresh SQLite database on persistent volume..."
    touch /data/db/database.sqlite
fi
echo "DB at: /data/db/database.sqlite"

# Uploaded files (logo, banners, media) live on the persistent volume
rm -rf /var/www/html/storage/app/public
ln -sf /data/uploads /var/www/html/storage/app/public
echo "Uploads linked: /data/uploads"

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Seed core data (roles, admin user, colors, pages)
php artisan db:seed --force 2>/dev/null || true

# Mark app as installed (required by CheckInstallation middleware)
touch storage/installed

# Link storage (public/storage -> storage/app/public -> /data/uploads)
rm -f /var/www/html/public/storage
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
echo "Storage link created"

# Clear and cache configs for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Seed demo data in background if needed
NO_SHAPES=$(php artisan tinker --execute="echo \App\Models\CakeShape::count();" 2>/dev/null | tail -1)
if [ "$NO_SHAPES" = "0" ] || [ -z "$NO_SHAPES" ]; then
    echo "No demo data found — seeding in background..."
    php artisan db:seed --class=DemoImportSeeder --force >> /tmp/demo-seed.log 2>&1 &
fi

echo "=== Starting server on port ${PORT:-8080} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
