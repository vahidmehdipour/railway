<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['role_id', 'first_name', 'last_name', 'entry_year', 'decree_year', 'national_code', 'is_active'])]
class Personnel extends Model
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
            ->setDescriptionForEvent(fn (string $eventName) => 'پرسنل '.($events[$eventName] ?? $eventName));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_year' => 'integer',
            'decree_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The role that the personnel belongs to.
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The evaluations submitted for the personnel.
     *
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * The personnel's full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    /**
     * The personnel's final score based on the average evaluation scores.
     */
    protected function finalScore(): Attribute
    {
        return Attribute::get(function (): float {
            $averages = Evaluation::calculateAverages((int) $this->id);

            return round(
                $averages['avg_theory']
                + $averages['avg_practical']
                + $averages['avg_ethics']
                - $averages['total_penalties'],
                2
            );
        });
    }
}
