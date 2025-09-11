# Stage 1: Build Vue frontend
FROM node:22-alpine AS frontend
WORKDIR /app

# Copy package files
COPY package.json package-lock.json* ./

# Install dependencies
RUN npm ci --no-audit --no-fund

# Copy source files
COPY resources/ resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./

# Build the frontend assets
RUN npm run build

# Stage 2: Laravel backend
FROM php:8.3-fpm

# Install system dependencies and security updates
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl zip nginx supervisor \
    && apt-get upgrade -y \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/* \
    && rm -rf /var/tmp/*

# Configure PHP-FPM to use stdout/stderr for logging
RUN echo '[global]' > /usr/local/etc/php-fpm.d/docker.conf \
    && echo 'error_log = /dev/stderr' >> /usr/local/etc/php-fpm.d/docker.conf \
    && echo '' >> /usr/local/etc/php-fpm.d/docker.conf \
    && echo '[www]' >> /usr/local/etc/php-fpm.d/docker.conf \
    && echo 'access.log = /dev/stdout' >> /usr/local/etc/php-fpm.d/docker.conf \
    && echo 'catch_workers_output = yes' >> /usr/local/etc/php-fpm.d/docker.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel application
COPY . .

# Verify storage files are copied
RUN echo "Verifying storage files are copied..." \
    && ls -la storage/app/public/ || echo "storage/app/public not found" \
    && ls -la storage/app/public/image/ || echo "storage/app/public/image not found" \
    && ls -la storage/app/public/image/logo.png || echo "logo.png not found in container"

# Copy built assets from frontend stage
COPY --from=frontend /app/public/build ./public/build

# Create non-root user for security
RUN groupadd -r appuser && useradd -r -g appuser appuser

# Set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Nginx configuration
COPY <<EOF /etc/nginx/sites-available/default
server {
    listen 8000;
    server_name _;
    root /var/www/html/public;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdnjs.cloudflare.com; img-src 'self' data: https:; font-src 'self' data: https://fonts.bunny.net https://cdnjs.cloudflare.com;" always;

    # Handle static assets
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files \$uri =404;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED \$document_root\$fastcgi_path_info;
        fastcgi_read_timeout 300;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
    }
}
EOF

# Supervisor configuration
COPY <<EOF /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root

[program:php-fpm]
command=php-fpm --nodaemonize
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true

[program:nginx]
command=nginx -g 'daemon off;'
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
EOF

EXPOSE 8000

# Copy start script
COPY start.sh /start.sh

RUN chmod +x /start.sh
CMD ["/start.sh"]
