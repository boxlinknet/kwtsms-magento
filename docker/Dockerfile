FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libxslt1-dev \
    libicu-dev \
    libsodium-dev \
    zip \
    unzip \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        ctype \
        dom \
        gd \
        intl \
        mbstring \
        pdo_mysql \
        soap \
        xsl \
        zip \
        sockets \
        sodium \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP
RUN echo "memory_limit=2G" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "max_execution_time=1800" >> /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "realpath_cache_size=10M" >> /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "realpath_cache_ttl=7200" >> /usr/local/etc/php/conf.d/memory-limit.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
