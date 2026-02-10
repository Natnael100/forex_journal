FROM serversideup/php:8.3-fpm-nginx

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage subdirectories
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public storage/database

# Expose port
EXPOSE 8080
