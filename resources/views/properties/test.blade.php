<x-app-layout>
    <div class="min-h-screen bg-gray-50 pt-32 pb-16">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold mb-8">Properties Test (No Livewire)</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($properties as $property)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        @if ($property->getFirstMediaUrl('images'))
                            <img src="{{ $property->getFirstMediaUrl('images', 'thumb') }}" alt="{{ $property->title }}"
                                class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2">{{ $property->title }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ $property->location }}</p>
                            <p class="text-blue-600 font-bold">Rp {{ number_format($property->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-500">No properties found</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $properties->links() }}
            </div>

            <div class="mt-8">
                <a href="{{ route('properties.index') }}" class="text-blue-600 hover:underline">
                    ← Back to Livewire version
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
