<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // GoWA API Configuration
    public ?string $gowa_username;

    public ?string $gowa_password;

    public ?string $gowa_api_url;

    public ?string $test_phone;

    // Logo
    public ?string $logo_path;

    public ?string $logo_url;

    public ?string $favicon_path;

    // Hero Section
    public ?string $hero_background_image;

    // SEO Settings
    public ?string $seo_meta_title;

    public ?string $seo_meta_description;

    public ?string $seo_meta_keywords;

    // Contact Information
    public ?string $contact_email;

    public ?string $contact_whatsapp;

    // Legal Documents
    public ?string $terms_and_conditions;

    public ?string $privacy_policy;

    public ?string $disclaimer;

    public ?string $about_us;

    public static function group(): string
    {
        return 'general';
    }
}
