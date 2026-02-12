#!/bin/bash

# Substitute the PORT environment variable in nginx.conf
sed -i "s/\$PORT/$PORT/g" /app/nginx.conf

# Configure PHP-FPM user and group dynamically
sed -i "s/FPM_USER/$(whoami)/g" /app/php-fpm.conf
sed -i "s/FPM_GROUP/$(id -gn)/g" /app/php-fpm.conf

# Initialize Database
echo "Running Database Initialization..."
php /app/init_railway_db.php

# Start PHP-FPM in the background
php-fpm -y /app/php-fpm.conf -R &

# Start Nginx in the foreground
nginx -c /app/nginx.conf
