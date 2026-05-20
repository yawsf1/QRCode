<?php
namespace Tests\Feature;
use App\Events\EmployeCheckedIn;
use App\Models\Presence;
use App\Models\SessionQR;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
class CheckInTest extends TestCase
{
    public function test_employe_can_check_in_with_valid_qr_token(): void
    {
        Event::fake([EmployeCheckedIn::class]);
        $admin = $this->createAdmin();
        $admin->horaire()->create([
            'heure_debut' => '09:00',
            'heure_fin' => '17:00',
            'jours_ouvres' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven'],
            'tolerance_retard' => 15,
            'admin_id' => $admin->id,
        ]);
        $employe = $this->createEmploye($admin);
        $session = SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'valid-token-123',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);
        Carbon::setTestNow(Carbon::today()->setTime(9, 0));
        $this->actingAs($employe)
            ->post(route('employe.checkin'), ['token' => $session->token])
            ->assertRedirect(route('employe.dashboard'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('presences', [
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
            'statut' => 'a_lheure',
        ]);
        Event::assertDispatched(EmployeCheckedIn::class);
        Carbon::setTestNow();
    }
    public function test_check_in_rejects_expired_token(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);
        $session = SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'expired-token',
            'expires_at' => Carbon::now()->subMinute(),
        ]);
        $this->actingAs($employe)
            ->post(route('employe.checkin'), ['token' => $session->token])
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertDatabaseCount('presences', 0);
    }
    public function test_check_in_prevents_duplicate_scan_same_day(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);
        $cacheKey = CacheKeys::userScannedToday($employe->id, Carbon::today()->toDateString());
        AppCache::put($cacheKey, true, now()->addDay());
        $session = SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'token-dup',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);
        $this->actingAs($employe)
            ->post(route('employe.checkin'), ['token' => $session->token])
            ->assertRedirect(route('employe.dashboard'))
            ->assertSessionHas('warning');
        $this->assertDatabaseCount('presences', 0);
    }
}
