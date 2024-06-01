FROM php:8.2.0-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www
