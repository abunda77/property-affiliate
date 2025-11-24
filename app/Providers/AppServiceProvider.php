<?php

namespace App\Providers;

use App\Events\LeadCreated;
use App\Listeners\SendLeadNotification;
use App\Models\Property;
use App\Observers\PropertyObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Property::observe(PropertyObserver::class);

        // Register event listeners
        Event::listen(
            LeadCreated::class,
            SendLeadNotification::class,
        );

        // Configure Scramble API Documentation
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        // Share settings with all views
        view()->composer('*', function ($view) {
            try {
                $settings = app(\App\Settings\GeneralSettings::class);
                $view->with('settings', $settings);
            } catch (\Exception $e) {
                // If settings table doesn't exist yet (during migration), use defaults
                $view->with('settings', null);
            }
        });
    }
}
