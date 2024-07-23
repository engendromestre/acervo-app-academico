#!/bin/bash

# Copia os arquivos de configuração
cp /home/default /etc/nginx/sites-enabled/default
cp /home/php.ini /usr/local/etc/php/conf.d/php.ini

# install support for queue
apt-get install -y supervisor
cp /home/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Reinicia o Nginx e o Supervisor
service nginx restart
service supervisor restart

php /home/site/wwwroot/artisan down --refresh=15 --secret="1630542a-246b-4b66-afa1-dd72a4c43515"

# Aplica as migrações e popula o banco de dados
# php /home/site/wwwroot/artisan migrate:fresh --seed

# Limpa e cacheia configurações e views
php /home/site/wwwroot/artisan cache:clear
php /home/site/wwwroot/artisan route:cache
php /home/site/wwwroot/artisan config:cache
php /home/site/wwwroot/artisan view:cache

# Verifica se o link simbólico já existe e cria-o se necessário
SYMLINK_PATH="/home/site/wwwroot/public/storage"
if [ -L "$SYMLINK_PATH" ]; then
    echo "O link simbólico já existe. Nenhuma ação necessária."
else
    echo "O link simbólico não existe. Executando o comando artisan para criá-lo."
    php /home/site/wwwroot/artisan storage:link
fi

# Turn off maintenance mode
php /home/site/wwwroot/artisan up

# run worker
nohup php /home/site/wwwroot/artisan queue:work &
