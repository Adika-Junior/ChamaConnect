#!/bin/bash
# Quick Start Script for Team Task Management System
# Run this script to build and start the application

echo "=============================================="
echo "Team Task Management System - Quick Start"
echo "=============================================="
echo ""

# Check if Docker is running
echo "Checking Docker status..."
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running!"
    echo "Please run: sudo service docker start"
    echo "Then run this script again."
    exit 1
fi

echo "✅ Docker is running"
echo ""

# Change to project directory
cd /home/j_view/Projects/Team-Task-Management-System

# Check if .env.docker exists
if [ ! -f .env.docker ]; then
    echo "❌ .env.docker file not found!"
    echo "Please create it from .env.example"
    exit 1
fi

echo "✅ Environment file exists"
echo ""

# Build Docker image
echo "Building Docker image (this may take a few minutes)..."
docker build -t ttms_app:latest . || {
    echo "❌ Docker build failed!"
    exit 1
}

echo "✅ Docker image built successfully"
echo ""

# Initialize application
echo "Initializing application..."
docker compose run --rm init_app || {
    echo "❌ Application initialization failed!"
    exit 1
}

echo "✅ Application initialized"
echo ""

# Start services
echo "Starting services..."
docker compose up -d db app nginx || {
    echo "❌ Failed to start services!"
    exit 1
}

echo "✅ Services started"
echo ""

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 5

# Run migrations
echo "Running database migrations..."
docker compose exec app php artisan migrate --force || {
    echo "⚠️  Migrations failed - database might already be migrated"
}

echo "✅ Migrations completed"
echo ""

# Display status
echo "=============================================="
echo "Application Status:"
echo "=============================================="
docker compose ps

echo ""
echo "✅ Setup Complete!"
echo ""
echo "🌐 Application URL: http://localhost:8000"
echo "🏥 Health Check: http://localhost:8000/healthz"
echo ""
echo "Useful commands:"
echo "  View logs:      docker compose logs -f app"
echo "  Stop services:   docker compose down"
echo "  Restart:        docker compose restart"
echo ""

