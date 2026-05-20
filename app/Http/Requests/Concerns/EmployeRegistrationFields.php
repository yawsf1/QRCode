<?php

namespace App\Http\Requests\Concerns;

trait EmployeRegistrationFields
{
    protected function employeRegistrationFieldRules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'departement' => ['required', 'string', 'max:100'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'heure_debut' => ['required', 'date_format:H:i', 'before:heure_fin'],
            'heure_fin' => ['required', 'date_format:H:i', 'after:heure_debut'],
            'jours_ouvres' => ['required', 'array', 'min:1'],
            'jours_ouvres.*' => ['string', 'in:Lun,Mar,Mer,Jeu,Ven,Sam,Dim'],
            'tolerance_retard' => ['required', 'integer', 'min:0', 'max:60'],
        ];
    }

    protected function employeRegistrationFieldMessages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'departement.required' => 'Le département est obligatoire.',
            'heure_debut.required' => 'L\'heure de début est obligatoire.',
            'heure_fin.required' => 'L\'heure de fin est obligatoire.',
            'heure_debut.before' => 'L\'heure de début doit être avant l\'heure de fin.',
            'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
            'jours_ouvres.required' => 'Sélectionnez au moins un jour ouvré.',
            'jours_ouvres.min' => 'Sélectionnez au moins un jour ouvré.',
            'tolerance_retard.required' => 'La tolérance de retard est obligatoire.',
        ];
    }
}
