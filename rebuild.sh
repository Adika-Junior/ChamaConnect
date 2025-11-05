#!/bin/bash
# Rebuild Docker image with all latest changes

set -e  # Exit on error

echo "🔄 Stopping all containers..."
docker compose down

echo "🏗️  Building Docker image with latest changes..."
docker build -t ttms_app:latest .

echo "📦 Initializing application code volume..."
docker compose run --rm init_app

echo "🚀 Starting services..."
docker compose up -d db app nginx

echo "⏳ Waiting for services to be ready..."
sleep 5

echo "✅ Checking application status..."
docker compose ps

echo ""
echo "🎉 Rebuild complete!"
echo "📱 Access application at: http://localhost:8000"
echo "🏥 Health check: http://localhost:8000/healthz"

