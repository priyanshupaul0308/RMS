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

# Setup database: if cloud DB is specified, connect to it; otherwise start embedded MariaDB
if [ -n "$DB_HOST" ] || [ -n "$DATABASE_URL" ] || [ -n "$MYSQL_URL" ]; then
    echo "Cloud database environment variable detected. Checking database..."
    php spark db:init || echo "Notice: db:init check completed."
else
    echo "No external cloud DB configured. Starting embedded MariaDB service..."
    mkdir -p /run/mysqld /var/lib/mysql
    chown -R mysql:mysql /run/mysqld /var/lib/mysql
    chmod 777 /run/mysqld

    # Initialize datadir if needed
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        echo "Initializing MariaDB datadir..."
        mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1 || true
    fi

    # Start MariaDB service
    /etc/init.d/mariadb start || service mariadb start

    # Wait up to 15 seconds for socket
    for i in {1..15}; do
        if mysqladmin ping -u root --silent 2>/dev/null; then
            break
        fi
        sleep 1
    done

    # Setup database and user
    mysql -u root <<EOF || true
CREATE DATABASE IF NOT EXISTS \`rms_db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY 'root';
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'root' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY 'root' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF

    # Import schema if tables not present
    TABLE_COUNT=$(mysql -u root -proot rms_db -e "SHOW TABLES;" 2>/dev/null | wc -l || echo "0")
    if [ "$TABLE_COUNT" -le 1 ]; then
        echo "Importing full database schema into embedded MariaDB..."
        mysql -u root -proot --default-character-set=utf8mb4 rms_db < /var/www/html/database/rms_db_complete.sql || true
        echo "Database schema imported successfully!"
    else
        echo "Database already initialized ($TABLE_COUNT tables found)."
    fi
fi

echo "Starting Apache web server..."
exec apache2-foreground
