<?php

namespace App\Http\Requests\Employe;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'departement' => ['required', 'string', 'max:100'],
            'telephone' => ['nullable', 'string', 'max:20'],
            
            'heure_debut'      => ['required', 'date_format:H:i', 'before:heure_fin'],
            'heure_fin'        => ['required', 'date_format:H:i', 'after:heure_debut'],
            'jours_ouvres'     => ['required', 'array', 'min:1'],
            'jours_ouvres.*'   => ['string', 'in:Lun,Mar,Mer,Jeu,Ven,Sam,Dim'],
            'tolerance_retard' => ['required', 'integer', 'min:0', 'max:60'],        
        ];
    }
}
