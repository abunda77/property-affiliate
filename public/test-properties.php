<?php
// Test properties page without Livewire
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

echo "<h1>Properties Page Test</h1>";

try {
    // Test database connection
    $properties = \App\Models\Property::published()->count();
    echo "<p>✓ Database connected: $properties published properties</p>";
    
    // Test if view exists
    if (view()->exists('properties.index')) {
        echo "<p>✓ View 'properties.index' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ View 'properties.index' not found</p>";
    }
    
    // Test if layout exists
    if (view()->exists('layouts.app')) {
        echo "<p>✓ Layout 'layouts.app' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Layout 'layouts.app' not found</p>";
    }
    
    // Test Livewire component
    if (class_exists(\App\Livewire\PropertyCatalog::class)) {
        echo "<p>✓ PropertyCatalog Livewire component exists</p>";
    } else {
        echo "<p style='color: red;'>✗ PropertyCatalog component not found</p>";
    }
    
    // Test session
    session_start();
    echo "<p>✓ Session working</p>";
    
    // Test settings
    try {
        $settings = app(\App\Settings\GeneralSettings::class);
        echo "<p>✓ Settings loaded</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠ Settings error: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>Try accessing:</h2>";
    echo "<ul>";
    echo "<li><a href='/properties'>Go to /properties</a></li>";
    echo "<li><a href='/'>Go to homepage</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>