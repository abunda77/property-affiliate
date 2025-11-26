<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Name'),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Email'),

                        TextInput::make('whatsapp')
                            ->tel()
                            ->maxLength(20)
                            ->label('WhatsApp Number')
                            ->placeholder('+62812345678'),

                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->label('Password')
                            ->helperText('Leave blank to keep current password'),

                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Roles')
                            ->helperText('Select one or more roles for this user')
                            ->options(Role::all()->pluck('name', 'id'))
                            ->required(),

                        Select::make('status')
                            ->options([
                                UserStatus::PENDING->value => 'Pending',
                                UserStatus::ACTIVE->value => 'Active',
                                UserStatus::BLOCKED->value => 'Blocked',
                            ])
                            ->default(UserStatus::PENDING->value)
                            ->required()
                            ->label('Status'),

                        FileUpload::make('profile_photo')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('profile-photos')
                            ->visibility('public')
                            ->label('Profile Photo')
                            ->imageEditor()
                            ->circleCropper(),

                        RichEditor::make('biodata')
                            ->label('Biodata')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Affiliate Information')
                    ->schema([
                        TextInput::make('affiliate_code')
                            ->maxLength(50)
                            ->label('Affiliate Code')
                            ->disabled()
                            ->helperText('Generated automatically upon approval'),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record && $record->affiliate_code),
            ]);
    }
}
