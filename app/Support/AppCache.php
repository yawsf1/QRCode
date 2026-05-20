<?php
namespace App\Support;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
class AppCache
{
    public static function store(): Repository
    {
        $store = config('cache.app_store', config('cache.default'));
        return Cache::store($store);
    }
    public static function remember(string $key, $ttl, callable $callback): mixed
    {
        return static::store()->remember($key, $ttl, $callback);
    }
    public static function forget(string $key): bool
    {
        return static::store()->forget($key);
    }
    public static function has(string $key): bool
    {
        return static::store()->has($key);
    }
    public static function put(string $key, mixed $value, $ttl = null): bool
    {
        return static::store()->put($key, $value, $ttl);
    }
}
