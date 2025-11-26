<?php

namespace App\Filament\Resources\Properties\Tables;

use App\Enums\PropertyStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->label('Image')
                    ->circular()
                    ->limit(1),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('listing_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'info',
                        'rent' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => $state === 'sale' ? 'Dijual' : 'Disewakan'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => PropertyStatus::DRAFT->value,
                        'success' => PropertyStatus::PUBLISHED->value,
                        'danger' => PropertyStatus::SOLD->value,
                    ])
                    ->formatStateUsing(fn (PropertyStatus $state): string => ucfirst($state->value)),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        PropertyStatus::DRAFT->value => 'Draft',
                        PropertyStatus::PUBLISHED->value => 'Published',
                        PropertyStatus::SOLD->value => 'Sold',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                // Eager load media to prevent N+1 queries
                $query->with('media');
            });
    }
}
