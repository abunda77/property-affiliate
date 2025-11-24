<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', (($settings && $settings->seo_meta_title) ? $settings->seo_meta_title : config('app.name', 'PAMS')) . ' - Property Affiliate Management System')</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ ($settings && $settings->seo_meta_description) ? $settings->seo_meta_description : 'Platform properti dengan sistem afiliasi terpercaya' }}">
    <meta name="keywords" content="{{ ($settings && $settings->seo_meta_keywords) ? $settings->seo_meta_keywords : 'properti, affiliate, real estate' }}">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ ($settings && $settings->seo_meta_title) ? $settings->seo_meta_title : config('app.name', 'PAMS') }}">
    <meta property="og:description" content="{{ ($settings && $settings->seo_meta_description) ? $settings->seo_meta_description : 'Platform properti dengan sistem afiliasi terpercaya' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($settings && $settings->logo_path)
    <meta property="og:image" content="{{ Storage::url($settings->logo_path) }}">
    @endif

    <!-- Additional Meta Tags -->
    @stack('meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <!-- Google Analytics -->
    @if(config('services.google_analytics.id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.id') }}');
    </script>
    @endif
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <header>
        <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ ($settings && $settings->logo_url) ? $settings->logo_url : route('properties.index') }}" class="flex items-center">
                        @if($settings && $settings->logo_path)
                            <img src="{{ Storage::url($settings->logo_path) }}" alt="{{ $settings->seo_meta_title ?? 'PAMS' }}" class="h-8 sm:h-10 w-auto">
                        @else
                            <span class="text-xl sm:text-2xl font-bold text-blue-600">PAMS</span>
                        @endif
                    </a>
                </div>

                <!-- Navigation Links - Desktop -->
                <div class="hidden md:flex items-center space-x-4 lg:space-x-8">
                    <a href="{{ route('properties.index') }}" class="text-sm lg:text-base text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        Katalog Properti
                    </a>
                    @auth
                        <a href="{{ url('/admin') }}" class="text-sm lg:text-base text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm lg:text-base text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 lg:px-6 py-2 rounded-lg text-sm lg:text-base font-medium transition-colors">
                                Daftar Affiliate
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button 
                        type="button" 
                        id="mobile-menu-button"
                        class="text-gray-700 hover:text-blue-600 p-2 -mr-2 min-touch focus:outline-none focus:ring-2 focus:ring-blue-500 rounded" 
                        onclick="toggleMobileMenu()" 
                        aria-label="Toggle menu" 
                        aria-expanded="false" 
                        aria-controls="mobile-menu"
                    >
                        <svg id="menu-icon-open" class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="menu-icon-close" class="w-6 h-6 hidden transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden overflow-hidden transition-all duration-300 ease-in-out">
                <div class="flex flex-col space-y-2 pb-4 pt-2">
                    <a href="{{ route('properties.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors py-3 px-3 rounded hover:bg-gray-50 min-touch focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Katalog Properti
                    </a>
                    @auth
                        <a href="{{ url('/admin') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors py-3 px-3 rounded hover:bg-gray-50 min-touch focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors py-3 px-3 rounded hover:bg-gray-50 min-touch focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors text-center min-touch focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Daftar Affiliate
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
        </nav>
    </header>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12 sm:mt-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div>
                    @if($settings && $settings->logo_path)
                        <a href="{{ ($settings && $settings->logo_url) ? $settings->logo_url : route('properties.index') }}">
                            <img src="{{ Storage::url($settings->logo_path) }}" alt="{{ $settings->seo_meta_title ?? 'PAMS' }} - Logo" class="h-8 sm:h-10 w-auto mb-4">
                        </a>
                    @else
                        <a href="{{ ($settings && $settings->logo_url) ? $settings->logo_url : route('properties.index') }}">
                            <h2 class="text-lg sm:text-xl font-bold mb-4">PAMS</h2>
                        </a>
                    @endif
                    <p class="text-sm sm:text-base text-gray-400">
                        {{ ($settings && $settings->seo_meta_description) ? $settings->seo_meta_description : 'Property Affiliate Management System - Platform properti dengan sistem afiliasi terpercaya.' }}
                    </p>
                </div>
                <nav aria-label="Footer navigation">
                    <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Link Cepat</h2>
                    <ul class="space-y-2 text-sm sm:text-base text-gray-400">
                        <li><a href="{{ route('properties.index') }}" class="hover:text-white transition-colors inline-block py-1">Katalog Properti</a></li>
                        @auth
                            <li><a href="{{ url('/admin') }}" class="hover:text-white transition-colors inline-block py-1">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors inline-block py-1">Login</a></li>
                            @if (Route::has('register'))
                                <li><a href="{{ route('register') }}" class="hover:text-white transition-colors inline-block py-1">Daftar Affiliate</a></li>
                            @endif
                        @endauth
                    </ul>
                </nav>
                <address class="not-italic sm:col-span-2 lg:col-span-1">
                    <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Kontak</h2>
                    <p class="text-sm sm:text-base text-gray-400 space-y-1">
                        @if($settings && $settings->contact_email)
                            <span class="block">Email: <a href="mailto:{{ $settings->contact_email }}" class="hover:text-white transition-colors break-all">{{ $settings->contact_email }}</a></span>
                        @endif
                        @if($settings && $settings->contact_whatsapp)
                            <span class="block">WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_whatsapp) }}" class="hover:text-white transition-colors">{{ $settings->contact_whatsapp }}</a></span>
                        @endif
                    </p>
                </address>
            </div>
            <div class="border-t border-gray-800 mt-6 sm:mt-8 pt-6 sm:pt-8 text-center text-xs sm:text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} {{ ($settings && $settings->seo_meta_title) ? $settings->seo_meta_title : 'PAMS' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            const openIcon = document.getElementById('menu-icon-open');
            const closeIcon = document.getElementById('menu-icon-close');
            const isExpanded = button.getAttribute('aria-expanded') === 'true';
            
            // Toggle menu visibility
            menu.classList.toggle('hidden');
            
            // Toggle icons
            openIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
            
            // Update aria-expanded
            button.setAttribute('aria-expanded', !isExpanded);
            
            // Focus management
            if (!isExpanded) {
                // Menu is opening - focus first link
                const firstLink = menu.querySelector('a');
                if (firstLink) {
                    setTimeout(() => firstLink.focus(), 100);
                }
            }
        }
        
        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const menu = document.getElementById('mobile-menu');
                const button = document.getElementById('mobile-menu-button');
                
                if (!menu.classList.contains('hidden')) {
                    toggleMobileMenu();
                    button.focus();
                }
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            
            if (!menu.classList.contains('hidden') && 
                !menu.contains(event.target) && 
                !button.contains(event.target)) {
                toggleMobileMenu();
            }
        });
    </script>
</body>
</html>
