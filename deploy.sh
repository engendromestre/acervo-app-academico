#!/bin/bash
set -e

echo "Deployment started ...."

# Enter maintenance mode or return true
# if already is in maintenance mode
(php artisan down) || true

# Pull the latest version of the app
git pull --rebase --autostash

# Install composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Generate Key
php artisan key:generate

# Clear the old cache
php artisan clear-compiled

# Recreate cache
php artisan optimize

# install npm dependencies
npm install

# Compile npm assets with build
npm run build

# Run database migrations
php artisan migrate --force

# Create Symbolic link
php artisan storage:link

# Exit maintenance mode
php artisan up

echo "Deployment finished!"