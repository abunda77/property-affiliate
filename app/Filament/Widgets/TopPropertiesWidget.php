<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TopPropertiesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'month';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        
        if (!$user || !$user->affiliate_code) {
            return $table
                ->heading('Top Performing Properties')
                ->query(fn () => null)
                ->columns([]);
        }

        $analyticsService = app(AnalyticsService::class);
        
        // Get date range based on filter
        [$startDate, $endDate] = $this->getDateRange();
        
        $metrics = $analyticsService->getAffiliateMetrics($user, $startDate, $endDate);
        $topProperties = $metrics['top_properties'];

        if ($topProperties->isEmpty()) {
            return $table
                ->heading('Top Performing Properties')
                ->query(fn () => \App\Models\Property::query()->whereRaw('1 = 0'))
                ->columns([
                    Tables\Columns\TextColumn::make('title')
                        ->label('No data available for selected period'),
                ]);
        }

        return $table
            ->heading('Top Performing Properties')
            ->query(
                fn () => \App\Models\Property::query()
                    ->whereIn('id', $topProperties->pluck('property_id'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('visits_count')
                    ->label('Visits')
                    ->getStateUsing(function ($record) use ($topProperties) {
                        $property = $topProperties->firstWhere('property_id', $record->id);
                        return $property ? $property['visit_count'] : 0;
                    })
                    ->sortable()
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort(fn ($query) => 
                $query->orderByRaw(
                    'FIELD(id, ' . $topProperties->pluck('property_id')->implode(',') . ')'
                )
            );
    }

    protected function getTableFilters(): array
    {
        return [
            // Fix: Return empty array to avoid "Call to a member function table() on string" error
            // The previous values were likely intended for a widget filter, not table filters
        ];
    }

    protected function getDateRange(): array
    {
        return match ($this->filter) {
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->affiliate_code !== null;
    }
}
