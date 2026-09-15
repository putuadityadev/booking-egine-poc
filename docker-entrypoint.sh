#!/bin/bash
set -e

echo "🚀 Starting Booking Engine POC..."

# Ensure database directory and SQLite file exist
mkdir -p /app/database
touch /app/database/database.sqlite

# Ensure storage directories and permissions exist
mkdir -p /app/storage/framework/sessions /app/storage/framework/views /app/storage/framework/cache /app/storage/logs /app/bootstrap/cache
chmod -R 777 /app/storage /app/bootstrap/cache /app/database

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run database migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Seed database if properties table is empty
PROPERTY_COUNT=$(php artisan tinker --execute="echo \App\Models\Property::count();" 2>/dev/null || echo "0")
if [ "$PROPERTY_COUNT" = "0" ] || [ -z "$PROPERTY_COUNT" ]; then
    echo "🌱 Seeding initial properties and rooms..."
    php artisan db:seed --force
fi

# Clear and optimize cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

PORT="${PORT:-10000}"
echo "🌐 Server running on 0.0.0.0:${PORT}"

exec php -d variables_order=EGPCS artisan serve --host=0.0.0.0 --port="${PORT}"
