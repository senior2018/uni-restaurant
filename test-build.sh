#!/bin/bash

echo "=== Testing Laravel + Vue Build Process ==="

# Check if we're in the right directory
if [ ! -f "package.json" ] || [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the project root directory"
    exit 1
fi

echo "✓ Found package.json and composer.json"

# Test npm build
echo "=== Testing NPM Build ==="
if npm run build; then
    echo "✓ NPM build successful"

    # Check if build files were created
    if [ -d "public/build" ]; then
        echo "✓ Build directory created"
        ls -la public/build/

        if [ -f "public/build/manifest.json" ]; then
            echo "✓ Manifest file created"
            cat public/build/manifest.json
        else
            echo "⚠ Manifest file missing"
        fi

        if [ -d "public/build/assets" ]; then
            echo "✓ Assets directory created"
            ls -la public/build/assets/
        else
            echo "⚠ Assets directory missing"
        fi
    else
        echo "❌ Build directory not created"
        exit 1
    fi
else
    echo "❌ NPM build failed"
    exit 1
fi

echo "=== Build Test Completed Successfully ==="
echo "You can now deploy to Render with confidence!"
