#!/bin/bash
set -e

echo "=== Laravel Deployment Script ==="
echo "Usage: ./deploy.sh [fresh|migrate|update]"
echo ""

DEPLOYMENT_TYPE=${1:-"update"}

case $DEPLOYMENT_TYPE in
    "fresh")
        echo "🔄 FRESH DEPLOYMENT - This will DROP ALL DATA!"
        echo "Running fresh migrations and seeding..."
        php artisan migrate:fresh --force --no-interaction
        php artisan db:seed --class=DeploymentSeeder --force --no-interaction
        echo "✅ Fresh deployment completed"
        ;;
    "migrate")
        echo "📊 MIGRATION DEPLOYMENT - Running new migrations only"
        php artisan migrate --force --no-interaction
        echo "✅ Migrations completed"
        ;;
    "update"|*)
        echo "🔄 UPDATE DEPLOYMENT - Standard deployment"
        php artisan migrate --force --no-interaction
        php artisan cache:clear --no-interaction
        php artisan config:cache --no-interaction
        php artisan route:cache --no-interaction
        php artisan view:cache --no-interaction
        echo "✅ Update deployment completed"
        ;;
esac

echo ""
echo "=== Deployment Summary ==="
echo "Type: $DEPLOYMENT_TYPE"
echo "Time: $(date)"
echo "Status: ✅ Completed"
