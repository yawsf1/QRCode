<?php

namespace App\Http\Controllers\Employe;

use App\Events\EmployeCheckedIn;
use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\SessionQR;
use App\Services\NotificationService;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $employee = auth()->user();

        if ($employee->hasCheckedInToday()) {
            return redirect()
                ->route('employe.dashboard')
                ->with('warning', 'Vous avez déjà validé votre présence aujourd\'hui.');
        }

        $qrSession = SessionQR::where('token', $request->token)->first();

        if (!$qrSession || Carbon::now()->greaterThan($qrSession->expires_at)) {
            return redirect()
                ->route('employe.scan.form')
                ->with('error', 'Le code QR scanné a expiré ou est invalide.');
        }

        $employee->load('horaire');
        $schedule = $employee->horaire;

        $targetStartTime = $schedule
            ? Carbon::parse($schedule->heure_debut)
            : Carbon::parse('09:00:00');
        $toleranceMinutes = $schedule ? (int) $schedule->tolerance_retard : 15;

        $now = Carbon::now();
        $currentTime = Carbon::parse($now->toTimeString());
        $deadlineTime = $targetStartTime->copy()->addMinutes($toleranceMinutes);
        $minuteDifference = (int) $currentTime->diffInMinutes($targetStartTime, false);

        if ($currentTime->lessThan($targetStartTime)) {
            $status = 'en_avance';
            $message = 'Pointage validé. Vous êtes en avance !';
            $ecart = abs($minuteDifference);
        } elseif ($currentTime->equalTo($targetStartTime)) {
            $status = 'a_lheure';
            $message = 'Pointage validé. Vous êtes pile à l\'heure !';
            $ecart = 0;
        } elseif ($currentTime->greaterThan($targetStartTime) && $currentTime->lessThanOrEqualTo($deadlineTime)) {
            $status = 'a_lheure';
            $message = 'Pointage validé (Pris en compte dans la marge de tolérance).';
            $ecart = abs($minuteDifference);
        } else {
            $status = 'en_retard';
            $message = 'Pointage enregistré avec retard.';
            $ecart = abs($minuteDifference);
        }

        $presence = Presence::create([
            'user_id'         => $employee->id,
            'statut'          => $status,
            'date_heure_scan' => $now,
            'ecart_minutes'   => $ecart,
            'session_id'      => $qrSession->id,
            'admin_id'        => $qrSession->admin_id,
            'adresse_ip'      => $request->ip(),
        ]);

        $cacheKey = CacheKeys::userScannedToday($employee->id, Carbon::today()->toDateString());
        AppCache::put($cacheKey, true, now()->addDay());
        AppCache::forget(CacheKeys::employeDashboard($employee->id));
        AppCache::forget(CacheKeys::adminDashboard($qrSession->admin_id));
        AppCache::forget(CacheKeys::superAdminDashboard());

        $this->notifications->notifyAdminOfCheckIn($presence);

        broadcast(new EmployeCheckedIn($presence));

        return redirect()
            ->route('employe.dashboard')
            ->with('success', $message);
    }
}
