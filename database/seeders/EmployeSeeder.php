<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeSeeder extends Seeder
{
    private const DEFAULT_SCHEDULE = [
        'heure_debut' => '09:00',
        'heure_fin' => '17:00',
        'jours_ouvres' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven'],
        'tolerance_retard' => 15,
    ];

    public function run(): void
    {
        $adidasAdmin = User::where('email', 'k8e6ddYdd@example.com')->first();
        if (!$adidasAdmin) {
            $this->command->error('Adidas Admin not found! Make sure to run AdminUserSeeder first.');

            return;
        }

        $employes = [
            [
                'email' => 'k8e6sdsdddYdd@example.com',
                'nom' => 'Admin',
                'prenom' => 'Admin',
            ],
            [
                'email' => 'k8e6sdssdsdsd2dddYdd@example.com',
                'nom' => 'Admin2',
                'prenom' => 'Admin2',
            ],
            [
                'email' => 'k8e6ssdsdds2dddYdd@example.com',
                'nom' => 'Admin2',
                'prenom' => 'Admin2',
            ],
            [
                'email' => 'k8e6ssdsdsads2dddYdd@example.com',
                'nom' => 'Admin2',
                'prenom' => 'Admin2',
            ],
        ];

        foreach ($employes as $data) {
            $employe = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'password' => bcrypt('admin123'),
                    'departement' => 'Admin',
                    'telephone' => '1234567890',
                    'est_actif' => true,
                    'role' => 'employe',
                    'admin_id' => $adidasAdmin->id,
                    'email_verified_at' => now(),
                ]
            );

            $employe->horaire()->firstOrCreate(
                ['user_id' => $employe->id],
                array_merge(self::DEFAULT_SCHEDULE, ['admin_id' => $adidasAdmin->id])
            );
        }
    }
}
