#!/bin/bash
set -e

# Default PORT to 80 if not specified by Render
PORT="${PORT:-80}"

echo "Configuring Apache for Render on port ${PORT}..."

# Update Apache listening ports
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure storage directories exist and are writable
mkdir -p /var/www/html/writable/cache /var/www/html/writable/logs /var/www/html/writable/session /var/www/html/writable/uploads /var/www/html/writable/debugbar /var/www/html/writable/backups
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

echo "Starting Apache web server..."
exec apache2-foreground
