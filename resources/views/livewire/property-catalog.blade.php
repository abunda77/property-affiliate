<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Katalog Properti</h1>
            <p class="text-sm sm:text-base text-gray-600">Temukan properti impian Anda</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-64 xl:w-72 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 lg:sticky lg:top-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Filter</h2>
                        @if($search || $location || $minPrice || $maxPrice)
                            <button wire:click="clearFilters" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Reset
                            </button>
                        @endif
                    </div>

                    <!-- Search -->
                    <div class="mb-4 sm:mb-6">
                        <label for="search" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                            Cari Properti
                        </label>
                        <input 
                            type="text" 
                            id="search"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari judul, deskripsi..."
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Location Filter -->
                    <div class="mb-4 sm:mb-6">
                        <label for="location" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                            Lokasi
                        </label>
                        <input 
                            type="text" 
                            id="location"
                            wire:model.live.debounce.500ms="location"
                            placeholder="Masukkan lokasi..."
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                            Rentang Harga
                        </label>
                        <div class="space-y-2 sm:space-y-3">
                            <input 
                                type="number" 
                                wire:model.live.debounce.500ms="minPrice"
                                placeholder="Harga minimum"
                                class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <input 
                                type="number" 
                                wire:model.live.debounce.500ms="maxPrice"
                                placeholder="Harga maksimum"
                                class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label for="sortBy" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                            Urutkan
                        </label>
                        <select 
                            id="sortBy"
                            wire:model.live="sortBy"
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="newest">Terbaru</option>
                            <option value="lowest_price">Harga Terendah</option>
                            <option value="highest_price">Harga Tertinggi</option>
                        </select>
                    </div>
                </div>
            </aside>

            <!-- Property Grid -->
            <section class="flex-1 min-w-0">
                <div wire:loading class="mb-4" role="status" aria-live="polite">
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base">
                        Memuat properti...
                    </div>
                </div>

                @if($properties->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 sm:gap-6">
                        @foreach($properties as $property)
                            <article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                                <a href="{{ route('property.show', $property->slug) }}" wire:navigate aria-label="Lihat detail {{ $property->title }}">
                                    <!-- Property Image -->
                                    <figure class="relative h-40 sm:h-48 bg-gray-200">
                                        @if($property->getFirstMediaUrl('images', 'thumb'))
                                            <picture>
                                                <source 
                                                    type="image/webp"
                                                    srcset="{{ $property->getFirstMediaUrl('images', 'thumb') }} 300w,
                                                            {{ $property->getFirstMediaUrl('images', 'medium') }} 800w"
                                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                                                >
                                                <source 
                                                    type="image/jpeg"
                                                    srcset="{{ $property->getFirstMediaUrl('images', 'thumb-jpg') }} 300w,
                                                            {{ $property->getFirstMediaUrl('images', 'medium-jpg') }} 800w"
                                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                                                >
                                                <img 
                                                    src="{{ $property->getFirstMediaUrl('images', 'thumb') }}" 
                                                    alt="Foto properti {{ $property->title }} di {{ $property->location }}"
                                                    class="w-full h-full object-cover"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            </picture>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400" role="img" aria-label="Tidak ada foto tersedia">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </figure>

                                    <!-- Property Info -->
                                    <div class="p-3 sm:p-4">
                                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[3rem] sm:min-h-[3.5rem]">
                                            @if($searchTerm)
                                                {!! $this->highlightSearchTerm($property->title, $searchTerm) !!}
                                            @else
                                                {{ $property->title }}
                                            @endif
                                        </h2>
                                        
                                        <p class="flex items-center text-gray-600 text-xs sm:text-sm mb-2 sm:mb-3">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="line-clamp-1">
                                                @if($searchTerm)
                                                    {!! $this->highlightSearchTerm($property->location, $searchTerm) !!}
                                                @else
                                                    {{ $property->location }}
                                                @endif
                                            </span>
                                        </p>

                                        <p class="text-lg sm:text-xl font-bold text-blue-600">
                                            {{ $property->formatted_price }}
                                        </p>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Pagination" class="mt-6 sm:mt-8">
                        {{ $properties->links() }}
                    </nav>
                @else
                    <div class="bg-white rounded-lg shadow-sm p-6 sm:p-12 text-center">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Tidak ada properti ditemukan</h2>
                        <p class="text-sm sm:text-base text-gray-600 mb-4">Coba ubah filter pencarian Anda</p>
                        @if($search || $location || $minPrice || $maxPrice)
                            <button wire:click="clearFilters" class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Reset Filter
                            </button>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
