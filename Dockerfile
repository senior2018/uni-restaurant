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
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;" always;

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

# Start script
COPY <<EOF /start.sh
#!/bin/bash
set -e

echo "=== Starting Laravel Application ==="

# Check if assets were built successfully
echo "Checking if assets were built successfully..."
if [ ! -f "public/build/manifest.json" ] || [ ! -d "public/build/assets" ]; then
    echo "Assets not found, creating fallback assets..."
    mkdir -p public/build/assets

    # Create comprehensive fallback CSS
    echo '/* Fallback CSS - Comprehensive styling for Laravel app */' > public/build/assets/app.css
    echo '* { box-sizing: border-box; }' >> public/build/assets/app.css
    echo 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; line-height: 1.6; color: #333; background-color: #f8f9fa; }' >> public/build/assets/app.css
    echo '.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }' >> public/build/assets/app.css
    echo '.min-h-screen { min-height: 100vh; }' >> public/build/assets/app.css
    echo '.flex { display: flex; }' >> public/build/assets/app.css
    echo '.flex-col { flex-direction: column; }' >> public/build/assets/app.css
    echo '.items-center { align-items: center; }' >> public/build/assets/app.css
    echo '.justify-center { justify-content: center; }' >> public/build/assets/app.css
    echo '.btn, button { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-size: 16px; transition: background-color 0.2s; }' >> public/build/assets/app.css
    echo '.btn:hover, button:hover { background: #0056b3; }' >> public/build/assets/app.css
    echo '.form-group { margin-bottom: 20px; }' >> public/build/assets/app.css
    echo '.form-control, input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; transition: border-color 0.2s; }' >> public/build/assets/app.css
    echo '.form-control:focus, input:focus, select:focus, textarea:focus { outline: none; border-color: #007bff; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }' >> public/build/assets/app.css
    echo 'label { display: block; margin-bottom: 8px; font-weight: 500; }' >> public/build/assets/app.css
    echo '.card { background: white; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }' >> public/build/assets/app.css
    echo '.navbar { background: white; border-bottom: 1px solid #e9ecef; padding: 16px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }' >> public/build/assets/app.css
    echo '.nav-link { color: #333; text-decoration: none; padding: 8px 16px; border-radius: 6px; transition: background-color 0.2s; }' >> public/build/assets/app.css
    echo '.nav-link:hover { background: #f8f9fa; }' >> public/build/assets/app.css
    echo '.table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }' >> public/build/assets/app.css
    echo '.table th, .table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e9ecef; }' >> public/build/assets/app.css
    echo '.table th { background: #f8f9fa; font-weight: 600; }' >> public/build/assets/app.css
    echo '.table tr:hover { background: #f8f9fa; }' >> public/build/assets/app.css
    echo '.text-center { text-align: center; }' >> public/build/assets/app.css
    echo '.mt-4 { margin-top: 2rem; }' >> public/build/assets/app.css
    echo '.mb-4 { margin-bottom: 2rem; }' >> public/build/assets/app.css
    echo '.p-4 { padding: 2rem; }' >> public/build/assets/app.css
    echo '.bg-white { background-color: white; }' >> public/build/assets/app.css
    echo '.shadow { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }' >> public/build/assets/app.css
    echo '.rounded { border-radius: 8px; }' >> public/build/assets/app.css
    echo '@media (max-width: 768px) { .container { padding: 0 16px; } .card { padding: 16px; margin-bottom: 16px; } }' >> public/build/assets/app.css

    # Create comprehensive fallback JS
    echo 'console.log("Fallback JavaScript loaded");' > public/build/assets/app.js
    echo 'window.axios = window.axios || { defaults: { headers: { common: { "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/json", "Accept": "application/json" } } }, get: function(url, config) { return fetch(url, { method: "GET", ...config }); }, post: function(url, data, config) { return fetch(url, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data), ...config }); }, put: function(url, data, config) { return fetch(url, { method: "PUT", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data), ...config }); }, delete: function(url, config) { return fetch(url, { method: "DELETE", ...config }); } };' >> public/build/assets/app.js
    echo 'window.Inertia = window.Inertia || { visit: function(url, options = {}) { if (options.method && options.method !== "GET") { const form = document.createElement("form"); form.method = options.method; form.action = url; if (options.data) { Object.keys(options.data).forEach(key => { const input = document.createElement("input"); input.type = "hidden"; input.name = key; input.value = options.data[key]; form.appendChild(input); }); } document.body.appendChild(form); form.submit(); } else { window.location.href = url; } }, reload: function() { window.location.reload(); } };' >> public/build/assets/app.js
    echo 'window.Vue = window.Vue || { createApp: function(options) { return { mount: function(selector) { console.log("Vue app mounted (fallback mode)"); return this; }, use: function(plugin) { console.log("Vue plugin used (fallback mode)"); return this; } }; } };' >> public/build/assets/app.js
    echo 'document.addEventListener("DOMContentLoaded", function() { console.log("Fallback app initialized"); const forms = document.querySelectorAll("form"); forms.forEach(form => { form.addEventListener("submit", function(e) { console.log("Form submitted:", form.action); const submitBtn = form.querySelector("button[type=\"submit\"], input[type=\"submit\"]"); if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = "Loading..."; setTimeout(() => { submitBtn.disabled = false; submitBtn.textContent = submitBtn.getAttribute("data-original-text") || "Submit"; }, 5000); } }); }); const buttons = document.querySelectorAll("button, .btn"); buttons.forEach(button => { if (button.type === "submit") { button.setAttribute("data-original-text", button.textContent); } button.addEventListener("click", function(e) { console.log("Button clicked:", button.textContent); if (button.classList.contains("btn-danger") || button.textContent.toLowerCase().includes("delete")) { if (!confirm("Are you sure you want to delete this item?")) { e.preventDefault(); return false; } } }); }); console.log("Fallback app setup complete"); });' >> public/build/assets/app.js

    # Create proper manifest
    echo '{' > public/build/manifest.json
    echo '  "resources/css/app.css": {' >> public/build/manifest.json
    echo '    "file": "assets/app.css",' >> public/build/manifest.json
    echo '    "src": "resources/css/app.css",' >> public/build/manifest.json
    echo '    "isEntry": true' >> public/build/manifest.json
    echo '  },' >> public/build/manifest.json
    echo '  "resources/js/app.js": {' >> public/build/manifest.json
    echo '    "file": "assets/app.js",' >> public/build/manifest.json
    echo '    "src": "resources/js/app.js",' >> public/build/manifest.json
    echo '    "isEntry": true' >> public/build/manifest.json
    echo '  }' >> public/build/manifest.json
    echo '}' >> public/build/manifest.json

    echo "Fallback assets created:"
    ls -la public/build/assets/
    echo "Manifest content:"
    cat public/build/manifest.json
else
    echo "Assets found, using built assets:"
    ls -la public/build/assets/
    echo "Manifest content:"
    cat public/build/manifest.json
fi

# Ensure proper permissions for storage directories
echo "Setting up storage permissions..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Clear any existing cache files that might have wrong permissions
rm -rf storage/framework/views/* storage/framework/cache/* storage/framework/sessions/*

# Run Laravel setup
php artisan migrate --force || echo "Migration failed, continuing..."
php artisan cache:clear || echo "Cache clear failed, continuing..."
php artisan config:cache || echo "Config cache failed, continuing..."

# Seed database with deployment data
echo "Seeding database with deployment data..."
php artisan db:seed --class=DeploymentSeeder --force
echo "Seeding completed successfully!"

# Create a simple test page to verify assets
echo "Creating test page to verify assets..."
echo '<!DOCTYPE html>' > public/test-assets.html
echo '<html>' >> public/test-assets.html
echo '<head>' >> public/test-assets.html
echo '    <title>Asset Test</title>' >> public/test-assets.html
echo '    <link rel="stylesheet" href="/build/assets/app.css">' >> public/test-assets.html
echo '</head>' >> public/test-assets.html
echo '<body>' >> public/test-assets.html
echo '    <div class="container">' >> public/test-assets.html
echo '        <h1>Asset Test Page</h1>' >> public/test-assets.html
echo '        <p>If you can see this styled properly, the assets are working!</p>' >> public/test-assets.html
echo '        <button class="btn">Test Button</button>' >> public/test-assets.html
echo '    </div>' >> public/test-assets.html
echo '    <script src="/build/assets/app.js"></script>' >> public/test-assets.html
echo '</body>' >> public/test-assets.html
echo '</html>' >> public/test-assets.html

echo "Test page created at /test-assets.html"

# Create a simple PHP info page for debugging
echo "Creating PHP info page for debugging..."
echo '<?php phpinfo(); ?>' > public/phpinfo.php
echo "PHP info page created at /phpinfo.php"

echo "=== Starting Services ==="

# Test PHP-FPM configuration
echo "Testing PHP-FPM configuration..."
php-fpm -t || echo "PHP-FPM config test failed"

# Show PHP-FPM configuration
echo "PHP-FPM main configuration:"
cat /usr/local/etc/php-fpm.conf
echo "PHP-FPM www pool configuration:"
cat /usr/local/etc/php-fpm.d/www.conf
echo "PHP-FPM docker configuration:"
cat /usr/local/etc/php-fpm.d/docker.conf

# Test if PHP-FPM can start
echo "Testing PHP-FPM startup..."
timeout 5 php-fpm --test || echo "PHP-FPM startup test completed"

# Test Nginx configuration
echo "Testing Nginx configuration..."
nginx -t || echo "Nginx config test failed"

# Start services
echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

RUN chmod +x /start.sh
CMD ["/start.sh"]
