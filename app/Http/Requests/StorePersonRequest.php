<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('people')],
            'cpf' => ['required', 'string', 'cpf', Rule::unique('people')],
            'dob' => ['required', 'date', 'before_or_equal:today'],
            'nationality' => ['required', 'string', 'max:255'],
        ];
    }
}
