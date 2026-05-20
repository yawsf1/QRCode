<?php
namespace Tests\Feature;
use App\Events\EmployeCheckedIn;
use App\Models\Presence;
use App\Models\SessionQR;
use Tests\TestCase;
class EmployeCheckedInEventTest extends TestCase
{
    public function test_event_broadcasts_on_attendance_channel(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);
        $session = SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'event-test-token',
            'expires_at' => now()->addMinutes(5),
        ]);
        $presence = Presence::create([
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
            'statut' => 'a_lheure',
            'date_heure_scan' => now(),
            'ecart_minutes' => 0,
            'session_id' => $session->id,
        ]);
        $event = new EmployeCheckedIn($presence);
        $this->assertSame('checked-in', $event->broadcastAs());
        $this->assertCount(1, $event->broadcastOn());
        $payload = $event->broadcastWith();
        $this->assertSame($employe->id, $payload['presence']['user_id']);
        $this->assertSame($admin->id, $payload['presence']['admin_id']);
    }
}
