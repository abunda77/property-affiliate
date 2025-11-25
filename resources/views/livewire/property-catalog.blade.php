<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative h-[600px] flex items-center justify-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img 
                src="https://images.unsplash.com/photo-1656646424620-feb44bd37437?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
                alt="Luxury Home" 
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-gray-900/60"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 text-center pt-20">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                Temukan Properti <br>
                <span class="text-blue-400">Impian Anda</span> Hari Ini
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl mx-auto">
                Jelajahi ribuan properti eksklusif dengan harga terbaik di lokasi strategis seluruh Indonesia.
            </p>

            <!-- Search Bar -->
            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl max-w-4xl mx-auto border border-white/20 shadow-2xl">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari judul, deskripsi..." 
                            class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white/20 transition-all"
                        >
                    </div>
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.500ms="location"
                            placeholder="Masukkan lokasi..." 
                            class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white/20 transition-all"
                        >
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-blue-600/30">
                        Cari
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-8 mt-12 max-w-3xl mx-auto border-t border-white/10 pt-8">
                <div>
                    <div class="text-3xl font-bold text-white mb-1">1.2k+</div>
                    <div class="text-sm text-gray-300 uppercase tracking-wider">Properti Terjual</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-white mb-1">850+</div>
                    <div class="text-sm text-gray-300 uppercase tracking-wider">Agen Terpercaya</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-white mb-1">50+</div>
                    <div class="text-sm text-gray-300 uppercase tracking-wider">Kota Dijangkau</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-72 flex-shrink-0 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-900">Filter Lanjutan</h2>
                        @if($search || $location || $minPrice || $maxPrice)
                            <button wire:click="clearFilters" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                Reset
                            </button>
                        @endif
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Rentang Harga
                        </label>
                        <div class="space-y-3">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="minPrice"
                                    placeholder="Minimum"
                                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                >
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="maxPrice"
                                    placeholder="Maksimum"
                                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label for="sortBy" class="block text-sm font-semibold text-gray-700 mb-3">
                            Urutkan
                        </label>
                        <div class="relative">
                            <select 
                                id="sortBy"
                                wire:model.live="sortBy"
                                class="w-full pl-4 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white transition-all"
                            >
                                <option value="newest">Terbaru</option>
                                <option value="lowest_price">Harga Terendah</option>
                                <option value="highest_price">Harga Tertinggi</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Property Grid -->
            <section class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Properti Unggulan</h2>
                    <span class="text-gray-500 text-sm">{{ $properties->total() }} properti ditemukan</span>
                </div>

                <div wire:loading class="w-full mb-8">
                    <div class="animate-pulse flex space-x-4">
                        <div class="flex-1 space-y-4 py-1">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="space-y-2">
                                <div class="h-4 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($properties->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($properties as $property)
                            <article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group border border-gray-100 overflow-hidden flex flex-col h-full">
                                <a href="{{ route('property.show', $property->slug) }}" wire:navigate class="flex flex-col h-full">
                                    <!-- Image -->
                                    <div class="relative aspect-[4/3] overflow-hidden">
                                        @if($property->getFirstMediaUrl('images', 'thumb'))
                                            <img 
                                                src="{{ $property->getFirstMediaUrl('images', 'medium') }}" 
                                                alt="{{ $property->title }}"
                                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Badge -->
                                        <div class="absolute top-4 left-4">
                                            <span class="bg-white/90 backdrop-blur-sm text-blue-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                                                Dijual
                                            </span>
                                        </div>
                                        
                                        <!-- Price Overlay -->
                                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/60 to-transparent p-4">
                                            <p class="text-white font-bold text-xl">{{ $property->formatted_price }}</p>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-5 flex-1 flex flex-col">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                            @if($searchTerm)
                                                {!! $this->highlightSearchTerm($property->title, $searchTerm) !!}
                                            @else
                                                {{ $property->title }}
                                            @endif
                                        </h3>
                                        
                                        <div class="flex items-center text-gray-500 text-sm mb-4">
                                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">
                                                @if($searchTerm)
                                                    {!! $this->highlightSearchTerm($property->location, $searchTerm) !!}
                                                @else
                                                    {{ $property->location }}
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Features -->
                                        @if($property->features && count($property->features) > 0)
                                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center gap-4 text-sm text-gray-600">
                                                @foreach(array_slice($property->features, 0, 3) as $feature)
                                                    <div class="flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span class="truncate max-w-[80px]">{{ is_string($feature) ? $feature : $loop->index }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12">
                        {{ $properties->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
                        <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada properti ditemukan</h3>
                        <p class="text-gray-500 mb-8 max-w-md mx-auto">Maaf, kami tidak dapat menemukan properti yang sesuai dengan kriteria pencarian Anda.</p>
                        @if($search || $location || $minPrice || $maxPrice)
                            <button wire:click="clearFilters" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-600/30">
                                Reset Filter
                            </button>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
