<?php

namespace App\Http\Requests\Concerns;

trait AdminRegistrationFields
{
    protected function adminRegistrationFieldRules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'departement' => ['required', 'string', 'max:100'],
        ];
    }

    protected function adminRegistrationFieldMessages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'departement.required' => 'Le nom de l\'entreprise est obligatoire.',
            'departement.max' => 'Le nom de l\'entreprise ne doit pas dépasser 100 caractères.',
        ];
    }
}
