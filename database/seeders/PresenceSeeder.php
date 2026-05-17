<?php

namespace Database\Seeders;

use App\Models\Presence;
use App\Models\User;
use App\Models\SessionQR;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = [
            'k8e6sdsdddYdd@example.com',
            'k8e6sdssdsdsd2dddYdd@example.com',
            'k8e6ssdsdds2dddYdd@example.com',
            'k8e6ssdsdsads2dddYdd@example.com'
        ];

        $employees = User::whereIn('email', $emails)->get();

        if ($employees->isEmpty()) {
            $this->command->error("None of the targeted employees were found! Make sure to run EmployeSeeder first.");
            return;
        }

        $session = SessionQR::first() ?? SessionQR::create([
            'token' => uniqid('test_'),
            'est_actif' => true,
            'expires_at' => Carbon::now()->addDays(30),
            'admin_id' => 2,
        ]);

        $mockCheckIns = [
            ['days_ago' => 9, 'time' => '08:42:00', 'statut' => 'a_lheure', 'ecart' => 0],
            ['days_ago' => 8, 'time' => '08:51:10', 'statut' => 'a_lheure', 'ecart' => 0],            
            ['days_ago' => 7, 'time' => '00:00:00', 'statut' => 'absent', 'ecart' => 0],
            ['days_ago' => 6, 'time' => '09:18:00', 'statut' => 'en_retard', 'ecart' => 18],
            ['days_ago' => 5, 'time' => '08:45:15', 'statut' => 'a_lheure', 'ecart' => 0],
            ['days_ago' => 4, 'time' => '08:58:00', 'statut' => 'a_lheure', 'ecart' => 0],
            ['days_ago' => 3, 'time' => '00:00:00', 'statut' => 'absent', 'ecart' => 0],
            ['days_ago' => 2, 'time' => '09:42:15', 'statut' => 'en_retard', 'ecart' => 42],
            ['days_ago' => 1, 'time' => '08:35:00', 'statut' => 'en_avance', 'ecart' => 0],
            ['days_ago' => 1, 'time' => '14:02:00', 'statut' => 'a_lheure', 'ecart' => 0],
            ['days_ago' => 0, 'time' => '08:52:22', 'statut' => 'a_lheure', 'ecart' => 0],
        ];

        $tableName = (new Presence())->getTable();
        $hasSessionId = Schema::hasColumn($tableName, 'session_id');
        $hasSessionQrsId = Schema::hasColumn($tableName, 'session_qrs_id');

        foreach ($employees as $employee) {
            Presence::where('user_id', $employee->id)->delete();

            foreach ($mockCheckIns as $data) {
                $scanTimestamp = Carbon::now()
                    ->subDays($data['days_ago'])
                    ->setTimeFromTimeString($data['time']);

                $presenceData = [
                    'date_heure_scan' => $scanTimestamp,
                    'statut'          => $data['statut'],
                    'ecart_minutes'   => $data['ecart'],
                    'adresse_ip'      => '192.168.1.' . rand(10, 250),
                    'user_id'         => $employee->id,
                    'admin_id'        => $session->admin_id,
                ];

                if ($hasSessionId) {
                    $presenceData['session_id'] = $session->id;
                } elseif ($hasSessionQrsId) {
                    $presenceData['session_qrs_id'] = $session->id;
                }

                Presence::create($presenceData);
            }
            
            $this->command->info("Seeded balanced tracking logs for user: {$employee->nom} ({$employee->email})");
        }

        $this->command->info("Data distribution successfully populated for analytical evaluation!");
    }
}