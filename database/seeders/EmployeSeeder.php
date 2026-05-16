<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::firstOrCreate([
            'nom' => 'Admin',
            'prenom' => 'Admin',
            'email' => 'k8e6sdsdddYdd@example.com',
            'password' => bcrypt('admin123'),
            'departement' => 'Admin',
            'telephone' => '1234567890',
            'est_actif' => true,
            'role' => 'employe',
            'admin_id' => 2,
        ]);
                
        User::firstOrCreate([
            'nom' => 'Admin2',
            'prenom' => 'Admin2',
            'email' => 'k8e6sds2dddYdd@example.com',
            'password' => bcrypt('admin123'),
            'departement' => 'Admin',
            'telephone' => '1234567890',
            'est_actif' => true,
            'role' => 'employe',
            'admin_id' => 2,
        ]);
    }
}
