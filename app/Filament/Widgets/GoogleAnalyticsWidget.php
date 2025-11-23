<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoogleAnalyticsWidget extends Widget
{
    protected string $view = 'filament.widgets.google-analytics-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Google Analytics Dashboard';
    }

    protected function getView(): string
    {
        return 'filament.widgets.google-analytics-widget';
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        $hasGoogleAnalytics = config('services.google_analytics.id') !== null;
        
        if (!$user) {
            return false;
        }
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', 'super_admin')
            ->exists() && $hasGoogleAnalytics;
    }

    protected function getViewData(): array
    {
        return [
            'googleAnalyticsId' => config('services.google_analytics.id'),
        ];
    }
}
