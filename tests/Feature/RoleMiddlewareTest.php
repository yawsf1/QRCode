<?php
namespace Tests\Feature;
use Tests\TestCase;
class RoleMiddlewareTest extends TestCase
{
    public function test_employe_cannot_access_admin_dashboard(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);
        $this->actingAs($employe)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
    public function test_admin_cannot_access_super_admin_dashboard(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin)
            ->get(route('super-admin.dashboard'))
            ->assertForbidden();
    }
    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }
}
