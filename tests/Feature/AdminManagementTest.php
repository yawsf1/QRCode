<?php

namespace Tests\Feature;

use App\Events\PlatformStatsUpdated;
use App\Mail\EmailVerificationCodeMail;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    public function test_super_admin_can_register_admin(): void
    {
        Event::fake([PlatformStatsUpdated::class]);
        Mail::fake();

        $superAdmin = $this->createSuperAdmin();
        $code = $this->captureRegistrationCode('claire.martin@test.com', 'Claire');

        $payload = [
            'nom' => 'Martin',
            'prenom' => 'Claire',
            'email' => 'claire.martin@test.com',
            'verification_code' => $code,
            'password' => 'password123',
            'departement' => 'IT',
            'telephone' => '0611111111',
        ];

        $this->actingAs($superAdmin)
            ->post(route('admin.register'), $payload)
            ->assertRedirect(route('super-admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'claire.martin@test.com',
            'role' => 'admin',
        ]);

        Event::assertDispatched(PlatformStatsUpdated::class);
    }

    public function test_super_admin_can_delete_admin(): void
    {
        Event::fake([PlatformStatsUpdated::class]);
        $superAdmin = $this->createSuperAdmin();
        $admin = $this->createAdmin();

        $this->actingAs($superAdmin)
            ->delete(route('admin.delete', $admin))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_register_new_admin(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.register'), [
                'nom' => 'Test',
                'prenom' => 'User',
                'email' => 'newadmin@test.com',
                'password' => 'password123',
            ])
            ->assertForbidden();
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
