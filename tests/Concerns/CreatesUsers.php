<?php
namespace Tests\Concerns;
use App\Models\User;
trait CreatesUsers
{
    protected function createSuperAdmin(array $attributes = []): User
    {
        return User::factory()->superAdmin()->create($attributes);
    }
    protected function createAdmin(array $attributes = []): User
    {
        return User::factory()->admin()->create($attributes);
    }
    protected function createEmploye(User $admin, array $attributes = []): User
    {
        $employe = User::factory()->employe($admin->id)->create($attributes);
        $employe->horaire()->create([
            'heure_debut' => '09:00',
            'heure_fin' => '17:00',
            'jours_ouvres' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven'],
            'tolerance_retard' => 15,
            'admin_id' => $admin->id,
        ]);
        return $employe;
    }
}
