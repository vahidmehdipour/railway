<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'personnel_id',
    'period_id',
    'station_id',
    'description',
    'evaluation_date',
    'theory_score',
    'practical_score',
    'ethics_score',
    'accident_count',
    'near_miss_count',
    'warning_count',
    'user_id',
])]
class Evaluation extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $events = [
            'created' => 'ثبت شد',
            'updated' => 'بهروزرسانی شد',
            'deleted' => 'حذف شد',
        ];

        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => 'ارزیابی '.($events[$eventName] ?? $eventName));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluation_date' => 'date',
            'theory_score' => 'decimal:2',
            'practical_score' => 'decimal:2',
            'ethics_score' => 'decimal:2',
            'accident_count' => 'integer',
            'near_miss_count' => 'integer',
            'warning_count' => 'integer',
            'total_score' => 'decimal:2',
        ];
    }

    /**
     * The personnel being evaluated.
     *
     * @return BelongsTo<Personnel, $this>
     */
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class);
    }

    /**
     * The evaluation period.
     *
     * @return BelongsTo<EvaluationPeriod, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(EvaluationPeriod::class, 'period_id');
    }

    /**
     * The station where the evaluation was recorded.
     *
     * @return BelongsTo<Station, $this>
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * The user who submitted the evaluation.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the penalty deduction for this evaluation.
     */
    public function penaltyTotal(): float
    {
        $accidentCoefficient = (float) get_setting('accident_coefficient', 2);
        $nearMissCoefficient = (float) get_setting('near_miss_coefficient', 1);
        $warningCoefficient = (float) get_setting('warning_coefficient', 0.5);

        $penalties = ((int) $this->accident_count * $accidentCoefficient)
            + ((int) $this->near_miss_count * $nearMissCoefficient)
            + ((int) $this->warning_count * $warningCoefficient);

        return round($penalties, 2);
    }

    /**
     * Calculate this evaluation's total from its non-null scores minus penalties.
     */
    public function calculatedTotal(): float
    {
        $scores = (float) ($this->theory_score ?? 0)
            + (float) ($this->practical_score ?? 0)
            + (float) ($this->ethics_score ?? 0);

        return round($scores - $this->penaltyTotal(), 2);
    }

    /**
     * Calculate the average scores and total penalties for a personnel.
     *
     * Averages ignore null score values, and penalties are calculated using
     * the coefficients stored in the settings table.
     *
     * @return array{avg_theory: float, avg_practical: float, avg_ethics: float, total_penalties: float}
     */
    public static function calculateAverages(int $personnelId): array
    {
        $totals = static::query()
            ->where('personnel_id', $personnelId)
            ->selectRaw('
                AVG(theory_score) as avg_theory,
                AVG(practical_score) as avg_practical,
                AVG(ethics_score) as avg_ethics,
                COALESCE(SUM(accident_count), 0) as accident_count,
                COALESCE(SUM(near_miss_count), 0) as near_miss_count,
                COALESCE(SUM(warning_count), 0) as warning_count
            ')
            ->first();

        $accidentCoefficient = (float) get_setting('accident_coefficient', 2);
        $nearMissCoefficient = (float) get_setting('near_miss_coefficient', 1);
        $warningCoefficient = (float) get_setting('warning_coefficient', 0.5);

        $totalPenalties = ((int) ($totals?->accident_count ?? 0) * $accidentCoefficient)
            + ((int) ($totals?->near_miss_count ?? 0) * $nearMissCoefficient)
            + ((int) ($totals?->warning_count ?? 0) * $warningCoefficient);

        return [
            'avg_theory' => round((float) ($totals?->avg_theory ?? 0), 2),
            'avg_practical' => round((float) ($totals?->avg_practical ?? 0), 2),
            'avg_ethics' => round((float) ($totals?->avg_ethics ?? 0), 2),
            'total_penalties' => round($totalPenalties, 2),
        ];
    }
}
