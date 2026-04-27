FROM php:8.4-fpm

# 1. Tambahkan libicu-dev untuk ekstensi intl
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
    libicu-dev \
    zip unzip git curl mariadb-client

RUN curl -fsSL https://deb.nodesource.com/setup_current.x | bash - \
    && apt-get install -y nodejs

# 2. Tambahkan intl di sini
RUN docker-php-ext-install pdo_mysql gd zip intl

# 3. Solusi untuk error "dubious ownership" git yang tadi muncul
RUN git config --global --add safe.directory /var/www/html

# 4. TAMBAHKAN INI: Set limit upload PHP menjadi 50MB (Permanen)
RUN echo "upload_max_filesize = 50M" > /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html