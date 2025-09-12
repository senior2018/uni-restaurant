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

# Create storage symlink for public access
echo "Creating storage symlink..."
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

# Verify storage symlink and logo file
echo "Verifying storage setup..."
echo "Checking if storage/app/public exists:"
ls -la storage/app/public/ || echo "storage/app/public not found"
echo "Checking if image directory exists:"
ls -la storage/app/public/image/ || echo "Image directory not found"
echo "Creating storage symlink (force):"
rm -f public/storage
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
echo "Verifying symlink creation:"
ls -la public/storage || echo "Storage symlink creation failed"
echo "Checking if logo is accessible via symlink:"
ls -la public/storage/image/logo-final.svg || echo "Logo not accessible via symlink"
echo "Testing direct access to logo:"
ls -la storage/app/public/image/logo-final.svg || echo "Logo not found in storage"

# Clear any existing cache files that might have wrong permissions
rm -rf storage/framework/views/* storage/framework/cache/* storage/framework/sessions/*

# Run Laravel setup (optimized)
php artisan migrate:fresh --force --no-interaction || echo "Migration failed, continuing..."
php artisan cache:clear --no-interaction || echo "Cache clear failed, continuing..."
php artisan config:cache --no-interaction || echo "Config cache failed, continuing..."

# Create storage link using Laravel artisan command
echo "Creating storage link using Laravel artisan..."
# Remove existing link first to avoid conflicts
rm -f public/storage
php artisan storage:link || echo "Storage link creation failed, using manual symlink"

# Final verification of storage setup
echo "Final storage verification..."
echo "Symlink status:"
ls -la public/storage
echo "Logo file check:"
ls -la public/storage/image/logo-final.svg || echo "Logo still not accessible"
echo "All files in public/storage/image/:"
ls -la public/storage/image/ || echo "No image directory found"
echo "All files in storage/app/public/image/:"
ls -la storage/app/public/image/ || echo "No storage image directory found"

# Ensure logo is accessible - copy from storage if symlink fails
echo "Ensuring logo accessibility..."
mkdir -p public/storage/image

# Always try to copy the original logo first
echo "Checking for original logo files..."
echo "Files in storage/app/public/image/:"
ls -la storage/app/public/image/ || echo "Storage image directory not found"

# Force copy the original logo if it exists
if [ -f "public/images/logo-final.svg" ]; then
    echo "✅ Found original logo-final.svg in public/images, copying to storage directory..."
    # Remove any existing fallback first
    rm -f public/storage/image/logo-final.svg
    cp public/images/logo-final.svg public/storage/image/logo-final.svg
    echo "✅ Original logo-final.svg copied successfully"
    echo "Verifying copy:"
    ls -la public/storage/image/logo-final.svg
    echo "Checking file content (first 3 lines):"
    head -3 public/storage/image/logo-final.svg
elif [ -f "storage/app/public/image/logo-final.svg" ]; then
    echo "✅ Found original logo-final.svg in storage, copying to public directory..."
    # Remove any existing fallback first
    rm -f public/storage/image/logo-final.svg
    cp storage/app/public/image/logo-final.svg public/storage/image/logo-final.svg
    echo "✅ Original logo-final.svg copied successfully"
    echo "Verifying copy:"
    ls -la public/storage/image/logo-final.svg
    echo "Checking file content (first 3 lines):"
    head -3 public/storage/image/logo-final.svg
elif [ -f "storage/app/public/image/logo.png" ]; then
    echo "Found logo.png, copying to public directory..."
    cp storage/app/public/image/logo.png public/storage/image/logo.png
    echo "✅ Logo.png copied successfully"
elif [ -f "storage/app/public/image/logo.jpg" ]; then
    echo "Found logo.jpg, copying to public directory..."
    cp storage/app/public/image/logo.jpg public/storage/image/logo.jpg
    echo "✅ Logo (JPEG) copied successfully"
else
    echo "⚠️ No original logo found in storage, creating fallback..."
    echo "This should not happen if the logo-final.svg file is properly committed to the repository"
    echo "Please ensure storage/app/public/image/logo-final.svg exists in your repository"
    
    # Create a simple fallback SVG logo
    cat > public/storage/image/logo-final.svg << 'EOF'
<svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
  <rect width="32" height="32" fill="#10b981" rx="4"/>
  <text x="16" y="20" font-family="Arial, sans-serif" font-size="12" font-weight="bold" text-anchor="middle" fill="white">R</text>
</svg>
EOF
    echo "⚠️ Fallback SVG logo created - this indicates the original logo file is missing"
fi

# Ensure placeholder image exists
if [ ! -f "public/storage/image/placeholder.jpg" ]; then
    echo "Creating placeholder image..."
    mkdir -p public/storage/image
    if [ -f "storage/app/public/image/placeholder.jpg" ]; then
        cp storage/app/public/image/placeholder.jpg public/storage/image/placeholder.jpg
        echo "Placeholder image copied from storage"
    else
        # Create a simple placeholder by copying logo
        cp storage/app/public/image/logo.jpg public/storage/image/placeholder.jpg 2>/dev/null || echo "Could not create placeholder image"
    fi
fi

# Simple logo setup
echo "Setting up logo files..."
# Use the actual restaurant logo (logo-final.svg contains the restaurant image)
if [ -f "storage/app/public/image/logo-final.svg" ]; then
    echo "Using logo-final.svg as main logo"
    cp storage/app/public/image/logo-final.svg public/favicon.png
    cp storage/app/public/image/logo-final.svg public/logo.png
    echo "Favicon and public logo created from logo-final.svg"
elif [ -f "storage/app/public/image/logo.png" ]; then
    echo "Using logo.png as main logo (copying to logo.jpg for proper format)"
    cp storage/app/public/image/logo.png storage/app/public/image/logo.jpg
    cp storage/app/public/image/logo.jpg public/favicon.png
    cp storage/app/public/image/logo.jpg public/logo.png
    echo "logo.jpg, favicon.png, and public/logo.png created from logo.png"
elif [ -f "storage/app/public/image/logo.jpg" ]; then
    echo "Using logo.jpg as main logo"
    cp storage/app/public/image/logo.jpg public/favicon.png
    cp storage/app/public/image/logo.jpg public/logo.png
    echo "Favicon and public logo created from logo.jpg"
elif [ -f "storage/app/public/image/Logo.png" ]; then
    echo "Using Logo.png as main logo (copying to logo.png and logo.jpg)"
    cp storage/app/public/image/Logo.png storage/app/public/image/logo.png
    cp storage/app/public/image/logo.png storage/app/public/image/logo.jpg
    cp storage/app/public/image/logo.jpg public/favicon.png
    cp storage/app/public/image/logo.jpg public/logo.png
    echo "All logo files created from Logo.png"
else
    echo "No logo found, creating simple fallback"
    # Create a simple green square as fallback
    echo -e '\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00 \x00\x00\x00 \x08\x06\x00\x00\x00szz\xf4\x00\x00\x00\x19tEXtSoftware\x00Adobe ImageReadyq\xc9e<\x00\x00\x00\xa4IDATx\xdab\xf8\x0f\x00\x00\x00\xff\xff\x03\x00\x00\x06\x00\x05\xdd\x1d\xa4\x00\x00\x00\x00IEND\xaeB`\x82' > public/favicon.png
fi

# Create simple favicon.ico
echo -e '\x00\x00\x01\x00\x01\x00\x10\x10\x00\x00\x01\x00\x20\x00\x68\x04\x00\x00\x16\x00\x00\x00\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x10\x00\x00\x00\x10\x08\x06\x00\x00\x00\x1f\xf3\xffa\x00\x00\x00\x19tEXtSoftware\x00Adobe ImageReadyq\xc9e<\x00\x00\x03\xf7IDATx\xdab\xf8\x0f\x00\x00\x00\xff\xff\x03\x00\x00\x06\x00\x05\xdd\x1d\xa4\x00\x00\x00\x00IEND\xaeB`\x82' > public/favicon.ico
echo "Simple favicon.ico created"

echo "File permissions:"
ls -la storage/app/public/image/logo-final.svg
echo "Final logo accessibility check:"
ls -la public/storage/image/logo-final.svg || echo "Logo still not accessible after all attempts"
echo "All available logo files:"
ls -la public/storage/image/logo* || echo "No logo files found"

# Seed database with deployment data (optimized)
echo "Seeding database with deployment data..."
php artisan db:seed --class=DeploymentSeeder --force --no-interaction
echo "Seeding completed successfully!"

# Verify the seeded data
echo "Verifying seeded data..."
php artisan tinker --execute="
echo 'Categories: ' . App\Models\MealCategory::count() . PHP_EOL;
echo 'Meals: ' . App\Models\Meal::count() . PHP_EOL;
echo 'Available meals: ' . App\Models\Meal::where('is_available', true)->count() . PHP_EOL;
echo 'Unavailable meals: ' . App\Models\Meal::where('is_available', false)->count() . PHP_EOL;
echo 'Meals with images: ' . App\Models\Meal::whereNotNull('image_url')->count() . PHP_EOL;
" || echo "Verification failed, continuing..."

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

# Create a logo test page
echo "Creating logo test page..."
echo '<!DOCTYPE html>' > public/logo-test.html
echo '<html><head><title>Logo Test</title></head>' >> public/logo-test.html
echo '<body>' >> public/logo-test.html
echo '<h1>Logo Test Page</h1>' >> public/logo-test.html
echo '<p>If you can see the logo below, the storage link is working:</p>' >> public/logo-test.html
echo '<img src="/storage/image/logo-final.svg" alt="Logo" style="width: 200px; border: 1px solid #ccc;">' >> public/logo-test.html
echo '<p>Logo path: /storage/image/logo-final.svg</p>' >> public/logo-test.html
echo '</body></html>' >> public/logo-test.html
echo "Logo test page created at /logo-test.html"

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
