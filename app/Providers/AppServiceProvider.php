<?php

namespace App\Providers;



use App\Listeners\ApproveUserAfterEmailVerification;
use App\Models\Property;
use App\Observers\PropertyObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production
        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }
        
        Property::observe(PropertyObserver::class);

        // Register event listeners
        Event::listen(Verified::class, ApproveUserAfterEmailVerification::class);



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
            } catch (\Spatie\LaravelSettings\Exceptions\MissingSettings $e) {
                // Auto-initialize missing settings
                $this->initializeMissingSettings();
                
                // Try again after initialization
                try {
                    $settings = app(\App\Settings\GeneralSettings::class);
                    $view->with('settings', $settings);
                } catch (\Throwable $e2) {
                    $view->with('settings', null);
                }
            } catch (\Throwable $e) {
                // If settings table doesn't exist yet (during migration), use defaults
                $view->with('settings', null);
            }
        });
    }

    /**
     * Initialize missing settings properties
     */
    private function initializeMissingSettings(): void
    {
        $keys = [
            'terms_and_conditions', 'privacy_policy', 'disclaimer', 'about_us',
            'gowa_username', 'gowa_password', 'gowa_api_url', 'test_phone',
            'logo_path', 'logo_url', 'favicon_path',
            'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords',
            'contact_email', 'contact_whatsapp',
        ];

        foreach ($keys as $key) {
            if (!\Illuminate\Support\Facades\DB::table('settings')->where('group', 'general')->where('name', $key)->exists()) {
                \Illuminate\Support\Facades\DB::table('settings')->insert([
                    'group' => 'general',
                    'name' => $key,
                    'locked' => false,
                    'payload' => json_encode(null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
