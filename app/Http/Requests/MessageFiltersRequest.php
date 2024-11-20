<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class MessageFiltersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tags'      => 'nullable|array',
            'tags.*'    => 'exists:tags,name',
            'user_ids'  => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'date_from' => 'nullable|date|before_or_equal:date_to',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ];
    }
    protected function prepareForValidation(): void
    {
        foreach (['date_from', 'date_to'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => $this->convertToCarbon($this->input($field)),
                ]);
            }
        }
    }

    protected function convertToCarbon(?string $date): ?Carbon
    {
        return $date ? Carbon::parse($date) : null;
    }
}
