#!/bin/bash
set -e

# Install Composer if not available
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    COMPOSER_CMD="php composer.phar"
else
    COMPOSER_CMD="composer"
fi

# Install PHP dependencies
echo "Installing PHP dependencies..."
$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Install Node dependencies
echo "Installing Node dependencies..."
npm ci

# Build assets
echo "Building assets..."
npm run build

echo "Build completed successfully!"
