<?php
// Test direct Laravel routing
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

// Simulate the exact request that's failing
$host = $_SERVER['HTTP_HOST'] ?? 'pams.produkmastah.com';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

echo "<h1>Route Test for: $host$uri</h1>";

try {
    // Create request exactly as it comes from web server
    $request = \Illuminate\Http\Request::create($uri, 'GET');
    $request->headers->set('HOST', $host);
    
    // Add all server variables
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $headerName = str_replace('HTTP_', '', $key);
            $headerName = str_replace('_', '-', $headerName);
            $request->headers->set($headerName, $value);
        }
    }
    
    echo "<h2>Request Details:</h2>";
    echo "<p>Method: " . $request->method() . "</p>";
    echo "<p>URL: " . $request->fullUrl() . "</p>";
    echo "<p>Path: " . $request->path() . "</p>";
    echo "<p>Host: " . $request->getHost() . "</p>";
    
    echo "<h2>Headers:</h2>";
    foreach ($request->headers->all() as $name => $values) {
        echo "<p>$name: " . implode(', ', $values) . "</p>";
    }
    
    // Handle the request
    $response = $app->handle($request);
    
    echo "<h2>Response:</h2>";
    echo "<p>Status: " . $response->getStatusCode() . "</p>";
    
    if ($response->getStatusCode() == 302) {
        echo "<p>Redirect to: " . $response->headers->get('Location') . "</p>";
    }
    
    if ($response->getStatusCode() == 404) {
        echo "<p style='color: red;'>404 Not Found - Route not matched</p>";
        
        // Show available routes
        echo "<h3>Available Routes:</h3>";
        $router = $app->make('router');
        foreach ($router->getRoutes() as $route) {
            $methods = implode('|', $route->methods());
            echo "<p>$methods " . $route->uri() . "</p>";
        }
    }
    
    // Show response content if it's not too large
    $content = $response->getContent();
    if (strlen($content) < 1000) {
        echo "<h3>Response Content:</h3>";
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>