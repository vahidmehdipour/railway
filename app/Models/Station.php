<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['name'])]
class Station extends Model
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
            ->setDescriptionForEvent(fn (string $eventName) => 'ایستگاه '.($events[$eventName] ?? $eventName));
    }

    /**
     * The evaluations recorded at the station.
     *
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
