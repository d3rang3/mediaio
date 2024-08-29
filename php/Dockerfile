FROM php:8.2-fpm

# Install required extensions
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev

# Install and enable PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql zip gd exif intl
RUN pecl install imagick && docker-php-ext-enable imagick

# Install Composer
# Allow super user to run Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# Optain Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy startup script
COPY ./start-composer.sh /start-composer.sh

# Make the script executable
RUN chmod +x /start-composer.sh

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Run the script when the container starts
CMD ["/start-composer.sh"]