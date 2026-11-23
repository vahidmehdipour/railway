<?php

namespace App\Support;

use App\Models\EvaluationPeriod;
use App\Models\Personnel;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

/**
 * Turns the raw attribute names stored by Spatie Activitylog into readable,
 * human-friendly change rows for the system activity log.
 */
final class ActivityChanges
{
    /** @var array<string, string> */
    private const ATTRIBUTE_LABELS = [
        'name' => 'نام',
        'name_en' => 'نام انگلیسی',
        'name_fa' => 'نام فارسی',
        'first_name' => 'نام',
        'last_name' => 'نام خانوادگی',
        'national_code' => 'کد ملی',
        'entry_year' => 'سال ورود',
        'decree_year' => 'سال حکم',
        'is_active' => 'وضعیت',
        'role_id' => 'نقش پرسنل',
        'personnel_id' => 'پرسنل',
        'period_id' => 'دوره ارزیابی',
        'station_id' => 'ایستگاه',
        'user_id' => 'ثبتکننده',
        'title' => 'عنوان',
        'start_date' => 'تاریخ شروع',
        'end_date' => 'تاریخ پایان',
        'description' => 'توضیحات',
        'evaluation_date' => 'تاریخ ارزیابی',
        'theory_score' => 'نمره تئوری',
        'practical_score' => 'نمره عملی',
        'ethics_score' => 'نمره اخلاق',
        'accident_count' => 'تعداد سانحهها',
        'near_miss_count' => 'شبه سانحهها',
        'warning_count' => 'تعداد اخطارها',
        'total_score' => 'نمره کل',
        'key' => 'تنظیم',
        'value' => 'مقدار',
    ];

    /** @var array<string, string> */
    private const SETTING_LABELS = [
        'max_theory_score' => 'حداکثر نمره تئوری',
        'max_practical_score' => 'حداکثر نمره عملی',
        'max_ethics_score' => 'حداکثر نمره اخلاق',
        'accident_coefficient' => 'ضریب سانحه',
        'near_miss_coefficient' => 'ضریب شبه سانحه',
        'warning_coefficient' => 'ضریب اخطار',
        'app_name' => 'نام سامانه',
        'footer_text' => 'متن پاورقی',
        'copyright_text' => 'متن کپی رایت',
        'login_background' => 'پسزمینه صفحه ورود',
    ];

    /** @var array<string, string> */
    private const DATE_ATTRIBUTES = ['start_date', 'end_date', 'evaluation_date'];

    /** @var array<string, string> */
    private const SCORE_ATTRIBUTES = ['theory_score', 'practical_score', 'ethics_score', 'total_score'];

    /** @var array<string, string> */
    private const NUMBER_ATTRIBUTES = ['entry_year', 'decree_year', 'accident_count', 'near_miss_count', 'warning_count'];

    /** @var array<string, class-string> */
    private const RELATED_MODELS = [
        'role_id' => Role::class,
        'personnel_id' => Personnel::class,
        'period_id' => EvaluationPeriod::class,
        'station_id' => Station::class,
        'user_id' => User::class,
    ];

    /** @var array<string, string|null> */
    private static array $relatedCache = [];

    /**
     * Build the readable change rows for an activity record.
     *
     * @return list<array{label: string, old: ?string, new: ?string, removed: bool}>
     */
    public static function format(Activity $activity): array
    {
        $changes = $activity->attribute_changes;

        if (! $changes instanceof Collection || $changes->isEmpty()) {
            // Older records may still keep the changes in the properties column.
            $changes = $activity->properties;
        }

        if (! $changes instanceof Collection) {
            return [];
        }

        /** @var array<string, mixed> $attributes */
        $attributes = $changes->get('attributes', []);
        /** @var array<string, mixed> $old */
        $old = $changes->get('old', []);

        if ($attributes === [] && $old === []) {
            return [];
        }

        $settingKey = self::settingKey($activity, $attributes, $old);
        $rows = [];

        foreach ($attributes as $key => $value) {
            if ($key === 'key' && $settingKey !== null) {
                continue;
            }

            $rows[] = self::row((string) $key, $value, $old[$key] ?? null, false, $settingKey);
        }

        foreach ($old as $key => $oldValue) {
            if (array_key_exists($key, $attributes)) {
                continue;
            }

            $rows[] = self::row((string) $key, null, $oldValue, true, $settingKey);
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $old
     */
    private static function settingKey(Activity $activity, array $attributes, array $old): ?string
    {
        if ($activity->subject_type !== Setting::class) {
            return null;
        }

        $key = $attributes['key'] ?? $old['key'] ?? null;

        return is_scalar($key) ? (string) $key : null;
    }

    /**
     * @return array{label: string, old: ?string, new: ?string, removed: bool}
     */
    private static function row(string $key, mixed $value, mixed $oldValue, bool $removed, ?string $settingKey): array
    {
        $label = self::ATTRIBUTE_LABELS[$key] ?? str_replace('_', ' ', $key);

        if ($key === 'value' && $settingKey !== null) {
            $label = self::SETTING_LABELS[$settingKey] ?? $settingKey;
        }

        return [
            'label' => $label,
            'old' => $oldValue === null ? null : self::display($key, $oldValue, $settingKey),
            'new' => $value === null ? null : self::display($key, $value, $settingKey),
            'removed' => $removed,
        ];
    }

    private static function display(string $key, mixed $value, ?string $settingKey): string
    {
        if ($key === 'is_active') {
            return $value ? 'فعال' : 'غیرفعال';
        }

        if ($key === 'key' && $settingKey !== null) {
            return self::SETTING_LABELS[$settingKey] ?? $settingKey;
        }

        if ($key === 'value' && $settingKey === 'login_background') {
            $background = is_scalar($value) ? (string) $value : '';

            return Appearance::BACKGROUNDS[$background] ?? $background;
        }

        if (isset(self::RELATED_MODELS[$key])) {
            $id = is_scalar($value) ? (int) $value : 0;

            return self::relatedName((string) $key, $id) ?? '#'.fa_num($id);
        }

        if (in_array($key, self::DATE_ATTRIBUTES, true)) {
            return jdate($value);
        }

        if (in_array($key, self::SCORE_ATTRIBUTES, true)) {
            return fa_num(number_format((float) $value, 2));
        }

        if (in_array($key, self::NUMBER_ATTRIBUTES, true)) {
            return fa_num($value);
        }

        if ($key === 'national_code') {
            return fa_num($value);
        }

        return is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    private static function relatedName(string $key, int $id): ?string
    {
        $cacheKey = $key.':'.$id;

        if (array_key_exists($cacheKey, self::$relatedCache)) {
            return self::$relatedCache[$cacheKey];
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = self::RELATED_MODELS[$key];
        $model = $modelClass::query()->find($id);

        $name = match ($key) {
            'role_id' => $model?->name_fa,
            'personnel_id' => $model?->full_name,
            'period_id' => $model?->title,
            'user_id' => $model?->name,
            default => $model?->name,
        };

        return self::$relatedCache[$cacheKey] = $name === null || $name === '' ? null : (string) $name;
    }
}
