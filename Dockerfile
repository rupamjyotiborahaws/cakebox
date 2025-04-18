# Build stage

FROM composer:2 AS builder

WORKDIR /app

# COPY composer.json composer.lock ./

# Copy full app
COPY . .

RUN composer install --no-dev --optimize-autoloader

# Final stage

FROM php:8.3-apache

# Install PHP extensions needed
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy app from builder stage
COPY --from=builder /app /var/www/html

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache Laravel config for performance
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80

CMD ["apache2-foreground"]
