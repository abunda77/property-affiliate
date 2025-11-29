<?php
// Check session configuration on production server
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

echo "<h1>Session Configuration Check</h1>";

echo "<h2>Environment</h2>";
echo "<p><strong>APP_ENV:</strong> " . env('APP_ENV') . "</p>";
echo "<p><strong>APP_DEBUG:</strong> " . (env('APP_DEBUG') ? 'true' : 'false') . "</p>";
echo "<p><strong>APP_URL:</strong> " . env('APP_URL') . "</p>";

echo "<h2>Session Configuration</h2>";
echo "<p><strong>Driver:</strong> " . config('session.driver') . "</p>";
echo "<p><strong>Lifetime:</strong> " . config('session.lifetime') . " minutes</p>";
echo "<p><strong>Path:</strong> " . config('session.path') . "</p>";
echo "<p><strong>Domain:</strong> " . (config('session.domain') ?: 'null') . "</p>";
echo "<p><strong>Secure:</strong> " . (config('session.secure') ? 'true ✓' : 'false ✗') . "</p>";
echo "<p><strong>HTTP Only:</strong> " . (config('session.http_only') ? 'true ✓' : 'false ✗') . "</p>";
echo "<p><strong>Same Site:</strong> " . config('session.same_site') . "</p>";

echo "<h2>Request Info</h2>";
echo "<p><strong>Protocol:</strong> " . ($_SERVER['REQUEST_SCHEME'] ?? 'unknown') . "</p>";
echo "<p><strong>HTTPS:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'yes ✓' : 'no ✗') . "</p>";
echo "<p><strong>X-Forwarded-Proto:</strong> " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set') . "</p>";
echo "<p><strong>Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";

echo "<h2>Expected Configuration for Production</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<p>✓ APP_ENV = production</p>";
echo "<p>✓ APP_DEBUG = false</p>";
echo "<p>✓ APP_URL = https://pams.produkmastah.com</p>";
echo "<p>✓ SESSION_SECURE_COOKIE = true</p>";
echo "<p>✓ SESSION_HTTP_ONLY = true</p>";
echo "<p>✓ SESSION_SAME_SITE = lax</p>";
echo "<p>✓ HTTPS detected = yes</p>";
echo "</div>";

echo "<h2>Status</h2>";
$allGood = true;

if (config('session.secure') !== true) {
    echo "<p style='color: red;'>✗ SESSION_SECURE_COOKIE is not true!</p>";
    $allGood = false;
}

if (config('session.http_only') !== true) {
    echo "<p style='color: red;'>✗ SESSION_HTTP_ONLY is not true!</p>";
    $allGood = false;
}

if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    if (!isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https') {
        echo "<p style='color: red;'>✗ HTTPS not detected properly!</p>";
        $allGood = false;
    }
}

if ($allGood) {
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✓ All configuration looks good!</p>";
    echo "<p>If you still get 403 after login:</p>";
    echo "<ol>";
    echo "<li>Clear browser cookies for this domain</li>";
    echo "<li>Try in Incognito/Private mode</li>";
    echo "<li>Run: <code>php artisan config:cache</code></li>";
    echo "<li>Check: <code>tail -f storage/logs/laravel.log</code></li>";
    echo "</ol>";
} else {
    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>✗ Configuration issues found!</p>";
    echo "<p>Please update .env file and run:</p>";
    echo "<pre>php artisan config:clear\nphp artisan config:cache</pre>";
}

echo "<hr>";
echo "<p><a href='/admin'>Go to Admin Login</a></p>";
?>