#!/bin/bash

# Run laravel migrations
cd /var/www/html

php artisan route:cache
php artisan config:clear

chmod 777 -R storage
chmod o+w storage/logs/
chmod o+w bootstrap/cache
cd /
php-fpm -D && /usr/sbin/httpd -DFOREGROUND
