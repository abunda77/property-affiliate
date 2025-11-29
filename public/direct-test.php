<?php
// Direct test bypassing rewrite
$_SERVER['REQUEST_URI'] = '/properties';
$_SERVER['PATH_INFO'] = '/properties';

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

$request = \Illuminate\Http\Request::create('/properties', 'GET');

// Copy all headers
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $headerName = str_replace('HTTP_', '', $key);
        $headerName = str_replace('_', '-', $headerName);
        $request->headers->set($headerName, $value);
    }
}

$request->server->add($_SERVER);

try {
    $response = $app->handle($request);
    
    // Output the response
    http_response_code($response->getStatusCode());
    
    foreach ($response->headers->all() as $name => $values) {
        foreach ($values as $value) {
            header("$name: $value", false);
        }
    }
    
    echo $response->getContent();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>