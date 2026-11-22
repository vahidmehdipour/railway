<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $events = [
            'created' => 'ایجاد شد',
            'updated' => 'بهروزرسانی شد',
            'deleted' => 'حذف شد',
        ];

        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => 'تنظیمات '.($events[$eventName] ?? $eventName));
    }

    /**
     * Get the value of a setting.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    /**
     * Create or update a setting.
     */
    public static function setValue(string $key, mixed $value): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value],
        );
    }
}
