<?php

use App\Models\Setting;
use App\Support\Appearance;

if (! function_exists('get_setting')) {
    /**
     * Get a setting value from the database.
     */
    function get_setting(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }
}

if (! function_exists('appearance_setting')) {
    /**
     * Get an appearance setting with its configured default.
     */
    function appearance_setting(string $key): mixed
    {
        return get_setting($key, Appearance::DEFAULTS[$key] ?? null);
    }
}

if (! function_exists('fa_num')) {
    /**
     * Convert Latin digits to Persian digits for display.
     */
    function fa_num(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return strtr((string) $value, [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',
        ]);
    }
}

if (! function_exists('jdate')) {
    /**
     * Format a Gregorian date as a Shamsi (Jalali) date.
     */
    function jdate(mixed $date, string $format = 'Y/m/d'): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        return fa_num(\Hekmatinasser\Verta\Verta::instance($date)->format($format));
    }
}
