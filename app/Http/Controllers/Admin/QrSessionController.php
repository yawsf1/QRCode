<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionQR;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Inertia\Inertia;

class QrSessionController extends Controller
{
    public function show()
    {
        $admin = auth()->user();

        $currentSession = SessionQR::where('admin_id', $admin->id)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$currentSession) {
            SessionQR::where('admin_id', $admin->id)->delete();
            
            // Safe manual creation to completely bypass Mass Assignment issues
            $currentSession = new SessionQR();
            $currentSession->admin_id = $admin->id;
            $currentSession->token = Str::random(40);
            $currentSession->expires_at = Carbon::now()->addSeconds(15);
            $currentSession->save();
        }

        return Inertia::render('Admin/QrDashboard', [
            'qrToken' => $currentSession->token,
            'expiresAt' => $currentSession->expires_at->toIso8601String()
        ]);
    }

    public function refresh()
    {
        $admin = auth()->user();

        SessionQR::where('admin_id', $admin->id)->delete();

        // Safe manual creation
        $newSession = new SessionQR();
        $newSession->admin_id = $admin->id;
        $newSession->token = Str::random(40);
        $newSession->expires_at = Carbon::now()->addSeconds(15);
        $newSession->save();

        return redirect()->back();
    }
}