<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'start_date', 'end_date', 'is_active'])]
class EvaluationPeriod extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The evaluations recorded in the period.
     *
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'period_id');
    }

    /**
     * Scope the query to active periods.
     *
     * @param  Builder<EvaluationPeriod>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
