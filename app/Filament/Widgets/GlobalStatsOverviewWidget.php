<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GlobalStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public ?string $filter = 'today';

    protected function getStats(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Only show for Super Admin
        if (!$user || !$user->hasRole('super_admin')) {
            return [];
        }

        $analyticsService = app(AnalyticsService::class);
        
        // Get date range based on filter
        [$startDate, $endDate] = $this->getDateRange();
        
        $metrics = $analyticsService->getGlobalMetrics($startDate, $endDate);

        return [
            Stat::make('Total Traffic', number_format($metrics['total_traffic']))
                ->description('Total visits across all affiliates')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->getTrafficTrend($startDate, $endDate)),
            
            Stat::make('Total Leads', number_format($metrics['total_leads']))
                ->description('New leads generated')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->chart($this->getLeadsTrend($startDate, $endDate)),
            
            Stat::make('Active Affiliates', number_format($metrics['active_affiliates']))
                ->description('Affiliates with activity')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            
            Stat::make('Conversion Rate', $metrics['conversion_rate'] . '%')
                ->description('Leads / Visits')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
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

    protected function getTrafficTrend(Carbon $startDate, Carbon $endDate): array
    {
        // Get daily traffic for the last 7 days for chart
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = DB::table('visits')
                ->whereDate('created_at', $date)
                ->count();
            $days[] = $count;
        }
        return $days;
    }

    protected function getLeadsTrend(Carbon $startDate, Carbon $endDate): array
    {
        // Get daily leads for the last 7 days for chart
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = DB::table('leads')
                ->whereDate('created_at', $date)
                ->count();
            $days[] = $count;
        }
        return $days;
    }

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->hasRole('super_admin');
    }
}
