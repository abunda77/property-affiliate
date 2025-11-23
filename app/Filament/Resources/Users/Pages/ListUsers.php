<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserStatus;
use App\Filament\Resources\Users\UserResource;
use App\Notifications\AffiliateApprovedNotification;
use Filament\Actions;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getToolbarActions(): array
    {
        return [
            Actions\BulkActionGroup::make([
                BulkAction::make('approve')
                ->label('Approve Selected Users')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Selected Users')
                ->modalDescription('Are you sure you want to approve the selected users? They will receive an email with their affiliate code.')
                ->modalSubmitActionLabel('Yes, Approve')
                ->action(function (Collection $records) {
                    $approvedCount = 0;
                    
                    foreach ($records as $user) {
                        if ($user->status === UserStatus::PENDING) {
                            // Approve user and generate affiliate code
                            $user->approve();
                            
                            // Assign Affiliate role
                            $user->assignRole('affiliate');
                            
                            // Send welcome email with affiliate code
                            $user->notify(new AffiliateApprovedNotification($user));
                            
                            $approvedCount++;
                        }
                    }
                    
                    Notification::make()
                        ->title('Users Approved')
                        ->body("{$approvedCount} user(s) have been approved and notified.")
                        ->success()
                        ->send();
                })
                ->deselectRecordsAfterCompletion(),
            ]),
        ];
    }
}
