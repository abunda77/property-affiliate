<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Visitor')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor WhatsApp disalin!')
                    ->icon('heroicon-m-phone'),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Properti')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (LeadStatus $state): string => match ($state) {
                        LeadStatus::NEW => 'info',
                        LeadStatus::FOLLOW_UP => 'warning',
                        LeadStatus::SURVEY => 'primary',
                        LeadStatus::CLOSED => 'success',
                        LeadStatus::LOST => 'danger',
                    })
                    ->formatStateUsing(fn (LeadStatus $state): string => match ($state) {
                        LeadStatus::NEW => 'Baru',
                        LeadStatus::FOLLOW_UP => 'Follow Up',
                        LeadStatus::SURVEY => 'Survey',
                        LeadStatus::CLOSED => 'Closed',
                        LeadStatus::LOST => 'Lost',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'Baru',
                        'follow_up' => 'Follow Up',
                        'survey' => 'Survey',
                        'closed' => 'Closed',
                        'lost' => 'Lost',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('Click to WA')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn ($record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->whatsapp))
                    ->openUrlInNewTab(),

                Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-m-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(fn ($record) => self::getAvailableStatusTransitions($record->status))
                            ->required()
                            ->default(fn ($record) => $record->status->value)
                            ->helperText('Pilih status baru untuk lead ini'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->default(fn ($record) => $record->notes)
                            ->helperText('Tambahkan catatan tentang perubahan status atau percakapan dengan visitor'),
                    ])
                    ->action(function ($record, array $data) {
                        $oldStatus = $record->status;
                        $newStatus = LeadStatus::from($data['status']);
                        
                        // Validate status transition
                        if (!self::isValidStatusTransition($oldStatus, $newStatus)) {
                            Notification::make()
                                ->title('Transisi status tidak valid')
                                ->body("Tidak dapat mengubah status dari {$oldStatus->value} ke {$newStatus->value}")
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        // Update lead
                        $record->update([
                            'status' => $newStatus,
                            'notes' => $data['notes'],
                        ]);
                        
                        // Log status change
                        Log::info('Lead status updated', [
                            'lead_id' => $record->id,
                            'affiliate_id' => Auth::id(),
                            'old_status' => $oldStatus->value,
                            'new_status' => $newStatus->value,
                            'timestamp' => now()->toDateTimeString(),
                        ]);
                    })
                    ->successNotificationTitle('Status lead berhasil diupdate')
                    ->modalWidth('md'),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Filter to show only leads assigned to logged-in affiliate
                // Add eager loading to prevent N+1 queries
                $query->where('affiliate_id', Auth::id())
                    ->with(['property:id,title,slug']);
            });
    }

    /**
     * Get available status transitions based on current status
     */
    private static function getAvailableStatusTransitions(LeadStatus $currentStatus): array
    {
        $allStatuses = [
            'new' => 'Baru',
            'follow_up' => 'Follow Up',
            'survey' => 'Survey',
            'closed' => 'Closed',
            'lost' => 'Lost',
        ];

        // Define invalid transitions
        $invalidTransitions = [
            LeadStatus::CLOSED->value => ['new'], // Can't go from closed back to new
            LeadStatus::LOST->value => ['new'], // Can't go from lost back to new
        ];

        // Remove invalid transitions for current status
        if (isset($invalidTransitions[$currentStatus->value])) {
            foreach ($invalidTransitions[$currentStatus->value] as $invalid) {
                unset($allStatuses[$invalid]);
            }
        }

        return $allStatuses;
    }

    /**
     * Validate if status transition is allowed
     */
    private static function isValidStatusTransition(LeadStatus $oldStatus, LeadStatus $newStatus): bool
    {
        // Same status is always valid (no change)
        if ($oldStatus === $newStatus) {
            return true;
        }

        // Define invalid transitions
        $invalidTransitions = [
            LeadStatus::CLOSED->value => [LeadStatus::NEW->value],
            LeadStatus::LOST->value => [LeadStatus::NEW->value],
        ];

        // Check if transition is invalid
        if (isset($invalidTransitions[$oldStatus->value])) {
            if (in_array($newStatus->value, $invalidTransitions[$oldStatus->value])) {
                return false;
            }
        }

        return true;
    }
}
