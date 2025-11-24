<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $settings = app(\App\Settings\GeneralSettings::class);
    @endphp

    <title>Katalog Properti - {{ $settings->seo_meta_title ?? config('app.name', 'PAMS') }}</title>
    
    <!-- Favicon -->
    @if($settings->logo_path)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($settings->logo_path) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    @if($settings->logo_path)
                        <a href="{{ $settings->logo_url ?: route('properties.index') }}" class="flex items-center">
                            <img src="{{ Storage::url($settings->logo_path) }}" alt="{{ $settings->seo_meta_title ?? config('app.name', 'PAMS') }}" class="h-16 w-auto">
                        </a>
                    @else
                        <a href="{{ route('properties.index') }}" class="text-2xl font-bold text-blue-600">
                            {{ $settings->seo_meta_title ?? config('app.name', 'PAMS') }}
                        </a>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('properties.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        Katalog Properti
                    </a>
                    @auth
                        <a href="{{ url('/admin') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Dashboard
                        </a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                Login
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Daftar Affiliate
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-blue-600" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="{{ route('properties.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        Katalog Properti
                    </a>
                    @auth
                        <a href="{{ url('/admin') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Dashboard
                        </a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                Login
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors text-center">
                                Daftar Affiliate
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        @livewire('property-catalog')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    @if($settings->logo_path)
                        <a href="{{ $settings->logo_url ?: route('properties.index') }}" class="inline-block mb-4">
                            <img src="{{ Storage::url($settings->logo_path) }}" alt="{{ $settings->seo_meta_title ?? config('app.name', 'PAMS') }}" class="h-12 w-auto">
                        </a>
                    @else
                        <h3 class="text-xl font-bold mb-4">{{ $settings->seo_meta_title ?? config('app.name', 'PAMS') }}</h3>
                    @endif
                    <p class="text-gray-400">
                        {{ $settings->seo_meta_description ?? 'Property Affiliate Management System - Platform properti dengan sistem afiliasi terpercaya.' }}
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Link Cepat</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('properties.index') }}" class="hover:text-white transition-colors">Katalog Properti</a></li>
                        @auth
                            <li><a href="{{ url('/admin') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                        @else
                            @if (Route::has('login'))
                                <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a></li>
                            @endif
                            @if (Route::has('register'))
                                <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar Affiliate</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Kontak</h4>
                    <p class="text-gray-400">
                        @if($settings->contact_email)
                            Email: {{ $settings->contact_email }}<br>
                        @endif
                        @if($settings->contact_whatsapp)
                            WhatsApp: {{ $settings->contact_whatsapp }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} PAMS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
