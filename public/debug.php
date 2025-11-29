<?php
echo "<h1>Debug Info</h1>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

echo "<h2>Laravel Test</h2>";
if (file_exists('../bootstrap/app.php')) {
    echo "<p>✓ Laravel bootstrap file exists</p>";
    
    try {
        require_once '../vendor/autoload.php';
        echo "<p>✓ Composer autoload works</p>";
        
        $app = require_once '../bootstrap/app.php';
        echo "<p>✓ Laravel app created</p>";
        
        // Test a simple request
        $request = \Illuminate\Http\Request::create('/', 'GET');
        $request->headers->set('HOST', $_SERVER['HTTP_HOST']);
        
        $response = $app->handle($request);
        echo "<p>✓ Laravel handles requests (Status: " . $response->getStatusCode() . ")</p>";
        
        if ($response->getStatusCode() == 302) {
            echo "<p>Redirect to: " . $response->headers->get('Location') . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>✗ Laravel error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p>✗ Laravel bootstrap file not found</p>";
}

echo "<h2>File Permissions</h2>";
$files = ['../bootstrap/app.php', '../vendor/autoload.php', '../storage', '../bootstrap/cache'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>$file: " . (is_readable($file) ? 'readable' : 'not readable') . "</p>";
    } else {
        echo "<p>$file: not found</p>";
    }
}
?>