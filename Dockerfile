FROM php:8.2.0-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Set working directory
WORKDIR /var/www
