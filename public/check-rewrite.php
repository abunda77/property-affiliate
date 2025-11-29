<?php
echo "<h1>Rewrite Check</h1>";

echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='/properties'>Test /properties</a></li>";
echo "<li><a href='/index.php/properties'>Test /index.php/properties</a></li>";
echo "<li><a href='/'>Test /</a></li>";
echo "</ul>";

echo "<h2>Server Info:</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . ($_SERVER['PHP_SELF'] ?? 'not set') . "</p>";
echo "<p><strong>REDIRECT_STATUS:</strong> " . ($_SERVER['REDIRECT_STATUS'] ?? 'not set') . "</p>";

echo "<h2>Rewrite Test:</h2>";
if (file_exists('.htaccess')) {
    echo "<p>✓ .htaccess exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "<p>✗ .htaccess not found</p>";
}

echo "<h2>Laravel Route Test:</h2>";
try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    // Test /properties route
    $request = \Illuminate\Http\Request::create('/properties', 'GET');
    $request->server->add($_SERVER);
    
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $headerName = str_replace('HTTP_', '', $key);
            $headerName = str_replace('_', '-', $headerName);
            $request->headers->set($headerName, $value);
        }
    }
    
    $response = $app->handle($request);
    echo "<p><strong>Status for /properties:</strong> " . $response->getStatusCode() . "</p>";
    
    if ($response->getStatusCode() == 200) {
        echo "<p style='color: green;'>✓ Route /properties works in Laravel!</p>";
        echo "<p>Problem is with web server rewrite rules.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>