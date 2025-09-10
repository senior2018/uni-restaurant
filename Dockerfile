# Stage 1: Build Vue frontend
FROM node:22-alpine AS frontend
WORKDIR /app

# Copy only package files first for caching
COPY package.json package-lock.json* ./

# Install build dependencies
RUN apk add --no-cache python3 make g++ git

# Install npm dependencies
RUN npm ci

# Copy all necessary files for building
COPY resources/ resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./

# Create public directory structure
RUN mkdir -p public/build

# Debug: Show what we have before build
RUN echo "=== PRE-BUILD DEBUG ===" \
    && echo "Resources CSS:" && ls -la resources/css/ \
    && echo "Resources JS:" && ls -la resources/js/ \
    && echo "Vite config:" && cat vite.config.js

# Build the frontend assets with verbose output
RUN echo "=== BUILDING ASSETS ===" && npm run build

# Debug: Show what was built
RUN echo "=== POST-BUILD DEBUG ===" \
    && echo "Public directory:" && ls -la public/ \
    && echo "Build directory:" && ls -la public/build/ \
    && echo "Build assets:" && ls -la public/build/assets/ || echo "No assets directory" \
    && echo "Manifest content:" && cat public/build/manifest.json || echo "No manifest found" \
    && echo "All CSS files:" && find public/build -name "*.css" -ls \
    && echo "All JS files:" && find public/build -name "*.js" -ls

# Stage 2: Laravel backend
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl zip nginx supervisor \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel application files
COPY . .

# Remove any existing build directory and copy fresh assets from frontend stage
RUN rm -rf public/build
COPY --from=frontend /app/public/build ./public/build

# Debug: Verify assets were copied correctly
RUN echo "=== ASSET COPY VERIFICATION ===" \
    && echo "Build directory exists:" && ls -la public/build/ \
    && echo "Assets directory:" && ls -la public/build/assets/ || echo "No assets directory" \
    && echo "Manifest:" && cat public/build/manifest.json || echo "No manifest" \
    && echo "CSS files after copy:" && find public/build -name "*.css" -ls \
    && echo "JS files after copy:" && find public/build -name "*.js" -ls

# Set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Nginx configuration with better asset handling
COPY <<EOF /etc/nginx/sites-available/default
server {
    listen 8000;
    server_name _;
    root /var/www/html/public;
    index index.php;

    # Enable gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Handle static assets with proper caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Access-Control-Allow-Origin "*";
        try_files \$uri =404;
        access_log off;
    }

    # Handle build assets specifically
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files \$uri =404;
        access_log off;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Supervisor configuration
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

EXPOSE 8000

# Enhanced start script with comprehensive debugging
COPY <<EOF /start.sh
#!/bin/bash
set -e

echo "=== DEPLOYMENT ASSET CHECK ==="
echo "Current working directory: \$(pwd)"
echo "Public directory contents:"
ls -la public/

echo "Build directory check:"
if [ -d "public/build" ]; then
    echo "✓ Build directory exists"
    ls -la public/build/

    echo "Assets directory check:"
    if [ -d "public/build/assets" ]; then
        echo "✓ Assets directory exists"
        ls -la public/build/assets/
    else
        echo "✗ Assets directory missing!"
    fi

    echo "Manifest check:"
    if [ -f "public/build/manifest.json" ]; then
        echo "✓ Manifest exists:"
        cat public/build/manifest.json
    else
        echo "✗ Manifest missing!"
    fi
else
    echo "✗ Build directory missing!"
fi

echo "All CSS files in build:"
find public/build -name "*.css" -exec echo "CSS: {}" \; 2>/dev/null || echo "No CSS files found"

echo "All JS files in build:"
find public/build -name "*.js" -exec echo "JS: {}" \; 2>/dev/null || echo "No JS files found"

echo "=== LARAVEL SETUP ==="
echo "Running migrations..."
php artisan migrate --force

echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache

echo "=== FINAL VERIFICATION ==="
echo "Nginx will serve from: /var/www/html/public"
echo "Asset directory permissions:"
ls -la public/build/ || echo "Build directory not accessible"

echo "Testing asset file accessibility:"
if [ -d "public/build/assets" ]; then
    for file in public/build/assets/*.css public/build/assets/*.js; do
        [ -f "\$file" ] && echo "✓ \$(basename \$file) is readable" || echo "✗ \$file not found"
    done
fi

echo "=== STARTING SERVICES ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

RUN chmod +x /start.sh

CMD ["/start.sh"]
