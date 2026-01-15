#!/bin/bash

# Script to prepare Laravel project for Vercel deployment
# This builds vendor folder and prepares the project

set -e

echo "🚀 Preparing Laravel project for Vercel deployment..."
echo ""

# Check if composer is available
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer is not installed."
    echo "Please install Composer first: https://getcomposer.org/"
    exit 1
fi

# Check if npm is available
if ! command -v npm &> /dev/null; then
    echo "❌ Error: npm is not installed."
    echo "Please install Node.js first: https://nodejs.org/"
    exit 1
fi

echo "✅ Composer found: $(composer --version)"
echo "✅ npm found: $(npm --version)"
echo ""

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
echo "📦 Installing Node dependencies..."
npm ci

# Build assets
echo "🔨 Building frontend assets..."
npm run build

echo ""
echo "✅ Build completed successfully!"
echo ""
echo "📝 Next steps:"
echo "1. Check if vendor/ folder exists: ls -la vendor/"
echo "2. Temporarily remove /vendor/ from .gitignore (comment it out)"
echo "3. Commit vendor folder: git add vendor/ && git commit -m 'Add vendor for Vercel'"
echo "4. Push to trigger Vercel deployment: git push"
echo ""
echo "⚠️  Note: Vendor folder (~50-100MB) will be committed to your repo."
echo "   This is required for Vercel deployment since Composer isn't available during build."
