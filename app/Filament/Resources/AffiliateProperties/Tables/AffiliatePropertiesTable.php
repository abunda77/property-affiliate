<?php

namespace App\Filament\Resources\AffiliateProperties\Tables;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AffiliatePropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Property::query()
                    ->where('status', PropertyStatus::PUBLISHED)
                    ->with('media') // Eager load media to prevent N+1 queries
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->getStateUsing(function (Property $record) {
                        return $record->getFirstMediaUrl('images', 'thumb');
                    })
                    ->defaultImageUrl('/images/placeholder.jpg')
                    ->circular()
                    ->size(60),

                TextColumn::make('title')
                    ->label('Property Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 30) {
                            return $state;
                        }

                        return null;
                    }),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PropertyStatus $state): string => match ($state) {
                        PropertyStatus::PUBLISHED => 'success',
                        PropertyStatus::DRAFT => 'warning',
                        PropertyStatus::SOLD => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->label('Location')
                    ->options(function () {
                        return Property::published()
                            ->pluck('location', 'location')
                            ->unique()
                            ->sort()
                            ->toArray();
                    })
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('copy_link')
                    ->label('Copy Link')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('primary')
                    ->action(function (Property $record, $livewire) {
                        $user = Auth::user();
                        $affiliateCode = $user?->affiliate_code ?? '';
                        $url = route('property.show', ['slug' => $record->slug]).'?ref='.$affiliateCode;

                        // Dispatch Livewire event to copy to clipboard
                        $livewire->dispatch('copy-to-clipboard', text: $url);
                    }),

                Action::make('download_promo')
                    ->label('Download Promo')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Property $record): string => route('affiliate.download-promo', ['property' => $record->id]))
                    ->openUrlInNewTab(false),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No Published Properties')
            ->emptyStateDescription('There are no published properties available to generate links.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}
