<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== Laravel Route Debug ===\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n\n";

// Test route resolution
$routes = [
    '/',
    '/properties',
    '/p/test-slug',
    '/admin',
];

foreach ($routes as $route) {
    try {
        $request = \Illuminate\Http\Request::create($route, 'GET');
        $response = $app->handle($request);
        echo "Route: $route -> Status: " . $response->getStatusCode() . "\n";
    } catch (Exception $e) {
        echo "Route: $route -> Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Registered Routes ===\n";
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$router = $app->make('router');

foreach ($router->getRoutes() as $route) {
    echo $route->methods()[0] . " " . $route->uri() . " -> " . ($route->getName() ?: 'unnamed') . "\n";
}