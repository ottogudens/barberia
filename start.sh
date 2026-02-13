#!/bin/bash

# Substitute the PORT environment variable in nginx.conf
sed -i "s/\$PORT/$PORT/g" /app/nginx.conf

# Configure PHP-FPM user and group dynamically
sed -i "s/FPM_USER/$(whoami)/g" /app/php-fpm.conf
sed -i "s/FPM_GROUP/$(id -gn)/g" /app/php-fpm.conf

# Initialize Database
echo "Running Database Initialization..."
php /app/init_railway_db.php

# Create directory for sockets if needed
mkdir -p /tmp/php

# Start PHP-FPM in the background
echo "Starting PHP-FPM..."
php-fpm -y /app/php-fpm.conf -R &

# Wait a moment
sleep 2

# Start Nginx in the foreground
echo "Starting Nginx..."
nginx -c /app/nginx.conf
