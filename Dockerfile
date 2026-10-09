FROM php:8.2-apache

# Install system dependencies and required PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    mariadb-server \
    mariadb-client \
    zip \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo_mysql \
        intl \
        mbstring \
        gd \
        zip \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Pre-configure MariaDB runtime directories and initialize system datadir
RUN mkdir -p /run/mysqld /var/run/mysqld /var/lib/mysql /var/log/mysql \
    && chown -R mysql:mysql /run/mysqld /var/run/mysqld /var/lib/mysql /var/log/mysql \
    && chmod 777 /run/mysqld /var/run/mysqld \
    && (mariadb-install-db --user=mysql --datadir=/var/lib/mysql || mysql_install_db --user=mysql --datadir=/var/lib/mysql)

# Configure PHP default MySQL socket
RUN echo "pdo_mysql.default_socket=/run/mysqld/mysqld.sock\nmysqli.default_socket=/run/mysqld/mysqld.sock" > /usr/local/etc/php/conf.d/docker-php-ext-mysql-socket.ini

# Enable Apache mod_rewrite and mod_headers
RUN a2enmod rewrite headers

# Configure Apache DocumentRoot to point to CodeIgniter 4's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure directory permissions and .htaccess overrides
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Setup entrypoint script (strip carriage returns to prevent CRLF execution issues)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN tr -d '\r' < /usr/local/bin/docker-entrypoint.sh > /usr/local/bin/entrypoint.sh \
    && mv /usr/local/bin/entrypoint.sh /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure storage directories exist and have proper permissions
RUN mkdir -p /var/www/html/writable/cache /var/www/html/writable/logs /var/www/html/writable/session /var/www/html/writable/uploads /var/www/html/writable/debugbar /var/www/html/writable/backups \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80

CMD ["docker-entrypoint.sh"]
