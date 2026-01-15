<?php

/**
 * Vercel Serverless Function Handler for Laravel
 * 
 * This file handles all incoming requests and routes them through Laravel.
 * It's placed in the api/ directory so Vercel treats it as a serverless function.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Get the project root (two levels up from api/)
$basePath = dirname(__DIR__);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $basePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

// Ensure storage directories exist (required for Vercel)
$storagePaths = [
    $basePath.'/storage/framework/cache',
    $basePath.'/storage/framework/sessions',
    $basePath.'/storage/framework/views',
    $basePath.'/storage/logs',
];

foreach ($storagePaths as $path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// Capture the request
$request = Request::capture();

// Handle the request and return the response
$response = $app->handleRequest($request);

// Send the response
$response->send();

// Terminate the application
$app->terminate();
