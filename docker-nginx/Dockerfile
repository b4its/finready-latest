FROM php:8.4-fpm

# 1. Tambahkan libicu-dev untuk ekstensi intl
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
    libicu-dev \
    zip unzip git curl mariadb-client

# 2. Tambahkan intl di sini
RUN docker-php-ext-install pdo_mysql gd zip intl

# 3. Solusi untuk error "dubious ownership" git yang tadi muncul
RUN git config --global --add safe.directory /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html