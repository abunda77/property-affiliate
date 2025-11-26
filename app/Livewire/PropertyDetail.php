<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\User;
use App\Services\SeoService;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class PropertyDetail extends Component
{
    public Property $property;
    public array $seoMetaTags = [];
    public array $structuredData = [];
    public ?User $affiliate = null;
    public ?User $superAdmin = null;

    public function mount(string $slug, SeoService $seoService)
    {
        $this->property = Property::published()
            ->where('slug', $slug)
            ->with('media')
            ->firstOrFail();

        // Generate SEO meta tags
        $this->seoMetaTags = $seoService->generateMetaTags($this->property);
        $this->structuredData = $seoService->generateStructuredData($this->property);

        // Get affiliate from cookie if exists
        $affiliateId = Cookie::get('affiliate_id');
        if ($affiliateId) {
            $this->affiliate = User::find($affiliateId);
        }

        // Get super admin for fallback contact
        $this->superAdmin = User::role('super_admin')->first();
    }

    public function render()
    {
        return view('livewire.property-detail');
    }
}
