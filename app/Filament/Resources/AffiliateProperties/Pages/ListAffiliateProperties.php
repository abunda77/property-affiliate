<?php

namespace App\Filament\Resources\AffiliateProperties\Pages;

use App\Filament\Resources\AffiliateProperties\AffiliatePropertyResource;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateProperties extends ListRecords
{
    protected static string $resource = AffiliatePropertyResource::class;

    protected static ?string $title = 'Link Generator';

    protected string $view = 'filament.resources.affiliate-properties.pages.list-affiliate-properties';

    public function getHeading(): string
    {
        return 'Link Generator';
    }

    public function getSubheading(): ?string
    {
        return 'Generate unique tracking links for properties to share with potential buyers';
    }

    public function mount(): void
    {
        parent::mount();
    }

    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'copyToClipboardScript' => true,
        ]);
    }

    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }
}
