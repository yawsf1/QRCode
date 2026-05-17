<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:100'],
            'prenom'      => ['required', 'string', 'max:100'],
            'email'       => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8'],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'departement' => ['required', 'string', 'max:100']
        ];
    }
    public function messages()
    {
        return [
            'departement.required' => 'Le nom de l\'entreprise est obligatoire.',
            'departement.max' => 'Le nom de l\'entreprise ne doit pas dépasser 100 caractères.',
            'departement.string' => 'Le nom de l\'entreprise doit contenir uniquement des lettres et des espaces.',
        ];
    }
}
