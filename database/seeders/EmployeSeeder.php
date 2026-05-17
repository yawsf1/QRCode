<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeSeeder extends Seeder
{
    public function run(): void
    {
        $adidasAdmin = User::where('email', 'k8e6ddYdd@example.com')->first();

        if (!$adidasAdmin) {
            $this->command->error("Adidas Admin not found! Make sure to run AdminUserSeeder first.");
            return;
        }

        User::firstOrCreate(
            ['email' => 'k8e6sdsdddYdd@example.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Admin',
                'password' => bcrypt('admin123'),
                'departement' => 'Admin',
                'telephone' => '1234567890',
                'est_actif' => true,
                'role' => 'employe',
                'admin_id' => $adidasAdmin->id,
            ]
        );
                
        User::firstOrCreate(
            ['email' => 'k8e6sdssdsdsd2dddYdd@example.com'],
            [
                'nom' => 'Admin2',
                'prenom' => 'Admin2',
                'password' => bcrypt('admin123'),
                'departement' => 'Admin',
                'telephone' => '1234567890',
                'est_actif' => true,
                'role' => 'employe',
                'admin_id' => $adidasAdmin->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'k8e6ssdsdds2dddYdd@example.com'],
            [
                'nom' => 'Admin2',
                'prenom' => 'Admin2',
                'password' => bcrypt('admin123'),
                'departement' => 'Admin',
                'telephone' => '1234567890',
                'est_actif' => true,
                'role' => 'employe',
                'admin_id' => $adidasAdmin->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'k8e6ssdsdsads2dddYdd@example.com'],
            [
                'nom' => 'Admin2',
                'prenom' => 'Admin2',
                'password' => bcrypt('admin123'),
                'departement' => 'Admin',
                'telephone' => '1234567890',
                'est_actif' => true,
                'role' => 'employe',
                'admin_id' => $adidasAdmin->id,
            ]
        );
    }
}