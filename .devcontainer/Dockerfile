# .devcontainer/Dockerfile
FROM php:8.1-apache

# Install system dependencies & PHP extensions
RUN apt-get update \
 && apt-get install -y \
      zip unzip \
      libpng-dev \
      libonig-dev \
      libxml2-dev \
      automake \
      autoconf \
      wget \
      git \
 && docker-php-ext-install \
      pdo_mysql \
      mbstring \
      exif \
      pcntl \
      bcmath \
      gd \
 && a2enmod rewrite

# Install Xdebug 3.4.3
RUN wget https://xdebug.org/files/xdebug-3.4.3.tgz \
 && tar -xvzf xdebug-3.4.3.tgz \
 && cd xdebug-3.4.3 \
 && phpize \
 && ./configure \
 && make \
 && cp modules/xdebug.so /usr/local/lib/php/extensions/no-debug-non-zts-20210902/ \
 && echo "[xdebug]\nzend_extension=xdebug\nxdebug.mode=debug\nxdebug.start_with_request=yes\nxdebug.client_host=host.docker.internal\nxdebug.client_port=9003\nxdebug.log=/tmp/xdebug.log" > /usr/local/etc/php/conf.d/99-xdebug.ini \
 && rm -rf xdebug-3.4.3 xdebug-3.4.3.tgz

# (optional) Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Set up directories and permissions
RUN mkdir -p /var/www/html/public/data/uploads/images \
    && mkdir -p /var/www/html/public/data/uploads/files \
    && mkdir -p /var/www/html/public/data/icon \
    && chown -R www-data:www-data /var/www/html/public/data \
    && chmod -R 770 /var/www/html/public/data

WORKDIR /var/www/html