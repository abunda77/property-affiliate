<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ApiDocumentation extends Page
{
    protected string $view = 'filament.pages.api-documentation';

    protected static \UnitEnum|string|null $navigationGroup = 'Developer';

    protected static ?string $navigationLabel = 'API Documentation';

    protected static ?int $navigationSort = 99;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-code-bracket';
    }

    public static function canAccess(): bool
    {
        // Only allow super_admin or users with specific permission
        return auth()->user()?->hasRole('super_admin') || 
               auth()->user()?->can('view_api_documentation');
    }

    public function getTitle(): string
    {
        return 'API Documentation';
    }

    public function getHeading(): string
    {
        return 'API Documentation (Scramble)';
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('open_docs')
                ->label('Open Full Docs')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->url('/docs/api')
                ->openUrlInNewTab(),
        ];
    }
}
