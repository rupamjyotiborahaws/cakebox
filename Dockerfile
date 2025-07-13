# Build stage
FROM node:20 AS node_builder

WORKDIR /app

# Copy only package files first for caching
COPY package*.json ./
RUN npm ci

# Copy entire Laravel app
COPY . .

# Build frontend assets using Vite
RUN npm run build

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

# Set working directory
WORKDIR /var/www/html

# Copy app from builder stage
COPY --from=builder /app /var/www/html

# Copy compiled assets (public/build) from node stage
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Copy custom Apache virtual host config
COPY apache/000-default.conf /etc/apache2/sites-available/000-default-ssl.conf
RUN a2ensite 000-default-ssl

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["apache2-foreground"]
