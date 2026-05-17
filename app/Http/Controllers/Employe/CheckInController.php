<?php

namespace App\Http\Controllers\Employe;

use App\Http\Controllers\Controller;
use App\Models\SessionQr;
use App\Models\Presence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    /**
     * Process the scanned token submission directly in the Inertia loop.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $employee = auth()->user();

        // 1. Validate Token Integrity & Expiration window
        $qrSession = SessionQr::where('token', $request->token)->first();

        if (!$qrSession || Carbon::now()->greaterThan($qrSession->expires_at)) {
            return redirect()->back()->with([
                'flash_status' => 'error',
                'flash_message' => 'Le code QR scanné a expiré ou est invalide.'
            ]);
        }

        // 2. Prevent double clocking operations inside the same working day
        // Fixed: Use date_heure_scan instead of created_at to check correctly
        $alreadyScanned = Presence::where('user_id', $employee->id)
            ->whereDate('date_heure_scan', Carbon::today())
            ->exists();

        if ($alreadyScanned) {
            return redirect()->back()->with([
                'flash_status' => 'warning',
                'flash_message' => 'Vous avez déjà validé votre présence aujourd\'hui.'
            ]);
        }

        // 3. Load the Admin relationship rule configurations
        $admin = User::with('horaire')->find($qrSession->admin_id);
        $schedule = $admin->horaire;

        // Establish math fallbacks if configurations don't exist
        $targetStartTime = $schedule ? Carbon::parse($schedule->heure_debut) : Carbon::parse('09:00:00');
        $toleranceMinutes = $schedule ? (int)$schedule->tolerance_retard : 15;

        // 4. Time Precision Calculations
        $now = Carbon::now();
        $currentTime = Carbon::parse($now->toTimeString());
        $deadlineTime = $targetStartTime->copy()->addMinutes($toleranceMinutes);

        // Calculate minute differential variations safely
        // Absolute difference between current time and target start time
        $minuteDifference = (int) $currentTime->diffInMinutes($targetStartTime, false);

        // Classify standard status matching rules using your database Enum definitions
        if ($currentTime->lessThanOrEqualTo($targetStartTime)) {
            $status = 'a_lheure'; 
            $message = 'Pointage validé. Vous êtes à l\'heure !';
            $ecart = 0;
        } elseif ($currentTime->greaterThan($targetStartTime) && $currentTime->lessThanOrEqualTo($deadlineTime)) {
            $status = 'a_lheure'; // Protected inside company flexibility grace guidelines
            $message = 'Pointage validé (Pris en compte dans la marge de tolérance).';
            $ecart = abs($minuteDifference);
        } else {
            $status = 'en_retard';
            $message = 'Pointage enregistré avec retard.';
            $ecart = abs($minuteDifference);
        }

        // 5. Commit record straight into the analytical presences table tracking pipe
        // Fixed: Mapped all required columns specified in your migration script
        Presence::create([
            'date_heure_scan' => $now,
            'statut'          => $status,
            'ecart_minutes'   => $ecart,
            'adresse_ip'      => $request->ip(), // Captures the login network origin footprints
            'user_id'         => $employee->id,
            'session_id'      => $qrSession->id, // Added to fix your migration constraints
            'admin_id'        => $qrSession->admin_id, // Added to fix your migration constraints
        ]);

        return redirect()->back()->with([
            'flash_status' => 'success',
            'flash_message' => $message
        ]);
    }
}