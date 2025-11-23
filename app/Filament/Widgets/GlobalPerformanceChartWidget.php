<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class GlobalPerformanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public ?string $filter = 'month';

    public function getHeading(): ?string
    {
        return 'Global Performance Trends';
    }

    protected function getData(): array
    {
        $user = Auth::user();
        
        // Only show for Super Admin
        $isSuperAdmin = $user && DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', User::class)
            ->where('roles.name', 'super_admin')
            ->exists();

        if (!$isSuperAdmin) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        [$startDate, $endDate, $labels] = $this->getDateRangeAndLabels();

        $visitsData = $this->getVisitsData($startDate, $endDate, $labels);
        $leadsData = $this->getLeadsData($startDate, $endDate, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Visits',
                    'data' => array_values($visitsData),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Leads',
                    'data' => array_values($leadsData),
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

    protected function getDateRangeAndLabels(): array
    {
        $labels = [];
        $startDate = null;
        $endDate = Carbon::now();

        switch ($this->filter) {
            case 'week':
                $startDate = Carbon::now()->subDays(6);
                for ($i = 6; $i >= 0; $i--) {
                    $labels[] = Carbon::now()->subDays($i)->format('M d');
                }
                break;
            case 'month':
                $startDate = Carbon::now()->subDays(29);
                for ($i = 29; $i >= 0; $i--) {
                    $labels[] = Carbon::now()->subDays($i)->format('M d');
                }
                break;
            case 'year':
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();
                for ($i = 11; $i >= 0; $i--) {
                    $labels[] = Carbon::now()->subMonths($i)->format('M Y');
                }
                break;
            default:
                $startDate = Carbon::now()->subDays(29);
                for ($i = 29; $i >= 0; $i--) {
                    $labels[] = Carbon::now()->subDays($i)->format('M d');
                }
        }

        return [$startDate, $endDate, $labels];
    }

    protected function getVisitsData(Carbon $startDate, Carbon $endDate, array $labels): array
    {
        $data = [];

        if ($this->filter === 'year') {
            // Monthly aggregation
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $count = DB::table('visits')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $data[] = $count;
            }
        } else {
            // Daily aggregation
            $days = $this->filter === 'week' ? 7 : 30;
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = DB::table('visits')
                    ->whereDate('created_at', $date)
                    ->count();
                $data[] = $count;
            }
        }

        return $data;
    }

    protected function getLeadsData(Carbon $startDate, Carbon $endDate, array $labels): array
    {
        $data = [];

        if ($this->filter === 'year') {
            // Monthly aggregation
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $count = DB::table('leads')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $data[] = $count;
            }
        } else {
            // Daily aggregation
            $days = $this->filter === 'week' ? 7 : 30;
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = DB::table('leads')
                    ->whereDate('created_at', $date)
                    ->count();
                $data[] = $count;
            }
        }

        return $data;
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', User::class)
            ->where('roles.name', 'super_admin')
            ->exists();
    }
}
