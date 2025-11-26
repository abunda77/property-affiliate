<?php

namespace App\Filament\Resources\Properties\Infolists;

use App\Enums\PropertyStatus;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PropertyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Group::make([
                    Section::make('Property Information')
                        ->schema([
                            SpatieMediaLibraryImageEntry::make('images')
                                ->collection('images')
                                ->label('Images')
                                ->columnSpanFull(),

                            TextEntry::make('title')
                                ->label('Title'),

                            TextEntry::make('slug')
                                ->label('Slug'),

                            TextEntry::make('price')
                                ->money('IDR')
                                ->label('Price'),

                            TextEntry::make('location')
                                ->label('Location'),

                            TextEntry::make('listing_type')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'sale' => 'info',
                                    'rent' => 'warning',
                                })
                                ->formatStateUsing(fn (string $state): string => $state === 'sale' ? 'Dijual' : 'Disewakan'),

                            TextEntry::make('status')
                                ->badge()
                                ->colors([
                                    'secondary' => PropertyStatus::DRAFT->value,
                                    'success' => PropertyStatus::PUBLISHED->value,
                                    'danger' => PropertyStatus::SOLD->value,
                                ])
                                ->formatStateUsing(fn (PropertyStatus $state): string => ucfirst($state->value)),
                        ])
                        ->columns(2),
                ])->columnSpan(2),

                Group::make([
                    Section::make('Description')
                        ->schema([
                            TextEntry::make('description')
                                ->html()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Details')
                        ->schema([
                            RepeatableEntry::make('features')
                                ->schema([
                                    TextEntry::make('feature'),
                                ])
                                ->grid(2)
                                ->columnSpanFull(),

                            KeyValueEntry::make('specs')
                                ->label('Specifications')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(2),
            ]);
    }
}
