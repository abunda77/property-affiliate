<x-app-layout>
    @php
        try {
            $settings = app(\App\Settings\GeneralSettings::class);
        } catch (\Spatie\LaravelSettings\Exceptions\MissingSettings $e) {
            $settings = null;
        }
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 pt-32 pb-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="max-w-5xl mx-auto mb-12">
                <div class="text-center space-y-4">
                    <h1 class="text-4xl md:text-5xl font-bold text-white">
                        Hubungi Kami
                    </h1>
                    <p class="text-gray-400 text-lg">
                        Ada pertanyaan atau ingin berkonsultasi? Kami siap membantu Anda.
                    </p>
                </div>
            </div>

            <!-- Contact Form Component -->
            <div class="max-w-5xl mx-auto">
                <livewire:general-contact-form />
            </div>

            <!-- Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('properties.index') }}"
                    class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
