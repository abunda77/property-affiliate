<?php

// Script untuk clear cache Laravel
echo "Clearing Laravel caches...\n";

// Clear application cache
exec('php artisan cache:clear', $output1);
echo "✓ Application cache cleared\n";

// Clear configuration cache
exec('php artisan config:clear', $output2);
echo "✓ Configuration cache cleared\n";

// Clear route cache
exec('php artisan route:clear', $output3);
echo "✓ Route cache cleared\n";

// Clear view cache
exec('php artisan view:clear', $output4);
echo "✓ View cache cleared\n";

// Clear compiled services
exec('php artisan clear-compiled', $output5);
echo "✓ Compiled services cleared\n";

// Optimize for production
if (isset($argv[1]) && $argv[1] === '--optimize') {
    exec('php artisan config:cache', $output6);
    echo "✓ Configuration cached\n";
    
    exec('php artisan route:cache', $output7);
    echo "✓ Routes cached\n";
    
    exec('php artisan view:cache', $output8);
    echo "✓ Views cached\n";
}

echo "\nCache clearing completed!\n";
echo "If you're still having issues, try restarting your web server.\n";