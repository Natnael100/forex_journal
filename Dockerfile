FROM serversideup/php:8.3-fpm-nginx

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --chown=www-data:www-data . .

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create storage subdirectories and set permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public storage/database \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8080
