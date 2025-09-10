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
    libpq-dev unzip git curl zip nginx supervisor \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel files
COPY . .

# Copy built Vue assets into Laravel public folder
COPY --from=frontend /app/public ./public

# Create necessary directories and set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy nginx configuration
COPY <<EOF /etc/nginx/sites-available/default
server {
    listen 8000;
    server_name _;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Copy supervisor configuration
COPY <<EOF /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
startretries=0

[program:nginx]
command=nginx -g 'daemon off;'
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
startretries=0
EOF

# Expose port
EXPOSE 8000

# Start script
COPY <<EOF /start.sh
#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear any cached views that might have wrong URLs
php artisan view:clear

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

RUN chmod +x /start.sh

CMD ["/start.sh"]
