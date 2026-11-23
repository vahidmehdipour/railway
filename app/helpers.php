<?php

use App\Models\Setting;
use App\Support\Appearance;
use Hekmatinasser\Verta\Verta;

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

if (! function_exists('app_version')) {
    /**
     * Get the current semantic application version.
     */
    function app_version(): string
    {
        return (string) config('app.version');
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

if (! function_exists('fa_input')) {
    /**
     * Format a value for a Persian-digit form input, keeping empty values empty.
     */
    function fa_input(mixed $value): string
    {
        return $value === null || $value === '' ? '' : fa_num($value);
    }
}

if (! function_exists('en_num')) {
    /**
     * Convert Persian and Arabic digits back to Latin digits for storage.
     */
    function en_num(mixed $value): string
    {
        return preg_replace_callback(
            '/[\x{0660}-\x{0669}\x{06F0}-\x{06F9}]/u',
            fn (array $matches): string => (string) (mb_ord($matches[0], 'UTF-8') - (mb_ord($matches[0], 'UTF-8') >= 0x06F0 ? 0x06F0 : 0x0660)),
            (string) $value,
        );
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

        return fa_num(Verta::instance($date)->format($format));
    }
}
