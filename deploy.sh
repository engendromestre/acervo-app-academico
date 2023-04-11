#!/bin/bash
set -e

echo "Deployment started ...."

# Update composer dependencies
composer update --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Enter maintenance mode or return true
# if already is in maintenance mode
(php artisan down) || true

# Pull the latest version of the app
git pull origin production

# Clear the old cache
php artisan clear-compiled

# Recreate cache
php artisan optimize

# Compile npm assets with build
npm run build

# Run database migrations
php artisan migrate --force

# Exit maintenance mode
php artisan up

echo "Deployment finished!"