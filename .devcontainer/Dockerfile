# .devcontainer/Dockerfile
FROM php:8.1-apache

# install system deps & PHP extensions
RUN apt-get update \
 && apt-get install -y \
      zip unzip \
      libpng-dev \
      libonig-dev \
      libxml2-dev \
 && docker-php-ext-install \
      pdo_mysql \
      mbstring \
      exif \
      pcntl \
      bcmath \
      gd \
 && a2enmod rewrite

# (optional) install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html
