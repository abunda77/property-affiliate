<?php
echo "<h1>Server Configuration Info</h1>";

echo "<h2>PHP Info</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "</p>";

echo "<h2>Loaded Modules</h2>";
$modules = get_loaded_extensions();
$rewrite_modules = ['mod_rewrite', 'rewrite'];
foreach ($rewrite_modules as $mod) {
    if (in_array($mod, $modules)) {
        echo "<p style='color: green;'>✓ $mod loaded</p>";
    }
}

echo "<h2>Apache/LiteSpeed Modules</h2>";
if (function_exists('apache_get_modules')) {
    $apache_modules = apache_get_modules();
    if (in_array('mod_rewrite', $apache_modules)) {
        echo "<p style='color: green;'>✓ mod_rewrite enabled</p>";
    } else {
        echo "<p style='color: red;'>✗ mod_rewrite not found</p>";
    }
} else {
    echo "<p>apache_get_modules() not available (normal for LiteSpeed)</p>";
}

echo "<h2>Rewrite Test</h2>";
echo "<p>Try accessing these URLs:</p>";
echo "<ul>";
echo "<li><a href='/properties'>Direct: /properties</a> (should work if rewrite is OK)</li>";
echo "<li><a href='/index.php/properties'>With index.php: /index.php/properties</a> (fallback)</li>";
echo "</ul>";

echo "<h2>Current Request Info</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "</p>";
echo "<p><strong>SCRIPT_FILENAME:</strong> " . ($_SERVER['SCRIPT_FILENAME'] ?? 'not set') . "</p>";

echo "<h2>.htaccess Status</h2>";
if (file_exists(__DIR__ . '/.htaccess')) {
    echo "<p style='color: green;'>✓ .htaccess exists</p>";
    $htaccess_content = file_get_contents(__DIR__ . '/.htaccess');
    echo "<p><strong>Size:</strong> " . strlen($htaccess_content) . " bytes</p>";
    echo "<details><summary>View .htaccess content</summary><pre>" . htmlspecialchars($htaccess_content) . "</pre></details>";
} else {
    echo "<p style='color: red;'>✗ .htaccess not found</p>";
}

echo "<h2>Directory Permissions</h2>";
$dirs = [
    '.' => __DIR__,
    '..' => dirname(__DIR__),
    '../storage' => dirname(__DIR__) . '/storage',
    '../bootstrap/cache' => dirname(__DIR__) . '/bootstrap/cache',
];

foreach ($dirs as $name => $path) {
    $perms = fileperms($path);
    $perms_str = substr(sprintf('%o', $perms), -4);
    echo "<p><strong>$name:</strong> $perms_str</p>";
}
?>