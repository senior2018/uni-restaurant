# Stage 1: Build Vue frontend
FROM node:22-alpine AS frontend
WORKDIR /app

# Copy only package files first for caching
COPY package.json package-lock.json* ./

# Install small set of build tools needed by some npm modules (only in build stage)
RUN apk add --no-cache python3 make g++ git

# Use npm ci for reproducible installs
RUN npm ci

# Copy the rest of the frontend resources
COPY resources/js resources/js
COPY resources/css resources/css
COPY vite.config.js ./
COPY tailwind.config.js ./
RUN npm run build

# Stage 2: Laravel backend
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl zip \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel files
COPY . .

# Copy built Vue assets into Laravel public folder
COPY --from=frontend /app/public ./public

# Make storage/cache writable
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install Laravel dependencies and cache config/routes/views
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port
EXPOSE 8000

# Run migrations and start Laravel server
CMD php artisan migrate --force && \
    php -d display_errors=On artisan serve --host=0.0.0.0 --port=8000
