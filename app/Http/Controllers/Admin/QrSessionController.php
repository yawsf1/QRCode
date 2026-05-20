<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\QrService;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Inertia\Inertia;
class QrSessionController extends Controller
{
    public function __construct(protected QrService $qrService) {}
    public function show()
    {
        $admin = auth()->user();
        $currentSession = $this->qrService->getOrCreateActiveSession($admin->id);
        return Inertia::render('Admin/QrDashboard', [
            'qrToken' => $currentSession->token,
            'expiresAt' => $currentSession->expires_at->toIso8601String(),
        ]);
    }
    public function refresh()
    {
        $admin = auth()->user();
        AppCache::forget(CacheKeys::qrSession($admin->id));
        $this->qrService->refreshSession($admin->id);
        return redirect()->back();
    }
}
