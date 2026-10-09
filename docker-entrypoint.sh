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

# Ensure MariaDB runtime directories exist
mkdir -p /run/mysqld /var/run/mysqld /var/lib/mysql /var/log/mysql
chown -R mysql:mysql /run/mysqld /var/run/mysqld /var/lib/mysql /var/log/mysql
chmod 777 /run/mysqld /var/run/mysqld

# Setup database: if cloud DB is specified, connect to it; otherwise start embedded MariaDB
if [ -n "$DB_HOST" ] || [ -n "$DATABASE_URL" ] || [ -n "$MYSQL_URL" ]; then
    echo "Cloud database environment variable detected ($DB_HOST). Checking database..."
    php spark db:init || echo "Notice: db:init check completed."
else
    echo "No external cloud DB configured. Starting embedded MariaDB service..."

    # Ensure datadir has system tables
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        echo "Initializing MariaDB system tables..."
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1 || mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1 || true
    fi

    # Start MariaDB directly via mysqld_safe daemon in background
    echo "Launching mysqld_safe daemon..."
    /usr/bin/mysqld_safe --user=mysql --skip-syslog > /var/log/mysql.log 2>&1 &
    MYSQL_PID=$!

    # Wait up to 25 seconds for socket/server to be ready
    echo "Waiting for MariaDB to become ready..."
    READY=0
    for i in {1..25}; do
        if mysqladmin ping --silent 2>/dev/null || mysqladmin -u root -proot ping --silent 2>/dev/null; then
            READY=1
            echo "MariaDB started and ready! (attempt $i)"
            break
        fi
        sleep 1
    done

    if [ "$READY" -eq 0 ]; then
        echo "Warning: MariaDB did not respond within 25 seconds. Checking logs:"
        tail -n 25 /var/log/mysql.log || true
    fi

    # Create symlinks for Unix sockets in all standard locations
    ln -sf /run/mysqld/mysqld.sock /var/run/mysqld/mysqld.sock 2>/dev/null || true
    ln -sf /run/mysqld/mysqld.sock /tmp/mysql.sock 2>/dev/null || true

    # Setup database and user privileges for both localhost and 127.0.0.1
    mysql -u root <<EOF || mysql -u root -proot <<EOF || true
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
        echo "Importing complete database schema into embedded MariaDB..."
        mysql -u root -proot --default-character-set=utf8mb4 rms_db < /var/www/html/database/rms_db_complete.sql 2>&1 || true
        echo "Database schema import completed!"
    else
        echo "Database already initialized ($TABLE_COUNT tables found)."
    fi
fi

echo "Starting Apache web server..."
exec apache2-foreground
