<?php

namespace App\Http\Requests\Concerns;

use App\Services\EmailVerificationService;
use Illuminate\Validation\Validator;

trait ValidatesRegistrationEmailCode
{
    public function registrationEmailCodeRules(): array
    {
        return [
            'verification_code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ];
    }

    public function registrationEmailCodeMessages(): array
    {
        return [
            'verification_code.required' => 'Veuillez saisir le code reçu par e-mail.',
            'verification_code.size' => 'Le code doit contenir 6 chiffres.',
            'verification_code.regex' => 'Le code doit contenir uniquement des chiffres.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $service = app(EmailVerificationService::class);

            if (! $service->registrationCodeIsValid(
                (string) $this->input('email'),
                (string) $this->input('verification_code'),
            )) {
                $validator->errors()->add(
                    'verification_code',
                    'Code invalide ou expiré. Fermez la fenêtre et soumettez à nouveau le formulaire pour recevoir un nouveau code.',
                );
            }
        });
    }
}
