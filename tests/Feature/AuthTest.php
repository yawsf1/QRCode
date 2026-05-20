<?php
namespace Tests\Feature;
use Tests\TestCase;
class AuthTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertOk();
    }
    public function test_super_admin_can_login_and_is_redirected(): void
    {
        $user = $this->createSuperAdmin(['email' => 'super@test.com']);
        $this->post(route('user.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('super-admin.dashboard'));
    }
    public function test_admin_can_login_and_is_redirected(): void
    {
        $user = $this->createAdmin(['email' => 'admin@test.com']);
        $this->post(route('user.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }
    public function test_employe_can_login_and_is_redirected(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin, ['email' => 'employe@test.com']);
        $this->post(route('user.login'), [
            'email' => $employe->email,
            'password' => 'password',
        ])->assertRedirect(route('employe.dashboard'));
    }
    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->createAdmin(['email' => 'admin@test.com']);
        $this->post(route('user.login'), [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }
    public function test_authenticated_user_can_logout(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('home'));
    }

    public function test_login_with_remember_me_sets_remember_cookie(): void
    {
        $admin = $this->createAdmin(['email' => 'remember@test.com']);

        $response = $this->post(route('user.login'), [
            'email' => $admin->email,
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNotNull($admin->fresh()->remember_token);
    }
}
