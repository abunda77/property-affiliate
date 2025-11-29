<?php
echo "<h1>HTTPS Detection Test</h1>";

echo "<h2>Server Variables:</h2>";
$httpsVars = [
    'HTTPS' => $_SERVER['HTTPS'] ?? 'not set',
    'HTTP_X_FORWARDED_PROTO' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set',
    'HTTP_X_FORWARDED_SSL' => $_SERVER['HTTP_X_FORWARDED_SSL'] ?? 'not set',
    'HTTP_CF_VISITOR' => $_SERVER['HTTP_CF_VISITOR'] ?? 'not set',
    'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? 'not set',
    'REQUEST_SCHEME' => $_SERVER['REQUEST_SCHEME'] ?? 'not set',
];

foreach ($httpsVars as $key => $value) {
    echo "<p><strong>$key:</strong> $value</p>";
}

echo "<h2>Laravel Test:</h2>";
try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    $request = \Illuminate\Http\Request::create('/', 'GET');
    
    // Copy all server variables to request
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $headerName = str_replace('HTTP_', '', $key);
            $headerName = str_replace('_', '-', $headerName);
            $request->headers->set($headerName, $value);
        }
    }
    
    $request->server->add($_SERVER);
    
    echo "<p><strong>Request is secure:</strong> " . ($request->isSecure() ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>Request scheme:</strong> " . $request->getScheme() . "</p>";
    echo "<p><strong>Request URL:</strong> " . $request->url() . "</p>";
    
    // Test URL generation
    $response = $app->handle($request);
    echo "<p><strong>Response status:</strong> " . $response->getStatusCode() . "</p>";
    
    if ($response->getStatusCode() == 302) {
        $location = $response->headers->get('Location');
        echo "<p><strong>Redirect to:</strong> $location</p>";
        echo "<p><strong>Uses HTTPS:</strong> " . (strpos($location, 'https://') === 0 ? 'YES' : 'NO') . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>