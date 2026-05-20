<x-mail::message>
# Vérification de votre adresse e-mail

Bonjour **{{ $recipientPrenom }}**,

Utilisez le code ci-dessous pour confirmer cette adresse e-mail lors de la création du compte. Ce code expire dans **15 minutes**.

<x-mail::panel>
## {{ $code }}
</x-mail::panel>

Si vous n'avez pas demandé ce code, ignorez cet e-mail.

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
