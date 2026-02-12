#!/bin/bash

# Substitute the PORT environment variable in nginx.conf
sed -i "s/\$PORT/$PORT/g" /app/nginx.conf

# Configure PHP-FPM user and group dynamically
# Ensure /run/php exists for sockets if needed (though we use /tmp)
mkdir -p /tmp/php

# Start PHP-FPM in the background
echo "Starting PHP-FPM..."
php-fpm -y /app/php-fpm.conf -R &

# Wait a moment
sleep 2

# Start Nginx in the foreground
echo "Starting Nginx..."
nginx -c /app/nginx.conf
