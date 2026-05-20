<?php

namespace Tests\Feature;

use App\Support\AppCache;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Tests\TestCase;

class ScanAccessTest extends TestCase
{
    public function test_employe_cannot_open_scan_page_after_check_in(): void
    {
        $admin = $this->createAdmin();
        $employe = $this->createEmploye($admin);

        AppCache::put(
            CacheKeys::userScannedToday($employe->id, Carbon::today()->toDateString()),
            true,
            now()->addDay()
        );

        $this->actingAs($employe)
            ->get(route('employe.scan.form'))
            ->assertRedirect(route('employe.dashboard'));
    }
}
