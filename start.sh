#!/bin/bash

# Substitute the PORT environment variable in nginx.conf
sed -i "s/\$PORT/$PORT/g" /app/nginx.conf

# Start PHP-FPM in the background
php-fpm -y /app/php-fpm.conf &

# Start Nginx in the foreground
nginx -c /app/nginx.conf
