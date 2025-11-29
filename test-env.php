<?php

echo "=== Environment Test ===\n";

// Test .env loading
if (file_exists('.env')) {
    echo "✓ .env file exists\n";
} else {
    echo "✗ .env file missing\n";
}

// Test Laravel bootstrap
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel bootstrap successful\n";
    
    // Test database connection
    try {
        $pdo = new PDO(
            'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
        echo "✓ Database connection successful\n";
    } catch (Exception $e) {
        echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    }
    
    // Test key environment variables
    $envVars = [
        'APP_URL' => env('APP_URL'),
        'APP_ENV' => env('APP_ENV'),
        'APP_DEBUG' => env('APP_DEBUG') ? 'true' : 'false',
        'DB_DATABASE' => env('DB_DATABASE'),
        'SESSION_DOMAIN' => env('SESSION_DOMAIN'),
    ];
    
    echo "\n=== Environment Variables ===\n";
    foreach ($envVars as $key => $value) {
        echo "$key: $value\n";
    }
    
} catch (Exception $e) {
    echo "✗ Laravel bootstrap failed: " . $e->getMessage() . "\n";
}