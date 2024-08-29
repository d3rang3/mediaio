#!/bin/bash

# Change to the directory with the composer.json file
cd /var/www/io

#composer clear-cache

# Install composer dependencies
composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction -vvv

# Composer dump autoload
composer dump-autoload --classmap-authoritative

# Run the main process (e.g., Apache or PHP-FPM)
exec php-fpm