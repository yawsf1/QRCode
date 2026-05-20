<?php

namespace Tests\Feature;

use App\Models\AlertNotification;
use App\Models\Presence;
use App\Models\SessionQR;
use Carbon\Carbon;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function test_check_in_creates_admin_notification(): void
    {
        $admin = $this->createAdmin();
        $admin->horaire()->create([
            'heure_debut' => '09:00',
            'heure_fin' => '17:00',
            'jours_ouvres' => ['Lun'],
            'tolerance_retard' => 15,
            'admin_id' => $admin->id,
        ]);

        $employe = $this->createEmploye($admin);

        $session = SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'notify-token',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Carbon::setTestNow(Carbon::today()->setTime(10, 30));

        $this->actingAs($employe)
            ->post(route('employe.checkin'), ['token' => $session->token])
            ->assertRedirect(route('employe.dashboard'));

        $this->assertDatabaseHas('notifications', [
            'admin_id' => $admin->id,
            'user_id' => $employe->id,
            'type' => 'retard',
            'lu' => false,
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_can_mark_notification_read(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        $notification = AlertNotification::create([
            'content' => 'Test alert',
            'type' => 'info',
            'lu' => false,
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertTrue($notification->fresh()->lu);
    }
}
