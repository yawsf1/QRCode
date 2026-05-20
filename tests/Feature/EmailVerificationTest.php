<?php

namespace Tests\Feature;

use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    public function test_send_admin_code_rejects_invalid_form_before_modal(): void
    {
        Mail::fake();

        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin)
            ->post(route('register.admin.verification.send'), [
                'nom' => '',
                'prenom' => '',
                'email' => 'not-an-email',
                'password' => 'short',
                'departement' => '',
            ])
            ->assertSessionHasErrors(['nom', 'prenom', 'email', 'password', 'departement']);

        Mail::assertNothingSent();
    }

    public function test_super_admin_can_request_registration_verification_code(): void
    {
        Mail::fake();

        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin)
            ->post(route('register.admin.verification.send'), [
                'nom' => 'Nouveau',
                'prenom' => 'Jean',
                'email' => 'newadmin@test.com',
                'password' => 'password123',
                'departement' => 'IT',
            ])
            ->assertRedirect();

        Mail::assertSent(EmailVerificationCodeMail::class, function ($mail) {
            return $mail->hasTo('newadmin@test.com');
        });
    }

    public function test_admin_register_requires_valid_verification_code(): void
    {
        Mail::fake();

        $superAdmin = $this->createSuperAdmin();
        $capturedCode = $this->captureRegistrationCode('valid@test.com', 'Jean');

        $this->actingAs($superAdmin)
            ->post(route('admin.register'), [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'valid@test.com',
                'email_confirmation' => 'valid@test.com',
                'verification_code' => '000000',
                'password' => 'password123',
                'departement' => 'IT',
            ])
            ->assertSessionHasErrors('verification_code');

        $this->actingAs($superAdmin)
            ->post(route('admin.register'), [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'valid@test.com',
                'verification_code' => $capturedCode,
                'password' => 'password123',
                'departement' => 'IT',
            ])
            ->assertRedirect(route('super-admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'valid@test.com',
            'role' => 'admin',
        ]);

        $this->assertNotNull(
            User::where('email', 'valid@test.com')->first()->email_verified_at
        );
    }

    public function test_admin_can_register_employe_with_verification_code(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $capturedCode = $this->captureRegistrationCode('employe@test.com', 'Marie');

        $this->actingAs($admin)
            ->post(route('admin.employe.register'), [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'employe@test.com',
                'verification_code' => $capturedCode,
                'password' => 'password123',
                'departement' => 'RH',
                'telephone' => '0600000000',
                'heure_debut' => '09:00',
                'heure_fin' => '17:00',
                'jours_ouvres' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven'],
                'tolerance_retard' => 10,
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'employe@test.com',
            'role' => 'employe',
        ]);
    }

    public function test_unverified_user_can_login_normally(): void
    {
        $admin = $this->createAdmin([
            'email' => 'legacy@test.com',
            'email_verified_at' => null,
        ]);

        $this->post(route('user.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    private function captureRegistrationCode(string $email, string $prenom): string
    {
        $code = null;

        app(EmailVerificationService::class)->sendRegistrationCode($email, $prenom);

        Mail::assertSent(EmailVerificationCodeMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        return $code;
    }
}
