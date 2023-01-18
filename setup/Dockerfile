FROM php:7.4-apache

WORKDIR /var/www/html/
RUN apt-get update
RUN apt-get install -y \
        libzip-dev \
        zip \
        libpng-dev
RUN docker-php-ext-install pdo_mysql zip gettext gd mysqli pdo xml
#RUN a2enmod rewrite
