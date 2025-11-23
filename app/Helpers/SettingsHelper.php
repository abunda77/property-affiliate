<?php

namespace App\Helpers;

use App\Settings\GeneralSettings;

class SettingsHelper
{
    /**
     * Get the general settings instance
     *
     * @return GeneralSettings
     */
    public static function general(): GeneralSettings
    {
        return app(GeneralSettings::class);
    }

    /**
     * Get a specific setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::general();
        return $settings->$key ?? $default;
    }
}
