<?php

namespace App\Filament\Resources\Properties\Schemas;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Property Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Property::class, 'slug', ignoreRecord: true)
                            ->helperText('Auto-generated from title, but can be customized'),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->helperText('Enter price in Rupiah'),

                        TextInput::make('location')
                            ->required()
                            ->maxLength(255)
                            ->helperText('e.g., Jakarta Selatan, DKI Jakarta'),

                        Select::make('status')
                            ->required()
                            ->options([
                                PropertyStatus::DRAFT->value => 'Draft',
                                PropertyStatus::PUBLISHED->value => 'Published',
                                PropertyStatus::SOLD->value => 'Sold',
                            ])
                            ->default(PropertyStatus::DRAFT->value)
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Description')
                    ->schema([
                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'link',
                            ]),
                    ]),

                Section::make('Features')
                    ->schema([
                        Repeater::make('features')
                            ->schema([
                                TextInput::make('feature')
                                    ->label('Feature')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Feature')
                            ->columnSpanFull()
                            ->helperText('Add property features like "Swimming Pool", "24/7 Security", etc.')
                            ->reorderable()
                            ->collapsible(),
                    ]),

                Section::make('Specifications')
                    ->schema([
                        KeyValue::make('specs')
                            ->label('Specifications')
                            ->keyLabel('Specification Name')
                            ->valueLabel('Value')
                            ->addActionLabel('Add Specification')
                            ->reorderable()
                            ->helperText('Add specifications like "Land Size: 200m²", "Building Size: 150m²", etc.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Images')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('images')
                            ->collection('images')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->imageEditor()
                            ->maxFiles(20)
                            ->helperText('Upload property images. First image will be used as thumbnail.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
