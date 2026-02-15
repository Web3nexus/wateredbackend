#!/bin/bash

# Laravel Permission Fix Script
# Run this on your server to resolve 403 Forbidden errors for images and assets.

echo "Setting permissions for storage and bootstrap/cache..."

# Change ownership to web user (adjust if your web user is different, e.g., www-data)
# sudo chown -R $USER:www-data .

# Set directory permissions
find storage -type d -exec chmod 775 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;

# Set file permissions
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;

# Ensure storage link exists
php artisan storage:link

echo "Permissions updated successfully. Please check your website for images."
