#!/bin/bash

cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
RUN chmod -R 777 /var/www/storage/

php-fpm