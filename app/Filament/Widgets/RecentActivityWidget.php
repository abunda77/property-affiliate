<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'leads';

    public function getHeading(): ?string
    {
        return 'Recent Activity';
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        
        // Only show for Super Admin - using DB query instead of hasRole
        if (!$user || !DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', 'super_admin')
            ->exists()) {
            return $table
                ->query(fn () => \App\Models\Lead::query()->whereRaw('1 = 0'))
                ->columns([]);
        }

        if ($this->filter === 'leads') {
            return $this->getLeadsTable($table);
        } else {
            return $this->getPropertyViewsTable($table);
        }
    }

    protected function getLeadsTable(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Lead::query()
                    ->with(['property', 'affiliate'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d, H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Visitor')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->copyable()
                    ->copyMessage('WhatsApp number copied'),
                
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('affiliate.name')
                    ->label('Affiliate')
                    ->default('Direct')
                    ->badge()
                    ->color(fn ($record) => $record->affiliate ? 'success' : 'gray'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'new' => 'info',
                        'follow_up' => 'warning',
                        'survey' => 'primary',
                        'closed' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false);
    }

    protected function getPropertyViewsTable(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Visit::query()
                    ->with(['property', 'affiliate'])
                    ->whereNotNull('property_id')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d, H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('affiliate.name')
                    ->label('Affiliate')
                    ->searchable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('device')
                    ->label('Device')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mobile' => 'warning',
                        'desktop' => 'info',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('browser')
                    ->label('Browser')
                    ->limit(20),
            ])
            ->paginated(false);
    }

    protected function getTableFilters(): array
    {
        return [
            // Remove filters temporarily to fix the table() error
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', 'super_admin')
            ->exists();
    }
}
