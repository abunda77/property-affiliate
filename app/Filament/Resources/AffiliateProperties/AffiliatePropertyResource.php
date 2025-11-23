<?php

namespace App\Filament\Resources\AffiliateProperties;

use App\Filament\Resources\AffiliateProperties\Pages;
use App\Filament\Resources\AffiliateProperties\Tables\AffiliatePropertiesTable;
use App\Models\Property;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AffiliatePropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Link Generator';

    protected static ?string $modelLabel = 'Property Link';

    protected static ?string $pluralModelLabel = 'Property Links';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return AffiliatePropertiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateProperties::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        // Allow affiliates and super admins to view the link generator
        return optional(Auth::user())->roles()->whereIn('name', ['affiliate', 'super_admin'])->exists();
    }
}
