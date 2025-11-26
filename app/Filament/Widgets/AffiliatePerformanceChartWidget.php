<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AffiliatePerformanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public ?string $filter = 'month';

    public function getHeading(): ?string
    {
        return 'Performance Trends';
    }

    protected function getData(): array
    {
        $user = Auth::user();

        if (! $user || ! $user->affiliate_code) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Get date range and interval based on filter
        [$startDate, $endDate, $interval, $format] = $this->getDateRangeAndInterval();

        $labels = [];
        $visitsData = [];
        $leadsData = [];

        if ($this->filter === 'year') {
            // For yearly view, group by month
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i)->startOfMonth();
                $labels[] = $date->format($format);

                $visitsCount = \App\Models\Visit::where('affiliate_id', $user->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $leadsCount = \App\Models\Lead::where('affiliate_id', $user->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $visitsData[] = $visitsCount;
                $leadsData[] = $leadsCount;
            }
        } else {
            // For week/month view, group by day
            $days = $this->filter === 'week' ? 6 : 29;

            for ($i = $days; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format($format);

                $visitsCount = \App\Models\Visit::where('affiliate_id', $user->id)
                    ->whereDate('created_at', $date)
                    ->count();

                $leadsCount = \App\Models\Lead::where('affiliate_id', $user->id)
                    ->whereDate('created_at', $date)
                    ->count();

                $visitsData[] = $visitsCount;
                $leadsData[] = $leadsCount;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Visits',
                    'data' => $visitsData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Leads',
                    'data' => $leadsData,
                    'borderColor' => 'rgb(251, 146, 60)',
                    'backgroundColor' => 'rgba(251, 146, 60, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            'year' => 'Last 12 Months',
        ];
    }

    protected function getDateRangeAndInterval(): array
    {
        return match ($this->filter) {
            'week' => [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now(),
                '1 day',
                'M d',
            ],
            'month' => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now(),
                '1 day',
                'M d',
            ],
            'year' => [
                Carbon::now()->subMonths(11)->startOfMonth(),
                Carbon::now(),
                '1 month',
                'M Y',
            ],
            default => [
                Carbon::now()->subDays(29)->startOfDay(),
                Carbon::now(),
                '1 day',
                'M d',
            ],
        };
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->affiliate_code !== null;
    }

    protected int|string|array $columnSpan = 1;
}
