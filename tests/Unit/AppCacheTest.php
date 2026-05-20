<?php
namespace Tests\Unit;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Tests\TestCase;
class AppCacheTest extends TestCase
{
    public function test_cache_keys_are_stable(): void
    {
        $this->assertSame('admin_dashboard_stats_5', CacheKeys::adminDashboard(5));
        $this->assertSame('employe_dashboard_stats_12', CacheKeys::employeDashboard(12));
        $this->assertSame('super_admin_dashboard_stats', CacheKeys::superAdminDashboard());
        $this->assertSame('user_scanned_3_2026-05-20', CacheKeys::userScannedToday(3, '2026-05-20'));
    }
    public function test_app_cache_remembers_and_forgets_values(): void
    {
        $key = 'test_remember_key';
        AppCache::forget($key);
        $value = AppCache::remember($key, 60, fn () => 'cached-value');
        $this->assertSame('cached-value', $value);
        $this->assertTrue(AppCache::has($key));
        AppCache::forget($key);
        $this->assertFalse(AppCache::has($key));
    }
}
