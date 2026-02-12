#!/bin/bash

# Substitute the PORT environment variable in nginx.conf
sed -i "s/\$PORT/$PORT/g" /app/nginx.conf

# Configure PHP-FPM user and group dynamically
# Start PHP-FPM in the background
# We use php-fpm (default in many nixpacks images)
php-fpm -y /app/php-fpm.conf -R &

# Wait a moment to ensure it doesn't crash immediately
sleep 2

# Start Nginx in the foreground
nginx -c /app/nginx.conf
