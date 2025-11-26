<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('User Information')
                    ->schema([
                        \Filament\Infolists\Components\ImageEntry::make('profile_photo')
                            ->circular()
                            ->label('Photo')
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                        \Filament\Infolists\Components\TextEntry::make('name'),
                        \Filament\Infolists\Components\TextEntry::make('email'),
                        \Filament\Infolists\Components\TextEntry::make('whatsapp'),
                        \Filament\Infolists\Components\TextEntry::make('roles.name')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn ($state) => ucfirst($state))
                            ->separator(','),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (\App\Enums\UserStatus $state): string => match ($state) {
                                \App\Enums\UserStatus::PENDING => 'warning',
                                \App\Enums\UserStatus::ACTIVE => 'success',
                                \App\Enums\UserStatus::BLOCKED => 'danger',
                            }),
                        \Filament\Infolists\Components\TextEntry::make('affiliate_code')
                            ->placeholder('Not assigned'),
                        \Filament\Infolists\Components\TextEntry::make('biodata')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
