<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

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
        // Start with published properties
        $query = Property::published();

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
}
