<?php

namespace Tests\Feature;

use App\Events\PlatformStatsUpdated;
use App\Mail\EmailVerificationCodeMail;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmployeManagementTest extends TestCase
{
    public function test_admin_can_register_employe(): void
    {
        Event::fake([PlatformStatsUpdated::class]);
        Mail::fake();

        $admin = $this->createAdmin();
        $code = $this->captureRegistrationCode('jean.dupont@test.com', 'Jean');

        $payload = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@test.com',
            'verification_code' => $code,
            'password' => 'password123',
            'departement' => 'RH',
            'telephone' => '0600000000',
            'heure_debut' => '09:00',
            'heure_fin' => '17:00',
            'jours_ouvres' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven'],
            'tolerance_retard' => 10,
        ];

        $this->actingAs($admin)
            ->post(route('admin.employe.register'), $payload)
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'jean.dupont@test.com',
            'role' => 'employe',
            'admin_id' => $admin->id,
        ]);

        Event::assertDispatched(PlatformStatsUpdated::class);
    }

    public function test_admin_can_delete_own_employe(): void
    {
        Event::fake([PlatformStatsUpdated::class]);
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        $this->actingAs($admin)
            ->delete(route('admin.employe.delete', $employe))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $employe->id]);
    }

    public function test_admin_cannot_delete_other_admin_employe(): void
    {
        $admin = $this->createAdmin();
        $otherAdmin = $this->createAdmin(['email' => 'other@test.com']);
        $employe = $this->createEmploye($otherAdmin);

        $this->actingAs($admin)
            ->delete(route('admin.employe.delete', $employe))
            ->assertRedirect()
            ->assertSessionHas('error');
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
