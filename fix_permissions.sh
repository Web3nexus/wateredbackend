#!/bin/bash

# Laravel Permission Fix Script (Shared Hosting Compatible)
# Run this on your server to resolve missing images and 403 Forbidden errors.

echo " fixing permissions..."

# 1. Clear existing link to avoid "link already exists" error
if [ -L public/storage ]; then
    echo "Removed old storage link."
    rm public/storage
fi

# 2. Ensure the target directory exists
if [ ! -d storage/app/public ]; then
    echo "Creating storage/app/public directory..."
    mkdir -p storage/app/public
fi

# 3. Create the symbolic link
/opt/alt/php84/usr/bin/php artisan storage:link
echo "Storage link created."

# 4. Set directory permissions (755 is standard for shared hosting folders)
find storage -type d -exec chmod 755 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;

# 5. Set file permissions (644 is standard for shared hosting files)
find storage -type f -exec chmod 644 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;

echo "Permissions updated successfully. Please check your website for images."
