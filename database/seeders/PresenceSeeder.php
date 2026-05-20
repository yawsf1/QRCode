<?php
namespace Database\Seeders;
use App\Events\EmployeCheckedIn;
use App\Models\Presence;
use App\Models\SessionQR;
use App\Models\User;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
class PresenceSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::where('email', 'k8e6ddYdd@example.com')->first();
        if (!$manager) {
            $this->command->error('Manager with ID 2 not found. Please ensure your AdminSeeder has created this manager first.');
            return;
        }
        $employee = User::where('admin_id', $manager->id)->first();
        if (!$employee) {
            $this->command->error("No employees found assigned to Manager ID {$manager->id}. Run your EmployeeSeeder first.");
            return;
        }
        $session = SessionQR::firstOrCreate(
            ['admin_id' => $manager->id],
            [
                'token' => uniqid('test_'),
                'est_actif' => true,
                'expires_at' => now()->addDays(30),
            ]
        );
        $mockPresences = [
            [
                'days_ago' => 0,
                'time' => Carbon::now()->toTimeString(),
                'statut' => 'en_retard',
                'ecart' => 10,
            ],
        ];
        $presenceTable = (new Presence())->getTable();
        $sessionColumn = Schema::hasColumn($presenceTable, 'session_id')
            ? 'session_id'
            : (Schema::hasColumn($presenceTable, 'session_qrs_id')
                ? 'session_qrs_id'
                : null);
        Presence::where('user_id', $employee->id)
            ->whereDate('date_heure_scan', Carbon::today())
            ->delete();
        foreach ($mockPresences as $presence) {
            $scanDate = Carbon::now()
                ->subDays($presence['days_ago'])
                ->setTimeFromTimeString($presence['time']);
            $data = [
                'date_heure_scan' => $scanDate,
                'statut' => $presence['statut'],
                'ecart_minutes' => $presence['ecart'],
                'adresse_ip' => '127.0.0.1',
                'user_id' => $employee->id,
                'admin_id' => $manager->id,
            ];
            if ($sessionColumn) {
                $data[$sessionColumn] = $session->id;
            }
            $newPresence = Presence::create($data);
            AppCache::forget(CacheKeys::employeDashboard($employee->id));
            AppCache::forget(CacheKeys::adminDashboard($manager->id));
            broadcast(new EmployeCheckedIn($newPresence));
        }
        $this->command->info("Success! Real-time presence broadcasted for employee '{$employee->prenom} {$employee->nom}' under Manager '{$manager->prenom} {$manager->nom}' (ID: 2).");
    }
}
