<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-blue-600">404</h1>
                <h2 class="text-3xl font-semibold text-gray-800 mt-4">Page Not Found</h2>
                <p class="text-gray-600 mt-4">
                    {{ $message ?? 'The page you are looking for could not be found.' }}
                </p>
            </div>
            
            <div class="space-y-4">
                <a href="{{ route('properties.index') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Properties
                </a>
                
                <div>
                    <a href="{{ url('/') }}" 
                       class="text-blue-600 hover:text-blue-800 underline">
                        Go to Homepage
                    </a>
                </div>
            </div>
            
            <div class="mt-12">
                <svg class="mx-auto h-48 w-48 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" 
                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
</body>
</html>
