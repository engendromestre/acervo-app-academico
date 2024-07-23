# install support for queue
apt-get install -y supervisor
cp /home/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# restart nginx
service nginx restart
service supervisor restart

php /home/site/wwwroot/artisan down --refresh=15 --secret="1630542a-246b-4b66-afa1-dd72a4c43515"

# php /home/site/wwwroot/artisan migrate --force

# Clear caches
php /home/site/wwwroot/artisan cache:clear

# Clear and cache routes
php /home/site/wwwroot/artisan route:cache

# Clear and cache config
php /home/site/wwwroot/artisan config:cache

# Clear and cache views
php /home/site/wwwroot/artisan view:cache

# Turn off maintenance mode
php /home/site/wwwroot/artisan up

# run worker
nohup php /home/site/wwwroot/artisan queue:work &
