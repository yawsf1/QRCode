<?php

namespace App\Http\Controllers\Routing;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Services\AdminDashboard;
use App\Services\SuperAdminDashboard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\User;

class RoutingController extends Controller
{
    protected $adminService;
    protected $superAdminService;
    public function __construct(AdminDashboard $adminService, SuperAdminDashboard $superAdminService)
    {
        $this->superAdminService = $superAdminService;
        $this->adminService = $adminService;
    }
    
    public function home(){
        return Inertia::render('Home');
    }
    public function login(){
        return Inertia::render('Auth/Login');
    }
    public function Admindashboard()
    {
        $admin = Auth::user();

        // 1. Core Scoping: Get only employees under this specific Admin
        $myEmployeesQuery = User::where('admin_id', $admin->id)->where('role', 'employe');
        $totalEmployees = $myEmployeesQuery->count();
        $employeeIds = $myEmployeesQuery->pluck('id')->toArray();

        // 2. Timeline Matrix Framework (Last 6 Months)
        $months = [];
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
            // Fetch chronological scans grouped by month and status
            $rawTimelineData = Presence::whereIn('user_id', $employeeIds)
                ->where('date_heure_scan', '>=', Carbon::now()->subMonths(5)->startOfMonth())
                ->select(
                    DB::raw("TO_CHAR(date_heure_scan, 'YYYY-MM') as year_month"),
                            'statut',
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('year_month', 'statut')
                ->get();

            // Map database counts into array index offsets matching chronological month names
            foreach ($rawTimelineData as $data) {
                $carbonDate = Carbon::parse($data->year_month . '-01');
                $index = -1;
                for ($j = 5; $j >= 0; $j--) {
                    if (Carbon::now()->subMonths($j)->format('Y-m') === $carbonDate->format('Y-m')) {
                        $index = 5 - $j;
                        break;
                    }
                }

                if ($index !== -1) {
                    if ($data->statut === 'a_lheure' || $data->statut === 'punctual') {
                        $chartData['punctuals'][$index] = (int)$data->count;
                    } elseif ($data->statut === 'en_retard' || $data->statut === 'late') {
                        $chartData['lates'][$index] = (int)$data->count;
                    } elseif ($data->statut === 'en_avance' || $data->statut === 'earlies') {
                        $chartData['earlies'][$index] = (int)$data->count;
                    } elseif ($data->statut === 'absent') {
                        $chartData['absents'][$index] = (int)$data->count;
                    }
                }
            }
        }

        // 3. High-End Analytical Strategic Metric Calculations
        $totalScans = Presence::whereIn('user_id', $employeeIds)->count();
        $absentScans = Presence::whereIn('user_id', $employeeIds)->where('statut', 'absent')->count();
        $lateScans = Presence::whereIn('user_id', $employeeIds)->where('statut', 'en_retard')->count();
        $onTimeOrEarlyScans = Presence::whereIn('user_id', $employeeIds)->whereIn('statut', ['a_lheure', 'en_avance'])->count();

        $presenceRate = $totalScans > 0 ? round((($totalScans - $absentScans) / $totalScans) * 100, 1) : 100;
        $punctualityRate = ($totalScans - $absentScans) > 0 ? round(($onTimeOrEarlyScans / ($totalScans - $absentScans)) * 100, 1) : 100;
        
        // Total historical late delay baseline calculation
        $totalLateMinutes = Presence::whereIn('user_id', $employeeIds)->where('statut', 'en_retard')->sum('ecart_minutes');
        $toleranceImpact = round($totalLateMinutes / 60, 1);

        // Dynamic calculated composite productivity KPI score
        $productivityScore = round(($presenceRate * 0.6) + ($punctualityRate * 0.4), 1);

        // 4. Leaderboard Profiling: Top 5 vs Worst 5 Team Profiles (Current Month Window)
        $currentMonthStart = Carbon::now()->startOfMonth();

        $topEmployees = User::where('admin_id', $admin->id)
            ->where('role', 'employe')
            ->withCount(['presences as punctual_count' => function ($query) use ($currentMonthStart) {
                $query->where('date_heure_scan', '>=', $currentMonthStart)
                    ->whereIn('statut', ['a_lheure', 'en_avance']);
            }])
            ->orderBy('punctual_count', 'desc')
            ->take(5)
            ->get()
            ->toArray();

        $worstEmployees = User::where('admin_id', $admin->id)
            ->where('role', 'employe')
            ->withCount(['presences as late_count' => function ($query) use ($currentMonthStart) {
                $query->where('date_heure_scan', '>=', $currentMonthStart)
                    ->where('statut', 'en_retard');
            }])
            ->withCount(['presences as absent_count' => function ($query) use ($currentMonthStart) {
                $query->where('date_heure_scan', '>=', $currentMonthStart)
                    ->where('statut', 'absent');
            }])
            ->get()
            // Filter out employees with zero issues to clean up dashboard presentation
            ->filter(fn($emp) => $emp->late_count > 0 || $emp->absent_count > 0)
            ->sortByDesc(fn($emp) => $emp->late_count + ($emp->absent_count * 2))
            ->take(5)
            ->values()
            ->toArray();

        return Inertia::render('Admin/Dashboard', [
            'totalEmployees'  => $totalEmployees,
            'months'          => $months,
            'chartData'       => $chartData,
            'companyMetrics'  => [
                'presenceRate'     => $presenceRate,
                'toleranceImpact'  => $toleranceImpact,
                'punctualityRate'  => $punctualityRate,
                'productivityScore'=> $productivityScore,
            ],
            'topEmployees'    => $topEmployees,
            'worstEmployees'  => $worstEmployees,
        ]);
    }
    public function EmployeRegister(){
        return Inertia::render('Admin/EmployeRegister');
    }
    public function AdminRegister(){
        return Inertia::render('SuperAdmin/AdminRegister');
    }
    public function editEmploye($id){
        return $this->adminService->editEmploye($id);
    }

    public function listEmploye(Request $request){
        return $this->adminService->listEmploye($request);
    }
    public function SuperAdmindashboard()
    {
        return $this->superAdminService->SuperAdmindashboard();
    }
    public function listAdmin(Request $request)
    {
        return $this->superAdminService->listAdmin($request);
    }
    public function showEmploye($id)
    {
        return $this->adminService->showEmploye($id);
    }

    public function Employedashboard()
{
    $employee = Auth::user();
    $now = Carbon::now();

    $history = Presence::where('user_id', $employee->id)
        ->orderBy('date_heure_scan', 'desc')
        ->take(7)
        ->get()
        ->map(function ($presence) {
            return [
                'id' => $presence->id,
                'statut' => $presence->statut,
                'date' => Carbon::parse($presence->date_heure_scan)->translatedFormat('d F Y'),
                'heure' => $presence->statut === 'absent' ? '--:--:--' : Carbon::parse($presence->date_heure_scan)->format('H:i:s'),
                'ecart' => $presence->ecart_minutes,
            ];
        });

    $totalPresences = Presence::where('user_id', $employee->id)->count();
    $ontimeCount = Presence::where('user_id', $employee->id)->where('statut', 'a_lheure')->count();
    $lateCount = Presence::where('user_id', $employee->id)->where('statut', 'en_retard')->count();
    $earlyCount = Presence::where('user_id', $employee->id)->where('statut', 'en_avance')->count();
    $absentCount = Presence::where('user_id', $employee->id)->where('statut', 'absent')->count();
    
    $presentDays = $totalPresences - $absentCount;
    $onTimeOrEarly = $ontimeCount + $earlyCount;
    $punctualityRate = $presentDays > 0 ? round(($onTimeOrEarly / $presentDays) * 100, 1) : 100;

    $daysOfWeek = collect(range(6, 0))->map(function ($i) {
        return Carbon::now()->subDays($i)->format('Y-m-d');
    });

    $rawChartData = Presence::where('user_id', $employee->id)
        ->where('date_heure_scan', '>=', Carbon::now()->subDays(6)->startOfDay())
        ->select(
            DB::raw("DATE(date_heure_scan) as date"),
            DB::raw("COUNT(*) as aggregate"),
            DB::raw("AVG(ecart_minutes) as avg_delay")
        )
        ->groupBy('date')
        ->get()
        ->keyBy('date');

    $chartData = [
        'labels' => $daysOfWeek->map(fn($d) => Carbon::parse($d)->translatedFormat('D d M'))->toArray(),
        'delays' => $daysOfWeek->map(fn($d) => isset($rawChartData[$d]) ? round($rawChartData[$d]->avg_delay) : 0)->toArray()
    ];

    $checkedInToday = Presence::where('user_id', $employee->id)
        ->whereDate('date_heure_scan', Carbon::today())
        ->where('statut', '!=', 'absent')
        ->exists();

    return Inertia::render('Employe/Dashboard', [
        'history' => $history,
        'checkedInToday' => $checkedInToday,
        'stats' => [
            'total_days' => $totalPresences,
            'punctuality_rate' => $punctualityRate . '%',
            'late_days' => $lateCount,
        ],
        'chartData' => $chartData,
        'pieLabels' => ["À l'heure", "En avance", "En retard", "Absent"],
        'pieCounts' => [$ontimeCount, $earlyCount, $lateCount, $absentCount]
    ]);
}
}