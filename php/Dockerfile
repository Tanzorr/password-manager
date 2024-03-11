# Use the official PHP 8.0.0rc1-fpm image
FROM php:8.0.0rc1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install required dependencies for Xdebug
RUN apt-get install -y \
    libonig-dev \
    libzip-dev \
    zlib1g-dev

# Download and extract Xdebug manually
RUN mkdir -p /usr/src/php/ext/xdebug \
    && curl -fsSL 'https://xdebug.org/files/xdebug-3.0.4.tgz' -o xdebug.tgz \
    && tar -xvzf xdebug.tgz -C /usr/src/php/ext/xdebug --strip-components=1 \
    && rm xdebug.tgz

# Build and enable Xdebug
RUN docker-php-ext-configure xdebug \
    && docker-php-ext-install xdebug

# Set up Xdebug configuration
COPY ./php/conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Set working directory
WORKDIR /var/www

# Expose port 8000 for the PHP built-in web server
EXPOSE 8000

# Copy project files to the container
COPY . /var/www

# Display PHP info when the container starts
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www"]
