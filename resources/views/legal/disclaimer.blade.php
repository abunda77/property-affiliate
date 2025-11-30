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
            <div class="max-w-4xl mx-auto mb-12">
                <div class="text-center space-y-4">
                    <h1 class="text-4xl md:text-5xl font-bold text-white">
                        Disclaimer
                    </h1>
                    <p class="text-gray-400 text-lg">
                        Penyangkalan dan batasan tanggung jawab
                    </p>
                </div>
            </div>

            <!-- Content -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 md:p-12 shadow-2xl">
                    @if ($settings && $settings->disclaimer)
                        <div class="prose prose-invert prose-lg max-w-none">
                            <div class="text-gray-300 leading-relaxed space-y-6">
                                {!! $settings->disclaimer !!}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-400 mb-2">Konten Belum Tersedia</h3>
                            <p class="text-gray-500">Disclaimer sedang dalam proses penyusunan.</p>
                        </div>
                    @endif
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
    </div>

    <style>
        .prose-invert h1,
        .prose-invert h2,
        .prose-invert h3,
        .prose-invert h4 {
            color: #fff;
            font-weight: 700;
            margin-top: 1.5em;
            margin-bottom: 0.75em;
        }

        .prose-invert h1 {
            font-size: 2em;
        }

        .prose-invert h2 {
            font-size: 1.5em;
        }

        .prose-invert h3 {
            font-size: 1.25em;
        }

        .prose-invert ul,
        .prose-invert ol {
            padding-left: 1.5em;
            margin: 1em 0;
        }

        .prose-invert li {
            margin: 0.5em 0;
        }

        .prose-invert a {
            color: #60a5fa;
            text-decoration: underline;
        }

        .prose-invert a:hover {
            color: #93c5fd;
        }

        .prose-invert strong {
            color: #fff;
            font-weight: 600;
        }

        .prose-invert blockquote {
            border-left: 4px solid #60a5fa;
            padding-left: 1em;
            font-style: italic;
            color: #9ca3af;
        }

        .prose-invert code {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 0.2em 0.4em;
            border-radius: 0.25em;
            font-size: 0.9em;
        }
    </style>
</x-app-layout>
