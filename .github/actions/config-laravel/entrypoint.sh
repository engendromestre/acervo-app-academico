#!/bin/sh
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan cache:clear
php artisan config:clear
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
chmod -R 777 storage bootstrap/cache