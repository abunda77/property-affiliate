<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\PromotionalPackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliatePromoController extends Controller
{
    public function __construct(
        private PromotionalPackageService $promoService
    ) {}

    /**
     * Download promotional materials for a property
     */
    public function download(Request $request, Property $property)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure user is authenticated and has affiliate role
        if (! $user || ! $user->roles()->whereIn('name', ['affiliate', 'super_admin'])->exists()) {
            abort(403, 'Unauthorized');
        }

        // Ensure property is published
        if ($property->status->value !== 'published') {
            abort(404, 'Property not found');
        }

        try {
            // Generate promotional package
            $zipPath = $this->promoService->generatePackage($property, $user);

            // Stream the ZIP file to browser and delete after sending
            return response()->download($zipPath, basename($zipPath), [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            abort(500, 'Failed to generate promotional package: '.$e->getMessage());
        }
    }
}
