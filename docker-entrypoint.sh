#!/bin/bash
set -e

# Pastikan direktori storage & cache tersedia
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Atur ownership & permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependencies jika vendor belum ada
if [ ! -d "/var/www/html/vendor" ]; then
    echo "[Entrypoint] Folder vendor tidak ditemukan, menjalankan composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

exec "$@"
