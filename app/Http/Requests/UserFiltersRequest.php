<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFiltersRequest extends FormRequest
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
            'email' => 'nullable|string|min:3',
            'name' => 'nullable|string|min:3',
        ];
    }
}
