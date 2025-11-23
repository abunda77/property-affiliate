@section('title', $seoMetaTags['title'] ?? $property->title)

@push('meta')
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $seoMetaTags['description'] ?? '' }}">
    <meta name="keywords" content="{{ $seoMetaTags['keywords'] ?? '' }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $seoMetaTags['og:title'] ?? $property->title }}">
    <meta property="og:description" content="{{ $seoMetaTags['og:description'] ?? '' }}">
    <meta property="og:type" content="{{ $seoMetaTags['og:type'] ?? 'product' }}">
    <meta property="og:url" content="{{ $seoMetaTags['og:url'] ?? url()->current() }}">
    <meta property="og:image" content="{{ $seoMetaTags['og:image'] ?? '' }}">
    <meta property="og:site_name" content="{{ $seoMetaTags['og:site_name'] ?? config('app.name') }}">
    
    <!-- Structured Data (JSON-LD) -->
    @if(!empty($structuredData))
    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endif
@endpush

<div class="min-h-screen bg-gray-50">
    <article class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <!-- Breadcrumb -->
        <nav aria-label="Breadcrumb" class="mb-4 sm:mb-6">
            <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600">
                <li>
                    <a href="{{ route('properties.index') }}" wire:navigate class="hover:text-blue-600">
                        Katalog
                    </a>
                </li>
                <li>
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
                <li class="text-gray-900 font-medium truncate">{{ $property->title }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 min-w-0">
                <!-- Photo Gallery -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4 sm:mb-6">
                    @php
                        $images = $property->getMedia('images');
                    @endphp

                    @if($images->count() > 0)
                        <figure x-data="{ 
                            currentIndex: 0,
                            images: {{ $images->map(fn($img) => [
                                'url' => $img->getUrl('large'),
                                'medium' => $img->getUrl('medium'),
                                'thumb' => $img->getUrl('thumb'),
                                'urlJpg' => $img->getUrl('large-jpg'),
                                'mediumJpg' => $img->getUrl('medium-jpg'),
                                'thumbJpg' => $img->getUrl('thumb-jpg')
                            ])->toJson() }},
                            touchStartX: 0,
                            touchEndX: 0,
                            handleTouchStart(e) {
                                this.touchStartX = e.touches[0].clientX;
                            },
                            handleTouchMove(e) {
                                this.touchEndX = e.touches[0].clientX;
                            },
                            handleTouchEnd() {
                                const swipeThreshold = 50;
                                const diff = this.touchStartX - this.touchEndX;
                                
                                if (Math.abs(diff) > swipeThreshold) {
                                    if (diff > 0) {
                                        // Swipe left - next image
                                        this.currentIndex = this.currentIndex < this.images.length - 1 ? this.currentIndex + 1 : 0;
                                    } else {
                                        // Swipe right - previous image
                                        this.currentIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.images.length - 1;
                                    }
                                }
                                
                                this.touchStartX = 0;
                                this.touchEndX = 0;
                            }
                        }">
                            <!-- Main Image -->
                            <div class="relative aspect-video bg-gray-200" 
                                @touchstart="handleTouchStart($event)"
                                @touchmove="handleTouchMove($event)"
                                @touchend="handleTouchEnd()"
                                style="touch-action: pan-y pinch-zoom;">
                                <template x-for="(image, index) in images" :key="index">
                                    <picture x-show="currentIndex === index" x-transition class="w-full h-full">
                                        <source 
                                            type="image/webp"
                                            :srcset="`${image.thumb} 300w, ${image.medium} 800w, ${image.url} 1920w`"
                                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 66vw"
                                        >
                                        <source 
                                            type="image/jpeg"
                                            :srcset="`${image.thumbJpg} 300w, ${image.mediumJpg} 800w, ${image.urlJpg} 1920w`"
                                            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 90vw, 66vw"
                                        >
                                        <img 
                                            :src="image.url" 
                                            :alt="'{{ $property->title }} - Foto properti ' + (index + 1) + ' di {{ $property->location }}'"
                                            class="w-full h-full object-cover"
                                            :loading="index === 0 ? 'eager' : 'lazy'"
                                            decoding="async"
                                        >
                                    </picture>
                                </template>

                                <!-- Navigation Arrows -->
                                <template x-if="images.length > 1">
                                    <div>
                                        <button 
                                            @click="currentIndex = currentIndex > 0 ? currentIndex - 1 : images.length - 1"
                                            class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white p-2 sm:p-3 rounded-full shadow-lg transition-colors min-touch"
                                            aria-label="Foto sebelumnya"
                                        >
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <button 
                                            @click="currentIndex = currentIndex < images.length - 1 ? currentIndex + 1 : 0"
                                            class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white p-2 sm:p-3 rounded-full shadow-lg transition-colors min-touch"
                                            aria-label="Foto berikutnya"
                                        >
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <!-- Image Counter -->
                                <div class="absolute bottom-2 sm:bottom-4 right-2 sm:right-4 bg-black/70 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm" role="status" aria-live="polite">
                                    <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                                </div>
                            </div>

                            <!-- Thumbnail Strip -->
                            <template x-if="images.length > 1">
                                <figcaption class="p-3 sm:p-4 bg-gray-50">
                                    <div class="flex gap-2 overflow-x-auto scrollbar-hide" role="group" aria-label="Thumbnail galeri foto" style="scroll-snap-type: x mandatory;">
                                        <template x-for="(image, index) in images" :key="index">
                                            <button 
                                                @click="currentIndex = index"
                                                :class="currentIndex === index ? 'ring-2 ring-blue-600' : 'ring-1 ring-gray-300'"
                                                class="flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden transition-all min-touch"
                                                :aria-label="'Lihat foto ' + (index + 1)"
                                                :aria-pressed="currentIndex === index ? 'true' : 'false'"
                                                style="scroll-snap-align: start;"
                                            >
                                                <img 
                                                    :src="image.thumb" 
                                                    :alt="'{{ $property->title }} - Thumbnail ' + (index + 1)"
                                                    class="w-full h-full object-cover"
                                                >
                                            </button>
                                        </template>
                                    </div>
                                </figcaption>
                            </template>
                        </figure>
                    @else
                        <figure class="aspect-video bg-gray-200 flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <figcaption class="sr-only">Tidak ada foto tersedia untuk properti ini</figcaption>
                        </figure>
                    @endif
                </div>

                <!-- Property Description -->
                <section class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Deskripsi</h2>
                    <div class="prose prose-sm sm:prose max-w-none text-gray-700 text-sm sm:text-base">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </section>

                <!-- Features -->
                @if($property->features && count($property->features) > 0)
                    <section class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Fitur</h2>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                            @foreach($property->features as $feature)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm sm:text-base text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <!-- Specifications -->
                @if($property->specs && count((array)$property->specs) > 0)
                    <section class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Spesifikasi</h2>
                        <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <table class="w-full min-w-full">
                                <caption class="sr-only">Spesifikasi detail properti {{ $property->title }}</caption>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($property->specs as $key => $value)
                                        <tr>
                                            <th scope="row" class="py-2 sm:py-3 pr-3 sm:pr-4 pl-4 sm:pl-0 text-xs sm:text-sm font-medium text-gray-900 w-2/5 sm:w-1/3 text-left">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </th>
                                            <td class="py-2 sm:py-3 pr-4 sm:pr-0 text-xs sm:text-sm text-gray-700">
                                                {{ $value }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif

                <!-- Google Maps -->
                <section class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Lokasi</h2>
                    <figure class="aspect-video bg-gray-200 rounded-lg overflow-hidden">
                        <iframe 
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            style="border:0"
                            src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google_maps.api_key', '') }}&q={{ urlencode($property->location) }}"
                            allowfullscreen
                            title="Peta lokasi {{ $property->location }}"
                        ></iframe>
                    </figure>
                    <address class="mt-3 sm:mt-4 flex items-start text-sm sm:text-base text-gray-700 not-italic">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 mt-0.5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $property->location }}</span>
                    </address>
                </section>
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-1">
                <!-- Price Card -->
                <section class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-6 lg:sticky lg:top-4">
                    <header class="mb-4 sm:mb-6">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ $property->title }}</h1>
                        <p class="text-2xl sm:text-3xl font-bold text-blue-600">
                            {{ $property->formatted_price }}
                        </p>
                    </header>

                    <!-- Contact Form Section -->
                    <div class="border-t pt-4 sm:pt-6">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Tertarik dengan properti ini?</h2>
                        <p class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4">
                            Hubungi kami untuk informasi lebih lanjut atau jadwalkan kunjungan.
                        </p>
                        
                        @livewire('contact-form', ['property' => $property])
                    </div>
                </section>

                <!-- Share Section -->
                <section class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Bagikan Properti</h2>
                    <div class="flex gap-2" role="group" aria-label="Opsi berbagi">
                        <button 
                            onclick="navigator.share ? navigator.share({title: '{{ $property->title }}', url: window.location.href}) : alert('Share not supported')"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-medium py-3 px-3 sm:px-4 rounded-lg transition-colors text-xs sm:text-sm min-touch"
                            aria-label="Bagikan properti"
                        >
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </button>
                        <button 
                            onclick="navigator.clipboard.writeText(window.location.href).then(() => alert('Link disalin!'))"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-medium py-3 px-3 sm:px-4 rounded-lg transition-colors text-xs sm:text-sm min-touch"
                            aria-label="Salin link properti"
                        >
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </section>
            </aside>
        </div>

        <!-- Affiliate Information Footer -->
        @if($affiliate)
            <footer class="mt-8 sm:mt-12 border-t pt-6 sm:pt-8">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                        <!-- Affiliate Photo -->
                        <div class="flex-shrink-0">
                            @if($affiliate->profile_photo)
                                <img 
                                    src="{{ Storage::url($affiliate->profile_photo) }}" 
                                    alt="Foto profil {{ $affiliate->name }}"
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-white shadow-lg"
                                >
                            @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center border-4 border-white shadow-lg">
                                    <span class="text-xl sm:text-2xl font-bold text-white">
                                        {{ strtoupper(substr($affiliate->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Affiliate Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm text-gray-600 mb-1">Properti ini dipromosikan oleh:</p>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1 sm:mb-2">{{ $affiliate->name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600">
                                Agen properti terpercaya yang siap membantu Anda menemukan properti impian.
                            </p>
                        </div>

                        <!-- Contact Button -->
                        @if($affiliate->whatsapp)
                            <div class="flex-shrink-0 w-full sm:w-auto">
                                <a 
                                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $affiliate->whatsapp) }}?text=Halo%20{{ urlencode($affiliate->name) }},%20saya%20tertarik%20dengan%20properti%20{{ urlencode($property->title) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl w-full sm:w-auto text-sm sm:text-base"
                                    aria-label="Hubungi {{ $affiliate->name }} via WhatsApp"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span>Hubungi Agen</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </footer>
        @endif
    </article>
</div>
