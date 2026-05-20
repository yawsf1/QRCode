<?php
namespace Tests\Feature;
use App\Models\SessionQR;
use Tests\TestCase;
class QrSessionTest extends TestCase
{
    public function test_admin_can_view_qr_dashboard(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin)
            ->get(route('admin.qr.show'))
            ->assertOk();
    }
    public function test_admin_can_refresh_qr_session(): void
    {
        $admin = $this->createAdmin();
        SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'old-token',
            'expires_at' => now()->addSeconds(15),
        ]);
        $this->actingAs($admin)
            ->post(route('admin.qr.refresh'))
            ->assertRedirect();
        $this->assertDatabaseMissing('sessions_qr', ['token' => 'old-token']);
        $this->assertDatabaseHas('sessions_qr', ['admin_id' => $admin->id]);
    }
}
