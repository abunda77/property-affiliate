<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserStatus;
use App\Notifications\AffiliateApprovedNotification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo')
                    ->circular()
                    ->label('Photo')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email')
                    ->copyable(),

                TextColumn::make('whatsapp')
                    ->searchable()
                    ->label('WhatsApp')
                    ->copyable()
                    ->placeholder('Not provided'),

                TextColumn::make('roles.name')
                    ->badge()
                    ->color('info')
                    ->label('Roles')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->separator(','),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (UserStatus $state): string => match ($state) {
                        UserStatus::PENDING => 'warning',
                        UserStatus::ACTIVE => 'success',
                        UserStatus::BLOCKED => 'danger',
                    })
                    ->label('Status'),

                TextColumn::make('affiliate_code')
                    ->searchable()
                    ->label('Affiliate Code')
                    ->copyable()
                    ->placeholder('Not assigned'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Registered At')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        UserStatus::PENDING->value => 'Pending',
                        UserStatus::ACTIVE->value => 'Active',
                        UserStatus::BLOCKED->value => 'Blocked',
                    ])
                    ->label('Status'),
                
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Roles'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),
                
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve User')
                    ->modalDescription('Are you sure you want to approve this user? They will receive an email with their affiliate code.')
                    ->modalSubmitActionLabel('Yes, Approve')
                    ->visible(fn ($record) => $record->status === UserStatus::PENDING)
                    ->action(function ($record) {
                        // Approve user and generate affiliate code
                        $record->approve();
                        
                        // Assign Affiliate role if not already assigned
                        if (!$record->hasRole('affiliate')) {
                            $record->assignRole('affiliate');
                        }
                        
                        // Send welcome email with affiliate code
                        $record->notify(new AffiliateApprovedNotification($record));
                        
                        Notification::make()
                            ->title('User Approved')
                            ->body("User {$record->name} has been approved and notified.")
                            ->success()
                            ->send();
                    }),
                
                Action::make('block')
                    ->label('Block')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Block User')
                    ->modalDescription('Are you sure you want to block this user? They will not be able to log in.')
                    ->modalSubmitActionLabel('Yes, Block')
                    ->visible(fn ($record) => $record->status !== UserStatus::BLOCKED)
                    ->action(function ($record) {
                        $record->block();
                        
                        Notification::make()
                            ->title('User Blocked')
                            ->body("User {$record->name} has been blocked.")
                            ->success()
                            ->send();
                    }),
                
                Action::make('unblock')
                    ->label('Unblock')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Unblock User')
                    ->modalDescription('Are you sure you want to unblock this user? They will be able to log in again.')
                    ->modalSubmitActionLabel('Yes, Unblock')
                    ->visible(fn ($record) => $record->status === UserStatus::BLOCKED)
                    ->action(function ($record) {
                        $record->status = UserStatus::ACTIVE;
                        $record->save();
                        
                        Notification::make()
                            ->title('User Unblocked')
                            ->body("User {$record->name} has been unblocked.")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
