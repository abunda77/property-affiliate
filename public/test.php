<?php
echo "Web server working! PHP version: " . PHP_VERSION . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Server software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";

// Test if Laravel bootstrap works
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "✓ Composer autoload works\n";
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✓ Laravel bootstrap works\n";
    
    echo "APP_URL: " . env('APP_URL') . "\n";
    echo "APP_ENV: " . env('APP_ENV') . "\n";
    
} catch (Exception $e) {
    echo "✗ Laravel error: " . $e->getMessage() . "\n";
}
?>