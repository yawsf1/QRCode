<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Veuillez saisir le code reçu par e-mail.',
            'code.size' => 'Le code doit contenir 6 chiffres.',
            'code.regex' => 'Le code doit contenir uniquement des chiffres.',
        ];
    }
}
