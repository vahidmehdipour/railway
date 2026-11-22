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
