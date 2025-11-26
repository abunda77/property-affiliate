<?php

namespace App\Livewire;

use App\Models\Property;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
class PropertyCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $location = '';
    public ?int $minPrice = null;
    public ?int $maxPrice = null;
    public string $sortBy = 'newest';

    protected $queryString = [
        'search' => ['except' => ''],
        'location' => ['except' => ''],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'sortBy' => ['except' => 'newest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLocation()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'location', 'minPrice', 'maxPrice', 'sortBy']);
        $this->resetPage();
    }

    public function render()
    {
        // Start with published properties and eager load media to prevent N+1
        $query = Property::published()->with('media');

        // Apply search using Laravel Scout
        if (!empty($this->search)) {
            // Use Scout search and get IDs
            $searchResults = Property::search($this->search)
                ->query(fn ($builder) => $builder->where('status', 'published'))
                ->get()
                ->pluck('id');
            
            // If search returns results, filter by those IDs
            if ($searchResults->isNotEmpty()) {
                $query->whereIn('id', $searchResults);
            } else {
                // No results found, return empty collection
                $query->whereRaw('1 = 0');
            }
        }

        // Apply location filter
        if (!empty($this->location)) {
            $query->where('location', 'like', '%' . $this->location . '%');
        }

        // Apply price range filter
        if ($this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'lowest_price':
                $query->orderBy('price', 'asc');
                break;
            case 'highest_price':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $properties = $query->paginate(12);

        return view('livewire.property-catalog', [
            'properties' => $properties,
            'searchTerm' => $this->search,
        ]);
    }
    
    /**
     * Highlight search terms in text
     */
    public function highlightSearchTerm(string $text, string $searchTerm): string
    {
        if (empty($searchTerm)) {
            return $text;
        }
        
        // Escape special regex characters in search term
        $pattern = preg_quote($searchTerm, '/');
        
        // Replace matches with highlighted version (case-insensitive)
        return preg_replace(
            '/(' . $pattern . ')/i',
            '<mark class="bg-yellow-200 px-1 rounded">$1</mark>',
            $text
        );
    }

    /**
     * Placeholder for lazy loading
     */
    public function placeholder()
    {
        return <<<'HTML'
        <div class="min-h-screen bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
                <div class="mb-6 sm:mb-8">
                    <div class="h-8 bg-gray-200 rounded w-64 mb-2 animate-pulse"></div>
                    <div class="h-4 bg-gray-200 rounded w-48 animate-pulse"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    @for($i = 0; $i < 8; $i++)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="h-48 bg-gray-200 animate-pulse"></div>
                        <div class="p-4">
                            <div class="h-4 bg-gray-200 rounded mb-2 animate-pulse"></div>
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2 animate-pulse"></div>
                            <div class="h-6 bg-gray-200 rounded w-1/2 animate-pulse"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
        HTML;
    }
}
