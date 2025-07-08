# Build stage

FROM composer:2 AS builder

WORKDIR /app

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
RUN a2enmod rewrite ssl headers

# Copy app from builder stage
COPY --from=builder /app /var/www/html

# Set working directory
WORKDIR /var/www/html

# Copy custom Apache virtual host config
COPY apache/000-default.conf /etc/apache2/sites-available/000-default-ssl.conf
RUN a2ensite 000-default-ssl

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["apache2-foreground"]
