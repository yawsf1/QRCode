<?php
namespace App\Services;
use App\Models\User;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Illuminate\Http\Request;
use Inertia\Inertia;
class SuperAdminDashboard
{
    private const MONTHS = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];
    public function superAdminDashboard()
    {
        $data = AppCache::remember(
            CacheKeys::superAdminDashboard(),
            now()->addHours(6),
            fn () => $this->buildDashboardData()
        );
        return Inertia::render('SuperAdmin/Dashboard', $data);
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
    public function buildDashboardData(): array
    {
        $year = (int) date('Y');
        $adminsByMonth = User::where('role', 'admin')
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(fn ($user) => $user->created_at->format('F'))
            ->map(fn ($group) => $group->count());
        $employesByMonth = User::where('role', 'employe')
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(fn ($user) => $user->created_at->format('F'))
            ->map(fn ($group) => $group->count());
        $salesData = [];
        $salesData2 = [];
        foreach (self::MONTHS as $month) {
            $salesData[] = $adminsByMonth->get($month, 0);
            $salesData2[] = $employesByMonth->get($month, 0);
        }
        $entreprises = User::where('role', 'admin')
            ->whereNotNull('departement')
            ->select('id', 'departement')
            ->withCount('employes')
            ->orderBy('employes_count', 'desc')
            ->limit(5)
            ->get();
        return [
            'months' => self::MONTHS,
            'salesData' => $salesData,
            'salesData2' => $salesData2,
            'entrepriseNames' => $entreprises->pluck('departement')->toArray(),
            'employeeCounts' => $entreprises->pluck('employes_count')->map(fn ($val) => (int) $val)->toArray(),
            'currentMonthIndex' => (int) date('n') - 1,
        ];
    }
}
