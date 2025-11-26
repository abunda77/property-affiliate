<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AffiliateStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.affiliate-status-widget';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->affiliate_code !== null;
    }

    protected int|string|array $columnSpan = 1;
}
