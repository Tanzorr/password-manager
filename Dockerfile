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

# Download and install Xdebug manually
RUN mkdir -p /usr/src/php/ext/xdebug \
    && curl -fsSL 'https://xdebug.org/files/xdebug-3.0.4.tgz' -o xdebug.tgz \
    && tar -xvzf xdebug.tgz -C /usr/src/php/ext/xdebug --strip-components=1 \
    && rm xdebug.tgz \
    && docker-php-ext-install xdebug \
    && docker-php-ext-enable xdebug

# Set working directory
WORKDIR /var/www

# Copy project files to the container
COPY . /var/www

# Expose port 8000 for the PHP built-in web server
EXPOSE 8000

# Display PHP info when the container starts
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www"]

