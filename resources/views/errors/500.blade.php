<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-orange-600">500</h1>
                <h2 class="text-3xl font-semibold text-gray-800 mt-4">Server Error</h2>
                <p class="text-gray-600 mt-4">
                    Oops! Something went wrong on our end. We're working to fix it.
                </p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>What can I do?</strong><br>
                    Try refreshing the page or come back in a few minutes. 
                    If the problem persists, please contact our support team.
                </p>
            </div>
            
            <div class="space-y-4">
                <button onclick="window.location.reload()" 
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Refresh Page
                </button>
                
                <div>
                    <a href="{{ route('properties.index') }}" 
                       class="text-blue-600 hover:text-blue-800 underline">
                        Browse Properties
                    </a>
                    <span class="text-gray-400 mx-2">|</span>
                    <a href="{{ url('/') }}" 
                       class="text-blue-600 hover:text-blue-800 underline">
                        Go to Homepage
                    </a>
                </div>
            </div>
            
            @if(config('app.env') !== 'production')
                <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-xs text-red-800 font-mono text-left">
                        <strong>Debug Info (Development Only):</strong><br>
                        {{ $exception->getMessage() ?? 'No additional information available.' }}
                    </p>
                </div>
            @endif
            
            <div class="mt-8 text-sm text-gray-500">
                <p>Need help? Contact support:</p>
                <a href="mailto:support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                </a>
            </div>
            
            <div class="mt-12">
                <svg class="mx-auto h-48 w-48 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" 
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
    </div>
</body>
</html>
