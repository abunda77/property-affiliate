<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopAffiliatesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'month';

    public function getHeading(): ?string
    {
        return 'Top Performing Affiliates';
    }

    protected function getTableFilters(): array
    {
        return [
            // Remove filters temporarily to fix the table() error
        ];
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        
        // Only show for Super Admin
        if (!$user || !DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', 'super_admin')
            ->exists()) {
            return $table
                ->query(fn () => \App\Models\User::query()->whereRaw('1 = 0'))
                ->columns([]);
        }

        [$startDate, $endDate] = $this->getDateRange();
        
        $analyticsService = app(AnalyticsService::class);
        $metrics = $analyticsService->getGlobalMetrics($startDate, $endDate);
        $topAffiliates = $metrics['top_affiliates'];

        // Convert collection to query builder for Filament table
        $affiliateIds = $topAffiliates->pluck('id')->toArray();
        
        return $table
            ->query(
                \App\Models\User::query()
                    ->whereIn('id', $affiliateIds)
                    ->orderByRaw('FIELD(id, ' . implode(',', $affiliateIds ?: [0]) . ')')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Affiliate Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('affiliate_code')
                    ->label('Code')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('leads_count')
                    ->label('Leads')
                    ->getStateUsing(function ($record) use ($topAffiliates) {
                        $affiliate = $topAffiliates->firstWhere('id', $record->id);
                        return $affiliate ? $affiliate['leads_count'] : 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('visits_count')
                    ->label('Visits')
                    ->getStateUsing(function ($record) use ($topAffiliates) {
                        $affiliate = $topAffiliates->firstWhere('id', $record->id);
                        return $affiliate ? $affiliate['visits_count'] : 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('Conversion')
                    ->getStateUsing(function ($record) use ($topAffiliates) {
                        $affiliate = $topAffiliates->firstWhere('id', $record->id);
                        return $affiliate ? $affiliate['conversion_rate'] . '%' : '0%';
                    })
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
            ])
            ->paginated(false);
    }

    protected function getDateRange(): array
    {
        return match ($this->filter) {
            'today' => [Carbon::today(), Carbon::now()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
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
