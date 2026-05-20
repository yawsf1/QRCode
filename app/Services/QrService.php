<?php

namespace App\Services;

use App\Models\SessionQR;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QrService
{
    public function getOrCreateActiveSession(int $adminId): SessionQR
    {
        $current = SessionQR::query()
            ->where('admin_id', $adminId)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($current) {
            return $current;
        }

        return DB::transaction(function () use ($adminId) {
            SessionQR::query()
                ->where('admin_id', $adminId)
                ->lockForUpdate()
                ->delete();

            return SessionQR::create([
                'admin_id' => $adminId,
                'token' => Str::random(40),
                'expires_at' => Carbon::now()->addSeconds(15),
            ]);
        });
    }

    public function refreshSession(int $adminId): SessionQR
    {
        AppCache::forget(CacheKeys::qrSession($adminId));

        return DB::transaction(function () use ($adminId) {
            SessionQR::query()
                ->where('admin_id', $adminId)
                ->lockForUpdate()
                ->delete();

            return SessionQR::create([
                'admin_id' => $adminId,
                'token' => Str::random(40),
                'expires_at' => Carbon::now()->addSeconds(15),
            ]);
        });
    }
}
