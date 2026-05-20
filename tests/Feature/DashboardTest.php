<?php
namespace Tests\Feature;
use Tests\TestCase;
class DashboardTest extends TestCase
{
    public function test_admin_can_access_dashboard(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
    public function test_super_admin_can_access_dashboard(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'))
            ->assertOk();
    }
    public function test_employe_can_access_dashboard(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);
        $this->actingAs($employe)
            ->get(route('employe.dashboard'))
            ->assertOk();
    }
}
