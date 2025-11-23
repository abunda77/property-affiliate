<?php

namespace App\Filament\Resources\AffiliateProperties\Pages;

use App\Filament\Resources\AffiliateProperties\AffiliatePropertyResource;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateProperties extends ListRecords
{
    protected static string $resource = AffiliatePropertyResource::class;

    protected static ?string $title = 'Link Generator';

    public function getHeading(): string
    {
        return 'Link Generator';
    }

    public function getSubheading(): ?string
    {
        return 'Generate unique tracking links for properties to share with potential buyers';
    }
}
