<x-app-layout>
    <div id="livewire-check">
        @livewire('property-catalog')
    </div>

    @push('scripts')
        <script>
            // Check if Livewire loaded
            setTimeout(function() {
                if (typeof Livewire === 'undefined') {
                    console.error('Livewire not loaded!');
                    document.getElementById('livewire-check').innerHTML =
                        '<div class="min-h-screen flex items-center justify-center bg-gray-50">' +
                        '<div class="text-center p-8">' +
                        '<h2 class="text-2xl font-bold text-red-600 mb-4">Livewire Error</h2>' +
                        '<p class="text-gray-600 mb-4">Livewire failed to load. Please refresh the page.</p>' +
                        '<a href="/properties-test" class="bg-blue-600 text-white px-6 py-3 rounded-lg inline-block">View Without Livewire</a>' +
                        '</div>' +
                        '</div>';
                }
            }, 2000);
        </script>
    @endpush
</x-app-layout>
