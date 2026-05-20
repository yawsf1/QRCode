<?php

namespace App\Services;

use App\Models\Presence;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmployeDashboard
{
    public function employeDashboard()
    {
        $employee = Auth::user();
        $employeeId = $employee->id;

        $cachedData = AppCache::remember(
            CacheKeys::employeDashboard($employeeId),
            now()->addMinutes(15),
            fn () => $this->buildDashboardData($employeeId)
        );

        return Inertia::render('Employe/Dashboard', [
            'history'        => $cachedData['history'],
            'checkedInToday' => $employee->hasCheckedInToday(),
            'stats'          => $cachedData['stats'],
            'chartData'      => $cachedData['chartData'],
            'pieLabels'      => ["À l'heure", 'En avance', 'En retard', 'Absent'],
            'pieCounts'      => [
                $cachedData['stats']['ontime_count'],
                $cachedData['stats']['early_count'],
                $cachedData['stats']['late_days'],
                $cachedData['stats']['absent_days'],
            ],
        ]);
    }

    private function buildDashboardData(int $employeeId): array
    {
        $history = Presence::where('user_id', $employeeId)
            ->orderBy('date_heure_scan', 'desc')
            ->take(7)
            ->get()
            ->map(function ($presence) {
                return [
                    'id' => $presence->id,
                    'statut' => $presence->statut,
                    'date' => Carbon::parse($presence->date_heure_scan)->translatedFormat('d F Y'),
                    'heure' => $presence->statut === 'absent'
                        ? '--:--:--'
                        : Carbon::parse($presence->date_heure_scan)->format('H:i:s'),
                    'ecart' => (int) $presence->ecart_minutes,
                ];
            });

        $totalPresences = Presence::where('user_id', $employeeId)->count();
        $ontimeCount = Presence::where('user_id', $employeeId)->where('statut', 'a_lheure')->count();
        $lateCount = Presence::where('user_id', $employeeId)->where('statut', 'en_retard')->count();
        $earlyCount = Presence::where('user_id', $employeeId)->where('statut', 'en_avance')->count();
        $absentCount = Presence::where('user_id', $employeeId)->where('statut', 'absent')->count();

        $presentDays = $totalPresences - $absentCount;
        $onTimeOrEarly = $ontimeCount + $earlyCount;
        $punctualityRate = $presentDays > 0
            ? round(($onTimeOrEarly / $presentDays) * 100, 1)
            : 100;

        $daysOfWeek = collect(range(6, 0))->map(
            fn ($i) => Carbon::now()->subDays($i)->format('Y-m-d')
        );

        $weekPresences = Presence::where('user_id', $employeeId)
            ->where('date_heure_scan', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->get(['date_heure_scan', 'ecart_minutes', 'statut']);

        $chartData = [
            'labels' => $daysOfWeek->map(
                fn ($d) => Carbon::parse($d)->translatedFormat('D d M')
            )->toArray(),
            'delays' => $daysOfWeek->map(function ($d) use ($weekPresences) {
                $dayRows = $weekPresences->filter(
                    fn ($p) => Carbon::parse($p->date_heure_scan)->format('Y-m-d') === $d
                        && $p->statut === 'en_retard'
                );

                if ($dayRows->isEmpty()) {
                    return 0;
                }

                return (int) round($dayRows->avg('ecart_minutes'));
            })->toArray(),
        ];

        return [
            'history' => $history->toArray(),
            'stats' => [
                'total_days' => $totalPresences,
                'punctuality_rate' => $punctualityRate . '%',
                'late_days' => $lateCount,
                'absent_days' => $absentCount,
                'ontime_count' => $ontimeCount,
                'early_count' => $earlyCount,
            ],
            'chartData' => $chartData,
        ];
    }
}
