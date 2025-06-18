FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo pdo_mysql

COPY ./public /var/www/html

RUN chown -R www-data:www-data /var/www/html
