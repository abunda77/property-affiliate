<?php

namespace App\Filament\Resources\AffiliateProperties\Tables;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AffiliatePropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Property::query()
                    ->where('status', PropertyStatus::PUBLISHED)
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
                    ->label('Copy Link Saya')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('primary')
                    ->requiresConfirmation(false)
                    ->action(function (Property $record) {
                        // Action is handled by JavaScript via extraAttributes
                    })
                    ->extraAttributes(function (Property $record) {
                        $user = Auth::user();
                        $affiliateCode = $user?->affiliate_code ?? '';
                        $url = route('property.show', ['slug' => $record->slug]) . '?ref=' . $affiliateCode;
                        
                        return [
                            'x-data' => '{ url: \'' . addslashes($url) . '\' }',
                            'x-on:click.prevent' => '
                                navigator.clipboard.writeText(url).then(() => {
                                    new FilamentNotification()
                                        .title(\'Link berhasil disalin!\')
                                        .success()
                                        .send();
                                }).catch(() => {
                                    new FilamentNotification()
                                        .title(\'Gagal menyalin link\')
                                        .danger()
                                        .send();
                                })
                            ',
                        ];
                    }),
                    
                Action::make('download_promo')
                    ->label('Download Materi Promosi')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->requiresConfirmation(false)
                    ->url(fn (Property $record): string => route('affiliate.download-promo', ['property' => $record->id]))
                    ->openUrlInNewTab(false),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No Published Properties')
            ->emptyStateDescription('There are no published properties available to generate links.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}
