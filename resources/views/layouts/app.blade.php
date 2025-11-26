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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <header class="fixed top-0 left-0 w-full z-50 transition-all duration-300" id="main-header">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ ($settings && $settings->logo_url) ? $settings->logo_url : route('properties.index') }}" class="flex items-center gap-2 group">
                        @if($settings && $settings->logo_path)
                            <img src="{{ Storage::url($settings->logo_path) }}" alt="{{ $settings->seo_meta_title ?? 'PAMS' }}" class="h-20 w-auto brightness-0 invert">
                        @else
                            <div class="bg-blue-600 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-white tracking-tight">MND<span class="text-blue-400">Properti</span></span>
                        @endif
                    </a>
                </div>

                <!-- Navigation Links - Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('properties.index') }}" class="text-gray-300 hover:text-white font-medium transition-colors text-sm uppercase tracking-wider">Beranda</a>
                    <a href="{{ route('properties.index') }}" class="text-white font-medium transition-colors text-sm uppercase tracking-wider">Katalog</a>
                    <a href="{{ route('about-us') }}" class="text-gray-300 hover:text-white font-medium transition-colors text-sm uppercase tracking-wider">Tentang Kami</a>                    
                    <a href="#" class="text-gray-300 hover:text-white font-medium transition-colors text-sm uppercase tracking-wider">Kontak</a>
                </div>

                <!-- Right Side Actions -->
                <div class="hidden md:flex items-center space-x-6">
                    @auth
                        <a href="{{ url('/admin') }}" class="text-white font-medium hover:text-blue-400 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-white font-medium hover:text-blue-400 transition-colors">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-full font-medium transition-all transform hover:scale-105 shadow-lg shadow-blue-600/30">
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
                        class="text-white hover:text-blue-400 p-2 transition-colors focus:outline-none" 
                        onclick="toggleMobileMenu()" 
                    >
                        <svg id="menu-icon-open" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="menu-icon-close" class="w-8 h-8 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-gray-900/95 backdrop-blur-xl absolute top-24 left-0 w-full border-t border-gray-800 shadow-2xl">
                <div class="flex flex-col p-6 space-y-4">
                    <a href="{{ route('properties.index') }}" class="text-gray-300 hover:text-white font-medium text-lg">Beranda</a>
                    <a href="{{ route('properties.index') }}" class="text-white font-medium text-lg">Katalog</a>
                    <a href="#" class="text-gray-300 hover:text-white font-medium text-lg">Tentang Kami</a>
                    <a href="#" class="text-gray-300 hover:text-white font-medium text-lg">Blog</a>
                    <a href="#" class="text-gray-300 hover:text-white font-medium text-lg">Kontak</a>
                    <div class="h-px bg-gray-800 my-2"></div>
                    @auth
                        <a href="{{ url('/admin') }}" class="text-white font-medium text-lg">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-white font-medium text-lg">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-center shadow-lg">
                                Daftar Affiliate
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-20 pb-10">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-white tracking-tight">MND<span class="text-blue-400">Properti</span></span>
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        Platform properti terpercaya dengan ribuan pilihan hunian eksklusif untuk masa depan Anda. Temukan rumah impian Anda bersama kami.
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h3 class="text-lg font-bold mb-6">Menu</h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Beranda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Katalog Properti</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Kontak</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-lg font-bold mb-6">Legal</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('legal.terms') }}" class="text-gray-400 hover:text-blue-400 transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('legal.privacy') }}" class="text-gray-400 hover:text-blue-400 transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('legal.disclaimer') }}" class="text-gray-400 hover:text-blue-400 transition-colors">Disclaimer</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-bold mb-6">Hubungi Kami</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-gray-400">Jl. Jendral Sudirman No. 123, Jakarta Selatan, Indonesia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:info@mndproperti.com" class="text-gray-400 hover:text-blue-400 transition-colors">info@mndproperti.com</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-gray-400">+62 812 3456 7890</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} MND Properti. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <span class="sr-only">Facebook</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <span class="sr-only">Instagram</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const openIcon = document.getElementById('menu-icon-open');
            const closeIcon = document.getElementById('menu-icon-close');
            
            menu.classList.toggle('hidden');
            openIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 50) {
                header.classList.add('bg-gray-900/95', 'backdrop-blur-md', 'shadow-lg');
            } else {
                header.classList.remove('bg-gray-900/95', 'backdrop-blur-md', 'shadow-lg');
            }
        });
    </script>
</body>
</html>
