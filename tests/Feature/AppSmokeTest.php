<?php

namespace Tests\Feature;

use App\Models\AlertNotification;
use App\Models\Presence;
use App\Models\SessionQR;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * End-to-end smoke coverage for pages and flows used before production deploy.
 */
class AppSmokeTest extends TestCase
{
    public function test_public_pages_are_reachable(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('login'))->assertOk();
    }

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('employe.dashboard'))->assertRedirect(route('login'));
        $this->get(route('super-admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_super_admin_can_access_all_super_admin_pages(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'))
            ->assertOk();

        $this->actingAs($superAdmin)
            ->get(route('admin.list'))
            ->assertOk();

        $this->actingAs($superAdmin)
            ->get(route('admin.register.form'))
            ->assertOk();
    }

    public function test_admin_can_access_team_management_pages(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('employe.list'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('employe.register.form'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('employe.show', $employe))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('employe.update.form', $employe))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.qr.show'))
            ->assertOk();
    }

    public function test_admin_can_update_employe(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin, [
            'nom' => 'Ancien',
            'prenom' => 'Nom',
            'email' => 'ancien@test.com',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.employe.update', $employe), [
                'nom' => 'Nouveau',
                'prenom' => 'Nom',
                'email' => 'nouveau@test.com',
                'telephone' => '0612345678',
                'departement' => 'Ops',
                'est_actif' => true,
                'heure_debut' => '08:30',
                'heure_fin' => '18:00',
                'jours_ouvres' => ['Lun', 'Mar', 'Mer'],
                'tolerance_retard' => 20,
            ])
            ->assertRedirect(route('employe.list'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $employe->id,
            'nom' => 'Nouveau',
            'email' => 'nouveau@test.com',
        ]);

        $employe->refresh();
        $this->assertSame('08:30', substr((string) $employe->horaire->heure_debut, 0, 5));
        $this->assertSame(20, (int) $employe->horaire->tolerance_retard);
    }

    public function test_employe_can_access_dashboard_and_scan_when_not_checked_in(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        $this->actingAs($employe)
            ->get(route('employe.dashboard'))
            ->assertOk();

        $this->actingAs($employe)
            ->get(route('employe.scan.form'))
            ->assertOk();
    }

    public function test_admin_can_mark_all_notifications_read(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        AlertNotification::create([
            'content' => 'Alert 1',
            'type' => 'info',
            'lu' => false,
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
        ]);

        AlertNotification::create([
            'content' => 'Alert 2',
            'type' => 'retard',
            'lu' => false,
            'user_id' => $employe->id,
            'admin_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.notifications.read-all'))
            ->assertRedirect();

        $this->assertEquals(
            0,
            AlertNotification::where('admin_id', $admin->id)->where('lu', false)->count()
        );
    }

    public function test_admin_cannot_export_rapport_for_other_team_employe(): void
    {
        $admin = $this->createAdmin();
        $otherAdmin = $this->createAdmin(['email' => 'other-admin@test.com']);
        $employe = $this->createEmploye($otherAdmin);

        $this->actingAs($admin)
            ->get(route('admin.employe.rapport', ['user' => $employe, 'type' => 'mensuel']))
            ->assertForbidden();
    }

    public function test_check_in_prevents_duplicate_scan_same_day(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        $session = SessionQR::create([
            'admin_id' => $admin->id,
            'token' => 'dup-token',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $this->actingAs($employe)
            ->post(route('employe.checkin'), ['token' => $session->token])
            ->assertRedirect(route('employe.dashboard'));

        $this->actingAs($employe)
            ->post(route('employe.checkin'), ['token' => $session->token])
            ->assertRedirect(route('employe.dashboard'))
            ->assertSessionHas('warning');

        $this->assertEquals(
            1,
            Presence::where('user_id', $employe->id)->whereDate('date_heure_scan', Carbon::today())->count()
        );
    }

    public function test_authenticated_user_can_logout(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
