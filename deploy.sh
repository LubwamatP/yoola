#!/bin/bash
# Yoola.ug Deployment Script
# Run this on the server after uploading files

echo "🚀 Starting Yoola.ug deployment..."

# Navigate to project directory
cd /home/zuuldqak/public_html

# Set permissions
echo "📁 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Install/update dependencies (if needed)
# echo "📦 Installing dependencies..."
# composer install --no-dev --optimize-autoloader

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers (if using)
# php artisan queue:restart

echo "✅ Deployment complete!"
echo ""
echo "🔗 Test URLs:"
echo "   - Homepage: https://yoola.ug"
echo "   - Power Calculator: https://yoola.ug/power-calculator"
echo "   - Admin: https://yoola.ug/admin"
