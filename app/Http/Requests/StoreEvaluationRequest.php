<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $numeric = [];

        foreach (['theory_score', 'practical_score', 'ethics_score', 'accident_count', 'near_miss_count', 'warning_count'] as $key) {
            if ($this->input($key) !== null) {
                $numeric[$key] = en_num($this->input($key));
            }
        }

        if ($numeric !== []) {
            $this->merge($numeric);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'personnel_id' => ['required', 'exists:personnels,id'],
            'period_id' => ['required', 'exists:evaluation_periods,id'],
            'evaluation_date' => ['required', 'date'],
            'station_id' => ['nullable', 'exists:stations,id'],
            'description' => ['nullable', 'string'],
            'theory_score' => ['nullable', 'numeric'],
            'practical_score' => ['nullable', 'numeric'],
            'ethics_score' => ['nullable', 'numeric'],
            'accident_count' => ['required', 'integer', 'min:0'],
            'near_miss_count' => ['required', 'integer', 'min:0'],
            'warning_count' => ['required', 'integer', 'min:0'],
        ];
    }
}
