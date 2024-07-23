<?php

// Load all dependencies
require_once 'vendor/autoload.php';

define('BASE_DIR', __DIR__);

if (file_exists(BASE_DIR . '/.env')) {
    // Load environment variables
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Serve the request
require_once 'src/app.php';