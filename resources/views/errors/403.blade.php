<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-red-600">403</h1>
                <h2 class="text-3xl font-semibold text-gray-800 mt-4">Access Denied</h2>
                <p class="text-gray-600 mt-4">
                    {{ $message ?? 'You do not have permission to access this resource.' }}
                </p>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <strong>Why am I seeing this?</strong><br>
                    This page or action requires specific permissions that your account does not have. 
                    If you believe this is an error, please contact your administrator.
                </p>
            </div>
            
            <div class="space-y-4">
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}" 
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('properties.index') }}" 
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Browse Properties
                    </a>
                @endauth
                
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
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>
    </div>
</body>
</html>
