<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonnelRequest extends FormRequest
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
            'role_id' => ['required', 'exists:roles,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'entry_year' => ['required', 'integer', 'between:1300,2100'],
            'decree_year' => ['nullable', 'integer', 'between:1300,2100'],
            'national_code' => ['nullable', 'digits:10'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $numeric = [];

        foreach (['entry_year', 'decree_year', 'national_code'] as $key) {
            if ($this->input($key) !== null) {
                $numeric[$key] = en_num($this->input($key));
            }
        }

        $this->merge([
            ...$numeric,
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
