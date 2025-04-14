#!/bin/sh

set -e

echo "🚀 Running Laravel setup..."

# Laravel関連のセットアップ（deploy.sh のPHP部分を抜粋）
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan config:cache
php artisan storage:link || true
php artisan migrate --force || true

echo "✅ Laravel setup complete"

# PHP-FPM を起動
exec php-fpm
