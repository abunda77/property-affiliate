<?php

// Debug script untuk test performance
require_once __DIR__ . '/vendor/autoload.php';

// Import DB facade
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG TEST PERFORMANCE ===\n";

// Check Scout index
echo "1. Checking Scout index...\n";
try {
    $properties = App\Models\Property::all();
    echo "Total properties in database: " . $properties->count() . "\n";
    
    $searchableCount = App\Models\Property::published()->count();
    echo "Searchable properties count: " . $searchableCount . "\n";
    
    // Test search functionality
    $searchResults = App\Models\Property::search('Villa')->get();
    echo "Search results for 'Villa': " . $searchResults->count() . "\n";
} catch (Exception $e) {
    echo "Scout error: " . $e->getMessage() . "\n";
}

// Check Livewire component
echo "\n2. Testing Livewire component...\n";
try {
    $component = new \App\Livewire\PropertyCatalog();
    echo "Livewire component created successfully\n";
    
    $start = microtime(true);
    $renderResult = $component->render();
    $end = microtime(true);
    
    echo "Render time: " . ($end - $start) . " seconds\n";
    echo "Render result type: " . gettype($renderResult) . "\n";
} catch (Exception $e) {
    echo "Livewire error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Check database performance
echo "\n3. Database performance check...\n";
try {
    $start = microtime(true);
    DB::table('properties')->count();
    $end = microtime(true);
    echo "Database query time: " . ($end - $start) . " seconds\n";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

// Check memory usage
echo "\n4. Memory usage...\n";
echo "Current memory usage: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
echo "Peak memory usage: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";

echo "\n=== END DEBUG ===\n";