<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class SuperAdminUserSeeder extends Seeder
{
public function run(): void
{
    User::firstOrCreate(
        ['email' => 'k8e6Y@example.com'],
        [
            'nom' => 'Admin',
            'prenom' => 'Admin',
            'password' => bcrypt('admin123'),
            'departement' => 'Admin',
            'telephone' => '1234567890',
            'est_actif' => true,
            'role' => 'super_admin',
            'admin_id' => null,
        ]
    );
}
}
