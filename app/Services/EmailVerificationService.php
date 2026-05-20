<?php

namespace App\Services;

use App\Mail\EmailVerificationCodeMail;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationService
{
    public const CODE_LENGTH = 6;

    public const EXPIRY_MINUTES = 15;

    public function sendRegistrationCode(string $email, string $prenom): void
    {
        $email = $this->normalizeEmail($email);
        $code = $this->generateCode();

        AppCache::put(
            CacheKeys::registrationEmailVerify($email),
            [
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES)->timestamp,
                'prenom' => $prenom,
            ],
            now()->addMinutes(self::EXPIRY_MINUTES),
        );

        Mail::to($email)->send(new EmailVerificationCodeMail($prenom, $code));
    }

    public function registrationCodeIsValid(string $email, string $code): bool
    {
        $email = $this->normalizeEmail($email);
        $payload = AppCache::store()->get(CacheKeys::registrationEmailVerify($email));

        if (! is_array($payload)) {
            return false;
        }

        if (($payload['expires_at'] ?? 0) < now()->timestamp) {
            AppCache::forget(CacheKeys::registrationEmailVerify($email));

            return false;
        }

        return Hash::check($this->normalizeCode($code), $payload['code_hash'] ?? '');
    }

    public function clearRegistrationVerification(string $email): void
    {
        AppCache::forget(CacheKeys::registrationEmailVerify($this->normalizeEmail($email)));
    }

    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    protected function normalizeCode(string $code): string
    {
        return preg_replace('/\D/', '', $code) ?? '';
    }

    protected function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
