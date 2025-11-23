<?php

namespace App\Services;

use App\Models\Property;
use App\Settings\GeneralSettings;
use Illuminate\Support\Str;

class SeoService
{
    protected GeneralSettings $settings;

    public function __construct(GeneralSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Generate meta tags for a property.
     *
     * @param Property $property
     * @return array
     */
    public function generateMetaTags(Property $property): array
    {
        return [
            'title' => $this->generateTitle($property),
            'description' => $this->generateDescription($property),
            'keywords' => $this->generateKeywords($property),
            'og:title' => $property->title,
            'og:description' => $this->generateDescription($property),
            'og:type' => 'product',
            'og:url' => route('property.show', $property->slug),
            'og:image' => $property->getFirstMediaUrl('images', 'large') ?: asset('images/placeholder.jpg'),
            'og:site_name' => $this->settings->seo_meta_title ?? config('app.name', 'PAMS'),
        ];
    }

    /**
     * Generate structured data (JSON-LD) for a property.
     *
     * @param Property $property
     * @return array
     */
    public function generateStructuredData(Property $property): array
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => $property->title,
            'description' => Str::limit(strip_tags($property->description), 200),
            'url' => route('property.show', $property->slug),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $property->location,
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $property->price,
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
            ],
        ];

        // Add image if available
        if ($property->getFirstMediaUrl('images')) {
            $structuredData['image'] = $property->getFirstMediaUrl('images', 'large');
        }

        return $structuredData;
    }

    /**
     * Generate page title for a property.
     *
     * @param Property $property
     * @return string
     */
    private function generateTitle(Property $property): string
    {
        $siteName = $this->settings->seo_meta_title ?? config('app.name', 'PAMS');
        return "{$property->title} - {$property->location} | {$siteName}";
    }

    /**
     * Generate meta description for a property.
     *
     * @param Property $property
     * @return string
     */
    private function generateDescription(Property $property): string
    {
        $description = strip_tags($property->description);
        return Str::limit($description, 160);
    }

    /**
     * Generate keywords for a property.
     *
     * @param Property $property
     * @return string
     */
    private function generateKeywords(Property $property): string
    {
        $keywords = [];

        // Add location
        $keywords[] = $property->location;

        // Add features
        if ($property->features && is_array($property->features)) {
            $keywords = array_merge($keywords, array_slice($property->features, 0, 5));
        }

        // Add property type from title
        $keywords[] = 'properti';
        $keywords[] = 'real estate';

        return implode(', ', array_unique($keywords));
    }
}
