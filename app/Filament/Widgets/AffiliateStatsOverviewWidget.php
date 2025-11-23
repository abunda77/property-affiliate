<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AffiliateStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public ?string $filter = 'today';

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Only show for affiliates
        if (!$user || !$user->affiliate_code) {
            return [];
        }

        $analyticsService = app(AnalyticsService::class);
        
        // Get date range based on filter
        [$startDate, $endDate] = $this->getDateRange();
        
        $metrics = $analyticsService->getAffiliateMetrics($user, $startDate, $endDate);

        return [
            Stat::make('Clicks', $metrics['total_visits'])
                ->description('Total visits')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            
            Stat::make('Leads', $metrics['total_leads'])
                ->description('New leads')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
            
            Stat::make('Conversion Rate', $metrics['conversion_rate'] . '%')
                ->description('Leads / Visits')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getDateRange(): array
    {
        return match ($this->filter) {
            'today' => [Carbon::today(), Carbon::now()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()],
            default => [Carbon::today(), Carbon::now()],
        };
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->affiliate_code !== null;
    }
}
