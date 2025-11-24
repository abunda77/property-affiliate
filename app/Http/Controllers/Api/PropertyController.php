<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties.
     * 
     * Returns a paginated list of all active properties with their details.
     * Supports filtering by type, location, and price range.
     *
     * @param Request $request
     * @return JsonResponse Returns paginated property list
     * 
     * @queryParam page integer Page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (max 50). Example: 15
     * @queryParam type string Filter by property type (house, apartment, land, etc.). Example: house
     * @queryParam location string Filter by location/city. Example: Jakarta
     * @queryParam min_price integer Minimum price filter. Example: 500000000
     * @queryParam max_price integer Maximum price filter. Example: 2000000000
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Luxury Villa in Bali",
     *       "slug": "luxury-villa-in-bali",
     *       "type": "house",
     *       "price": 5000000000,
     *       "location": "Bali",
     *       "bedrooms": 4,
     *       "bathrooms": 3,
     *       "land_area": 500,
     *       "building_area": 300,
     *       "description": "Beautiful luxury villa...",
     *       "featured_image": "https://example.com/image.jpg",
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://example.com/api/properties?page=1",
     *     "last": "http://example.com/api/properties?page=10",
     *     "prev": null,
     *     "next": "http://example.com/api/properties?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 10,
     *     "per_page": 15,
     *     "to": 15,
     *     "total": 150
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Property::query()->where('is_active', true);

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $perPage = min($request->get('per_page', 15), 50);
        $properties = $query->latest()->paginate($perPage);

        return response()->json($properties);
    }

    /**
     * Display the specified property.
     * 
     * Returns detailed information about a specific property including
     * all images, amenities, and related data.
     *
     * @param string $slug The property slug
     * @return JsonResponse Returns property details
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Luxury Villa in Bali",
     *     "slug": "luxury-villa-in-bali",
     *     "type": "house",
     *     "price": 5000000000,
     *     "location": "Bali",
     *     "address": "Jl. Sunset Road No. 123",
     *     "bedrooms": 4,
     *     "bathrooms": 3,
     *     "land_area": 500,
     *     "building_area": 300,
     *     "description": "Beautiful luxury villa with ocean view...",
     *     "featured_image": "https://example.com/image.jpg",
     *     "images": [
     *       "https://example.com/image1.jpg",
     *       "https://example.com/image2.jpg"
     *     ],
     *     "amenities": ["Swimming Pool", "Garden", "Garage"],
     *     "certificate": "SHM",
     *     "year_built": 2020,
     *     "views_count": 1250,
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-15T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 404 {
     *   "message": "Property not found."
     * }
     */
    public function show(string $slug): JsonResponse
    {
        $property = Property::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Increment views count
        $property->increment('views_count');

        return response()->json([
            'data' => $property,
        ]);
    }

    /**
     * Get featured properties.
     * 
     * Returns a list of featured/highlighted properties for homepage display.
     *
     * @param Request $request
     * @return JsonResponse Returns featured properties
     * 
     * @queryParam limit integer Number of properties to return (max 20). Example: 6
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Luxury Villa in Bali",
     *       "slug": "luxury-villa-in-bali",
     *       "type": "house",
     *       "price": 5000000000,
     *       "location": "Bali",
     *       "featured_image": "https://example.com/image.jpg",
     *       "bedrooms": 4,
     *       "bathrooms": 3
     *     }
     *   ]
     * }
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 6), 20);

        $properties = Property::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $properties,
        ]);
    }

    /**
     * Track property click for affiliate.
     * 
     * Records a click event when an affiliate link is used to view a property.
     * This is used for tracking affiliate commissions.
     *
     * @param Request $request
     * @return JsonResponse Returns tracking confirmation
     * 
     * @bodyParam property_id integer required The ID of the property. Example: 1
     * @bodyParam affiliate_code string required The affiliate tracking code. Example: AFF123
     * @bodyParam source string The traffic source (optional). Example: facebook
     * 
     * @response 200 {
     *   "message": "Click tracked successfully.",
     *   "tracking_id": "abc123xyz"
     * }
     * 
     * @response 422 {
     *   "message": "Validation error.",
     *   "errors": {
     *     "property_id": ["The property id field is required."],
     *     "affiliate_code": ["The affiliate code field is required."]
     *   }
     * }
     */
    public function trackClick(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'affiliate_code' => 'required|exists:users,affiliate_code',
            'source' => 'nullable|string|max:50',
        ]);

        // Here you would implement your tracking logic
        // For example, create a record in affiliate_clicks table

        return response()->json([
            'message' => 'Click tracked successfully.',
            'tracking_id' => uniqid('track_'),
        ]);
    }
}
