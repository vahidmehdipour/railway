<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['name_en', 'name_fa'])]
class Role extends Model
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
            ->setDescriptionForEvent(fn (string $eventName) => 'نقش پرسنل '.($events[$eventName] ?? $eventName));
    }

    /**
     * The personnel that belong to the role.
     *
     * @return HasMany<Personnel, $this>
     */
    public function personnels(): HasMany
    {
        return $this->hasMany(Personnel::class);
    }
}
