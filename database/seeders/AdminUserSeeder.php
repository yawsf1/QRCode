<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::firstOrCreate([
            'nom' => 'Admin',
            'prenom' => 'Admin',
            'email' => 'k8e6ddYdd@example.com',
            'password' => bcrypt('admin123'),
            'departement' => 'Adidas',
            'telephone' => '1234567890',
            'est_actif' => true,
            'role' => 'admin',
            'admin_id' => 1,
        ]);
        User::firstOrCreate([
            'nom' => 'Admin',
            'prenom' => 'Admin',
            'email' => 'sdsdsds@example.com',
            'password' => bcrypt('admin123'),
            'departement' => 'Nike',
            'telephone' => '1234567890',
            'est_actif' => true,
            'role' => 'admin',
            'admin_id' => 1,
        ]);
        User::firstOrCreate([
            'nom' => 'Admin',
            'prenom' => 'Admin',
            'email' => 'k8e6ddYdsddd@example.com',
            'password' => bcrypt('admin123'),
            'departement' => 'Puma',
            'telephone' => '1234567890',
            'est_actif' => true,
            'role' => 'admin',
            'admin_id' => 1,
        ]);
        
    }
}
