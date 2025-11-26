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
    <!-- Hero Section with Gallery -->
    <div class="relative h-[70vh] bg-gray-900">
        @php
            $images = $property->getMedia('images');
        @endphp

        @if($images->count() > 0)
            <div x-data="{ 
                activeSlide: 0,
                slides: {{ $images->map(fn($img) => $img->getUrl('large'))->toJson() }},
                next() {
                    this.activeSlide = this.activeSlide === this.slides.length - 1 ? 0 : this.activeSlide + 1
                },
                prev() {
                    this.activeSlide = this.activeSlide === 0 ? this.slides.length - 1 : this.activeSlide - 1
                }
            }" class="relative w-full h-full group">
                <!-- Slides -->
                <template x-for="(slide, index) in slides" :key="index">
                    <div 
                        x-show="activeSlide === index"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 transform scale-105"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute inset-0"
                    >
                        <img :src="slide" class="w-full h-full object-cover" alt="{{ $property->title }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
                    </div>
                </template>

                <!-- Navigation -->
                <button @click="prev" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 backdrop-blur-md hover:bg-white/20 text-white p-3 rounded-full transition-all opacity-0 group-hover:opacity-100 transform -translate-x-4 group-hover:translate-x-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button @click="next" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 backdrop-blur-md hover:bg-white/20 text-white p-3 rounded-full transition-all opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>

                <!-- Thumbnails -->
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex space-x-2 overflow-x-auto max-w-[90vw] p-2 rounded-xl bg-black/20 backdrop-blur-md">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button 
                            @click="activeSlide = index"
                            :class="{'ring-2 ring-blue-500 scale-110': activeSlide === index, 'opacity-70 hover:opacity-100': activeSlide !== index}"
                            class="relative w-16 h-12 rounded-lg overflow-hidden transition-all flex-shrink-0"
                        >
                            <img :src="slide" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
        @else
            <div class="w-full h-full flex items-center justify-center text-white/20">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        @endif

        <!-- Property Title Overlay -->
        <div class="absolute bottom-32 left-0 w-full">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <div class="flex items-center gap-2 text-blue-400 font-semibold mb-2">
                        <span class="{{ $property->listing_type == 'sale' ? 'bg-blue-600/20 border-blue-500/30' : 'bg-purple-600/20 border-purple-500/30' }} backdrop-blur-md px-3 py-1 rounded-full text-sm border">
                            {{ $property->listing_type == 'sale' ? 'Dijual' : 'Disewakan' }}
                        </span>
                        <span class="flex items-center text-gray-300 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            {{ $property->location }}
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight shadow-sm">
                        {{ $property->title }}
                    </h1>
                    <p class="text-3xl md:text-4xl font-bold text-blue-400">
                        {{ $property->formatted_price }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-10 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Overview Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($property->specs)
                        @foreach(array_slice((array)$property->specs, 0, 4) as $key => $value)
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center hover:shadow-md transition-shadow">
                                <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                <span class="block text-lg font-bold text-gray-900">{{ $value }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Deskripsi Properti</h2>
                    <div class="prose prose-lg max-w-none text-gray-600">
                        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($property->description) }}
                    </div>
                </div>

                <!-- Specifications -->
                @if($property->specs && count((array)$property->specs) > 0)
                    <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Spesifikasi</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                            @foreach($property->specs as $key => $value)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                    <span class="text-gray-900 font-semibold">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Features -->
                @if($property->features && count($property->features) > 0)
                    <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Fasilitas & Fitur</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($property->features as $feature)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 font-medium">{{ is_string($feature) ? $feature : $loop->index }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Photo Gallery Collection -->
                @php
                    $galleryImages = $property->getMedia('images');
                @endphp
                @if($galleryImages->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Galeri Foto</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4" x-data="{ showModal: false, activeImage: '' }">
                            @foreach($galleryImages as $image)
                                <div 
                                    class="relative aspect-square rounded-xl overflow-hidden cursor-pointer group"
                                    @click="showModal = true; activeImage = '{{ $image->getUrl('large') }}'"
                                >
                                    <img 
                                        src="{{ $image->getUrl('medium') }}" 
                                        alt="{{ $property->title }}" 
                                        class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110"
                                        loading="lazy"
                                    >
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform scale-75 group-hover:scale-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Lightbox Modal -->
                            <div 
                                x-show="showModal" 
                                style="display: none;"
                                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                            >
                                <div class="relative w-full max-w-6xl max-h-[90vh] flex items-center justify-center" @click.away="showModal = false">
                                    <img :src="activeImage" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
                                    <button 
                                        @click="showModal = false" 
                                        class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors"
                                    >
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Location -->
                <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Lokasi</h2>
                    <div class="aspect-video rounded-xl overflow-hidden shadow-inner">
                        <iframe 
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            style="border:0"
                            src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google_maps.api_key', '') }}&q={{ urlencode($property->location) }}"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Agent Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-24">
                    @if($affiliate)
                        <div class="flex items-center gap-4 mb-6">
                            @if($affiliate->profile_photo)
                                <img src="{{ Storage::url($affiliate->profile_photo) }}" class="w-16 h-16 rounded-full object-cover border-2 border-blue-100">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                    {{ substr($affiliate->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500">Listed by</p>
                                <h3 class="text-lg font-bold text-gray-900">{{ $affiliate->name }}</h3>
                                <div class="flex items-center text-yellow-400 text-sm">
                                    ★★★★★ <span class="text-gray-400 ml-1">(4.9)</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            {{-- @if($affiliate->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $affiliate->whatsapp) }}?text=Halo%20{{ urlencode($affiliate->name) }},%20saya%20tertarik%20dengan%20properti%20{{ urlencode($property->title) }}" 
                                   target="_blank"
                                   class="flex items-center justify-center w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-green-500/30">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    WhatsApp
                                </a>
                            @endif --}}
                            <div x-data="{ showBiodata: false }">
                                <button @click="showBiodata = true" class="group relative flex items-center justify-center w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-[1.02] hover:shadow-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-600/40 overflow-hidden">
                                    <!-- Animated background overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-in-out"></div>
                                    
                                    <!-- Icon with subtle animation -->
                                    <svg class="w-5 h-5 mr-2.5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    
                                    <!-- Text -->
                                    <span class="relative z-10 tracking-wide">Lihat Biodata Agent</span>
                                </button>

                                <!-- Biodata Modal -->
                                <div
                                    x-show="showBiodata"
                                    style="display: none;"
                                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                >
                                    <!-- Backdrop -->
                                    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showBiodata = false"></div>

                                    <!-- Modal Panel -->
                                    <div 
                                        class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl ring-1 ring-gray-200 overflow-hidden"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    >
                                        <!-- Header -->
                                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                                            <h3 class="text-lg font-bold text-gray-900">Biodata Agent</h3>
                                            <button @click="showBiodata = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="p-6 max-h-[70vh] overflow-y-auto">
                                            <div class="flex items-center gap-4 mb-6">
                                                @if($affiliate->profile_photo)
                                                    <img src="{{ Storage::url($affiliate->profile_photo) }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg">
                                                @else
                                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg border-4 border-white">
                                                        {{ substr($affiliate->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="text-xl font-bold text-gray-900">{{ $affiliate->name }}</h4>
                                                    <p class="text-blue-600 font-medium">{{ $affiliate->email }}</p>
                                                    <div class="flex items-center text-yellow-400 text-sm mt-1">
                                                        ★★★★★ <span class="text-gray-400 ml-1">(4.9)</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="prose prose-blue max-w-none text-gray-600">
                                                @if($affiliate->biodata)
                                                    {!! $affiliate->biodata !!}
                                                @else
                                                    <p class="text-gray-400 italic">Belum ada biodata.</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Footer -->
                                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                                            <button @click="showBiodata = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors shadow-sm">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                MP
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Listed by</p>
                                <h3 class="text-lg font-bold text-gray-900">MND Properti</h3>
                                <div class="flex items-center text-yellow-400 text-sm">
                                    ★★★★★ <span class="text-gray-400 ml-1">(5.0)</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mb-6">
                            @if($superAdmin && $superAdmin->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $superAdmin->whatsapp) }}?text=Halo%20{{ urlencode($superAdmin->name) }},%20saya%20tertarik%20dengan%20properti%20{{ urlencode($property->title) }}" 
                                   target="_blank"
                                   class="flex items-center justify-center w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-green-500/30">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="font-bold text-gray-900 mb-4">Kirim Pesan</h4>
                        @livewire('contact-form', ['property' => $property])
                    </div>
                </div>

                <!-- Share -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h4 class="font-bold text-gray-900 mb-4">Bagikan</h4>
                    <div class="flex gap-2">
                        <button onclick="navigator.clipboard.writeText(window.location.href)" class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-600 py-2 rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                        </button>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($property->title) }}" target="_blank" class="flex-1 bg-sky-50 hover:bg-sky-100 text-sky-500 py-2 rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
