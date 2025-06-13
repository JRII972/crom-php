# .devcontainer/Dockerfile
FROM php:8.1-apache

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - 
    
# Copy development php.ini and ensure extensions are properly loaded
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" 

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
      nodejs \
      libjpeg62-turbo-dev \
      libfreetype6-dev \
      libwebp-dev \
      libxpm-dev \
      libavif-dev \
      libheif-dev \
 && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype --with-avif --with-xpm \
 && docker-php-ext-install -j$(nproc) \
      pdo_mysql \
      mbstring \
      exif \
      pcntl \
      bcmath \
      gd \
 && a2enmod rewrite
 
RUN pecl install apcu && docker-php-ext-enable apcu

# (optional) Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN pecl install xdebug \
 && mkdir -p /var/www/html/profiling && chmod 777 /var/www/html/profiling \
 && docker-php-ext-enable xdebug \
 && tee /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini > /dev/null <<'EOF'
zend_extension=xdebug.so

; Mode de débogage et profilage
xdebug.mode=debug,profile,develop
xdebug.start_with_request=yes
xdebug.client_port=9003
xdebug.client_host=172.17.0.1

; Pour les environnements Docker
xdebug.discover_client_host=false
xdebug.log=/var/www/html/xdebug.log
xdebug.log_level=7

; Configuration du profileur
xdebug.output_dir=/var/www/html/profiling
xdebug.profiler_output_name=cachegrind.out.%p
EOF


# Set up directories and permissions
RUN mkdir -p /var/www/html/public/data/uploads/images \
    && mkdir -p /var/www/html/public/data/uploads/files \
    && mkdir -p /var/www/html/public/data/icon \
    && chown -R www-data:www-data /var/www/html/public/data \
    && chmod -R 755 /var/www/html/public/data \
    && mkdir -p /var/www/html/App/cache \
    && chmod -R 755 /var/www/html/App/cache 

WORKDIR /var/www/html