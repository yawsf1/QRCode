<?php
namespace App\Services;
use App\Models\Presence;
use App\Models\User;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class AdminDashboard
{
    public function showEmploye($id)
    {
        $employe = User::where('role', 'employe')
            ->where('admin_id', Auth::id())
            ->with(['horaire', 'presences' => function ($query) {
                $query->latest();
            }])
            ->findOrFail($id);
        $stats = [
            'total_scans' => $employe->presences->count(),
            'on_time'     => $employe->presences->where('statut', 'a_lheure')->count(),
            'late'        => $employe->presences->where('statut', 'en_retard')->count(),
            'early'       => $employe->presences->where('statut', 'en_avance')->count(),
            'absent'      => $employe->presences->where('statut', 'absent')->count(),
        ];
        return Inertia::render('Admin/Employe', [
            'employe' => $employe,
            'stats'   => $stats,
        ]);
    }
    public function listEmploye(Request $request)
    {
        $employes = User::query()
            ->where('role', 'employe')
            ->where('admin_id', Auth::id())
            ->with('horaire')
            ->filter($request->only(['search', 'status']))
            ->sortBy($request->sortBy)
            ->paginate(4)
            ->withQueryString();
        return Inertia::render('Admin/ListEmploye', [
            'employes' => $employes,
        ]);
    }
    public function editEmploye($id)
    {
        AppCache::forget(CacheKeys::adminDashboard(Auth::id()));
        $employe = User::where('role', 'employe')
            ->where('admin_id', Auth::id())
            ->with('horaire')
            ->findOrFail($id);
        return Inertia::render('Admin/EmployeUpdate', [
            'employe' => $employe,
        ]);
    }
    public function adminDashboard()
    {
        $admin   = Auth::user();
        $adminId = $admin->id;
        $data = AppCache::remember(
            CacheKeys::adminDashboard($adminId),
            now()->addHours(24),
            fn () => $this->buildDashboardData($adminId)
        );
        return Inertia::render('Admin/Dashboard', $data);
    }
    private function buildDashboardData(int $adminId): array
    {
        $myEmployeesQuery = User::where('admin_id', $adminId)->where('role', 'employe');
        $totalEmployees   = $myEmployeesQuery->count();
        $employeeIds      = $myEmployeesQuery->pluck('id')->toArray();
        $months    = [];
        $chartData = [
            'punctuals' => [0, 0, 0, 0, 0, 0],
            'lates'     => [0, 0, 0, 0, 0, 0],
            'earlies'   => [0, 0, 0, 0, 0, 0],
            'absents'   => [0, 0, 0, 0, 0, 0],
        ];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->translatedFormat('M Y');
        }
        if (!empty($employeeIds)) {
            $presences = Presence::whereIn('user_id', $employeeIds)
                ->where('date_heure_scan', '>=', Carbon::now()->subMonths(5)->startOfMonth())
                ->get(['date_heure_scan', 'statut']);
            foreach ($presences as $presence) {
                $scanMonth = Carbon::parse($presence->date_heure_scan)->format('Y-m');
                $index = -1;
                for ($j = 5; $j >= 0; $j--) {
                    if (Carbon::now()->subMonths($j)->format('Y-m') === $scanMonth) {
                        $index = 5 - $j;
                        break;
                    }
                }
                if ($index === -1) {
                    continue;
                }
                match ($presence->statut) {
                    'a_lheure'  => $chartData['punctuals'][$index]++,
                    'en_retard' => $chartData['lates'][$index]++,
                    'en_avance' => $chartData['earlies'][$index]++,
                    'absent'    => $chartData['absents'][$index]++,
                    default => null,
                };
            }
        }
        $totalScans         = Presence::whereIn('user_id', $employeeIds)->count();
        $absentScans        = Presence::whereIn('user_id', $employeeIds)->where('statut', 'absent')->count();
        $onTimeOrEarlyScans = Presence::whereIn('user_id', $employeeIds)
            ->whereIn('statut', ['a_lheure', 'en_avance'])->count();
        $presenceRate = $totalScans > 0
            ? round((($totalScans - $absentScans) / $totalScans) * 100, 1)
            : 100;
        $punctualityRate = ($totalScans - $absentScans) > 0
            ? round(($onTimeOrEarlyScans / ($totalScans - $absentScans)) * 100, 1)
            : 100;
        $totalLateMinutes = Presence::whereIn('user_id', $employeeIds)
            ->where('statut', 'en_retard')->sum('ecart_minutes');
        $toleranceImpact   = round($totalLateMinutes / 60, 1);
        $productivityScore = round(($presenceRate * 0.6) + ($punctualityRate * 0.4), 1);
        $currentMonthStart = Carbon::now()->startOfMonth();
        $topEmployees = User::where('admin_id', $adminId)
            ->where('role', 'employe')
            ->withCount(['presences as punctual_count' => function ($q) use ($currentMonthStart) {
                $q->where('date_heure_scan', '>=', $currentMonthStart)
                  ->whereIn('statut', ['a_lheure', 'en_avance']);
            }])
            ->orderBy('punctual_count', 'desc')
            ->take(5)
            ->get()
            ->toArray();
        $worstEmployees = User::where('admin_id', $adminId)
            ->where('role', 'employe')
            ->withCount(['presences as late_count' => function ($q) use ($currentMonthStart) {
                $q->where('date_heure_scan', '>=', $currentMonthStart)
                  ->where('statut', 'en_retard');
            }])
            ->withCount(['presences as absent_count' => function ($q) use ($currentMonthStart) {
                $q->where('date_heure_scan', '>=', $currentMonthStart)
                  ->where('statut', 'absent');
            }])
            ->get()
            ->filter(fn ($emp) => $emp->late_count > 0 || $emp->absent_count > 0)
            ->sortByDesc(fn ($emp) => $emp->late_count + ($emp->absent_count * 2))
            ->take(5)
            ->values()
            ->toArray();
        return [
            'totalEmployees' => $totalEmployees,
            'months'         => $months,
            'chartData'      => $chartData,
            'companyMetrics' => [
                'presenceRate'      => $presenceRate,
                'toleranceImpact'   => $toleranceImpact,
                'punctualityRate'   => $punctualityRate,
                'productivityScore' => $productivityScore,
            ],
            'topEmployees'   => $topEmployees,
            'worstEmployees' => $worstEmployees,
        ];
    }
}
