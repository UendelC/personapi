<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('people')->ignore($this->person)
            ],
            'cpf' => ['sometimes', 'cpf', Rule::unique('people')->ignore($this->person)],
            'dob' => ['sometimes', 'date', 'before_or_equal:today'],
            'nationality' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
