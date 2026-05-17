<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SuperAdminDashboard
{
        public function SuperAdmindashboard()
    {
        $adminsByMonth = User::where('role', 'admin')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('COUNT(id) as total'),
                DB::raw("TRIM(TO_CHAR(created_at, 'Month')) as month") 
            )
            ->groupBy(DB::raw("TRIM(TO_CHAR(created_at, 'Month'))"))
            ->pluck('total', 'month');

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $employesByMonth = User::where('role', 'employe')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('COUNT(id) as total'),
                DB::raw("TRIM(TO_CHAR(created_at, 'Month')) as month")
            )
            ->groupBy(DB::raw("TRIM(TO_CHAR(created_at, 'Month'))"))
            ->pluck('total', 'month');

        $salesData = [];
        foreach ($months as $month) {
            $salesData[] = $adminsByMonth->get($month, 0);
        }

        $salesData2 = [];
        foreach ($months as $month) {
            $salesData2[] = $employesByMonth->get($month, 0);
        }

        $entreprises = User::where('role', 'admin')
            ->whereNotNull('departement')
            ->select('id', 'departement')
            ->withCount('employes')
            ->orderBy('employes_count', 'desc')
            ->limit(5)
            ->get();
                
        $entrepriseNames = $entreprises->pluck('departement')->toArray();
        $employeeCounts = $entreprises->pluck('employes_count')->map(fn($val) => (int)$val)->toArray();

        return Inertia::render('SuperAdmin/Dashboard', [
            'months' => $months,
            'salesData' => $salesData,
            'salesData2' => $salesData2,
            'entrepriseNames' => $entrepriseNames,
            'employeeCounts' => $employeeCounts
        ]);
    }


    public function listAdmin(Request $request)
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->withCount('employes')
            ->filter($request->only(['search', 'status']))
            ->sortBy($request->sortBy)
            ->paginate(4)
            ->withQueryString();

        return Inertia::render('SuperAdmin/ListAdmin', [
            'admins' => $admins,
        ]);
    }



    public function AdminDashboard()
{
    $admin = auth()->user();
    
    $totalEmployees = User::where('role', 'employe')
        ->where('admin_id', $admin->id)
        ->count();

    $months = [
        'January', 'February', 'March', 'April', 'May', 'June', 
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    $presenceData = DB::table('presences')
        ->join('users', 'presences.user_id', '=', 'users.id')
        ->where('users.admin_id', $admin->id)
        ->whereYear('presences.created_at', date('Y'))
        ->select(
            DB::raw("TRIM(TO_CHAR(presences.created_at, 'Month')) as month"),
            DB::raw("COUNT(CASE WHEN presences.statut = 'absent' THEN 1 END) as absents"),
            DB::raw("COUNT(CASE WHEN presences.statut = 'late' THEN 1 END) as lates"),
            DB::raw("COUNT(CASE WHEN presences.statut = 'early' THEN 1 END) as earlies"),
            DB::raw("COUNT(CASE WHEN presences.statut = 'punctual' THEN 1 END) as punctuals")
        )
        ->groupBy(DB::raw("TRIM(TO_CHAR(presences.created_at, 'Month'))"))
        ->get()
        ->keyBy('month');

    $chartAbsents = []; $chartLates = []; $chartEarlies = []; $chartPunctuals = [];
    foreach ($months as $month) {
        $monthData = $presenceData->get($month);
        $chartAbsents[]   = $monthData ? (int)$monthData->absents : 0;
        $chartLates[]     = $monthData ? (int)$monthData->lates : 0;
        $chartEarlies[]   = $monthData ? (int)$monthData->earlies : 0;
        $chartPunctuals[] = $monthData ? (int)$monthData->punctuals : 0;
    }

    $totalPresences = array_sum($chartPunctuals) + array_sum($chartLates) + array_sum($chartEarlies);
    $totalLates = array_sum($chartLates);
    $totalAbsences = array_sum($chartAbsents);

    $toleranceMinutes = $admin->horaire->tolerance_retard ?? 15;
    $scheduledDays = $totalEmployees * 20; 

    $presenceRate = $scheduledDays > 0 ? max(0, 1 - ($totalAbsences / $scheduledDays)) : 0;
    $latenessRatio = $totalPresences > 0 ? ($totalLates / $totalPresences) : 0;
    $toleranceImpact = $toleranceMinutes / 60;
    $punctualityRate = max(0, 1 - ($latenessRatio * $toleranceImpact));
    $productivityScore = $presenceRate * $punctualityRate * 100;

    // 4. Top 5 Best & Worst Employees of the Current Month
    $employeeLeaderboard = User::where('role', 'employe')
        ->where('admin_id', $admin->id)
        ->withCount([
            'presences as punctual_count' => fn($q) => $q->where('statut', 'punctual')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')),
            'presences as late_count' => fn($q) => $q->where('statut', 'late')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y')),
            'presences as absent_count' => fn($q) => $q->where('statut', 'absent')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
        ])
        ->get();

    $topEmployees = $employeeLeaderboard
        ->sortByDesc('punctual_count')
        ->take(5)
        ->values();

    $worstEmployees = $employeeLeaderboard
        ->sortByDesc(fn($emp) => $emp->late_count + ($emp->absent_count * 2))
        ->take(5)
        ->values();

    return Inertia::render('Admin/Dashboard', [
        'totalEmployees' => $totalEmployees,
        'months' => $months,
        'chartData' => [
            'absents'   => $chartAbsents,
            'lates'     => $chartLates,
            'earlies'   => $chartEarlies,
            'punctuals' => $chartPunctuals,
        ],
        'companyMetrics' => [
            'presenceRate'      => round($presenceRate * 100, 1),
            'toleranceImpact'   => round($toleranceImpact, 2),
            'punctualityRate'   => round($punctualityRate * 100, 1),
            'productivityScore' => round($productivityScore, 1)
        ],
        'topEmployees'   => $topEmployees,
        'worstEmployees' => $worstEmployees
    ]);
}
}
