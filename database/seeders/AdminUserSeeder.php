<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $superAdminId = $superAdmin ? $superAdmin->id : null;
        User::firstOrCreate(
            ['email' => 'k8e6ddYdd@example.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Adidas',
                'password' => bcrypt('admin123'),
                'departement' => 'Adidas',
                'telephone' => '1234567890',
                'est_actif' => true,
                'role' => 'admin',
                'admin_id' => $superAdminId,
            ]
        );
        User::firstOrCreate(
            ['email' => 'sdsdsds@example.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Nike',
                'password' => bcrypt('admin123'),
                'departement' => 'Nike',
                'telephone' => '1234567890',
                'est_actif' => true,
                'role' => 'admin',
                'admin_id' => $superAdminId,
            ]
        );
        User::firstOrCreate(
            ['email' => 'k8e6ddYdsddd@example.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Puma',
                'password' => bcrypt('admin123'),
                'departement' => 'Puma',
                'telephone' => '1234567890',
                'est_actif' => true,
                'role' => 'admin',
                'admin_id' => $superAdminId,
            ]
        );
    }
}